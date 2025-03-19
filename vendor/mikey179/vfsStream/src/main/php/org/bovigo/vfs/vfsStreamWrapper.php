<?php
/**
 * This file is part of vfsStream.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  org\bovigo\vfs
 */
namespace org\bovigo\vfs;
/**
 * Stream wrapper to mock file system requests.
 */
class vfsStreamWrapper
{
    /**
     * open file for reading
     */
    const READ                   = 'r';
    /**
     * truncate file
     */
    const TRUNCATE               = 'w';
    /**
     * set file pointer to end, append new data
     */
    const APPEND                 = 'a';
    /**
     * set file pointer to start, overwrite existing data
     */
    const WRITE                  = 'x';
    /**
     * set file pointer to start, overwrite existing data; or create file if
     * does not exist
     */
    const WRITE_NEW              = 'c';
    /**
     * file mode: read only
     */
    const READONLY               = 0;
    /**
     * file mode: write only
     */
    const WRITEONLY              = 1;
    /**
     * file mode: read and write
     */
    const ALL                    = 2;
    /**
     * switch whether class has already been registered as stream wrapper or not
     *
     * @type  bool
     */
    protected static $registered = false;
    /**
     * root content
     *
     * @type  vfsStreamContent
     */
    protected static $root;
    /**
     * disk space quota
     *
     * @type  Quota
     */
    private static $quota;
    /**
     * file mode: read only, write only, all
     *
     * @type  int
     */
    protected $mode;
    /**
     * shortcut to file container
     *
     * @type  vfsStreamFile
     */
    protected $content;
    /**
     * shortcut to directory container
     *
     * @type  vfsStreamDirectory
     */
    protected $dir;
    /**
     * shortcut to directory container iterator
     *
     * @type  vfsStreamDirectory
     */
    protected $dirIterator;

    /**
     * method to register the stream wrapper
     *
     * Please be aware that a call to this method will reset the root element
     * to null.
     * If the stream is already registered the method returns silently. If there
     * is already another stream wrapper registered for the scheme used by
     * vfsStream a vfsStreamException will be thrown.
     *
     * @throws  vfsStreamException
     */
    public static function register()
    {
        self::$root  = null;
        self::$quota = Quota::unlimited();
        if (true === self::$registered) {
            return;
        }

        if (@stream_wrapper_register(vfsStream::SCHEME, __CLASS__) === false) {
            throw new vfsStreamException('A handler has already been registered for the ' . vfsStream::SCHEME . ' protocol.');
        }

        self::$registered = true;
    }

    /**
     * sets the root content
     *
     * @param   vfsStreamContainer  $root
     * @return  vfsStreamContainer
     */
    public static function setRoot(vfsStreamContainer $root)
    {
        self::$root = $root;
        return self::$root;
    }

    /**
     * returns the root content
     *
     * @return  vfsStreamContainer
     */
    public static function getRoot()
    {
        return self::$root;
    }

    /**
     * sets quota for disk space
     *
     * @param  Quota  $quota
     * @since  1.1.0
     */
    public static function setQuota(Quota $quota)
    {
        self::$quota = $quota;
    }

    /**
     * returns content for given path
     *
     * @param   string  $path
     * @return  vfsStreamContent
     */
    protected function getContent($path)
    {
        if (null === self::$root) {
            return null;
        }

        if (self::$root->getName() === $path) {
            return self::$root;
        }

        if (self::$root->hasChild($path) === true) {
            return self::$root->getChild($path);
        }

        return null;
    }

    /**
     * returns content for given path but only when it is of given type
     *
     * @param   string  $path
     * @param   int     $type
     * @return  vfsStreamContent
     */
    protected function getContentOfType($path, $type)
    {
        $content = $this->getContent($path);
        if (null !== $content && $content->getType() === $type) {
            return $content;
        }

        return null;
    }

    /**
     * splits path into its dirname and the basename
     *
     * @param   string  $path
     * @return  string[]
     */
    protected function splitPath($path)
    {
        $lastSlashPos = strrpos($path, '/');
        if (false === $lastSlashPos) {
            return array('dirname' => '', 'basename' => $path);
        }

        return array('dirname'  => substr($path, 0, $lastSlashPos),
                     'basename' => substr($path, $lastSlashPos + 1)
               );
    }

    /**
     * helper method to resolve a path from /foo/bar/. to /foo/bar
     *
     * @param   string  $path
     * @return  string
     */
    protected function resolvePath($path)
    {
        $newPath  = array();
        foreach (explode('/', $path) as $pathPart) {
            if ('.' !== $pathPart) {
                if ('..' !== $pathPart) {
                    $newPath[] = $pathPart;
                } else {
                    array_pop($newPath);
                }
            }
        }

        return implode('/', $newPath);
    }

    /**
     * open the stream
     *
     * @param   string  $path         the path to open
     * @param   string  $mode         mode for opening
     * @param   string  $options      options for opening
     * @param   string  $opened_path  full path that was actually opened
     * @return  bool
     */
    public function stream_open($path, $mode, $options, $opened_path)
    {
        $extended = ((strstr($mode, '+') !== false) ? (true) : (false));
        $mode     = str_replace(array('b', '+'), '', $mode);
        if (in_array($mode, array('r', 'w', 'a', 'x', 'c')) === false) {
            if (($options & STREAM_REPORT_ERRORS) === STREAM_REPORT_ERRORS) {
                trigger_error('Illegal mode ' . $mode . ', use r, w, a, x  or c, flavoured with b and/or +', E_USER_WARNING);
            }

            return false;
        }

        $this->mode    = $this->calculateMode($mode, $extended);
        $path          = $this->resolvePath(vfsStream::path($path));
        $this->content = $this->getContentOfType($path, vfsStreamContent::TYPE_FILE);
        if (null !== $this->content) {
            if (self::WRITE === $mode) {
                if (($options & STREAM_REPORT_ERRORS) === STREAM_REPORT_ERRORS) {
                    trigger_error('File ' . $path . ' already exists, can not open with mode x', E_USER_WARNING);
                }

                return false;
            }

            if (
                (self::TRUNCATE === $mode || self::APPEND === $mode) &&
                $this->content->isWritable(vfsStream::getCurrentUser(), vfsStream::getCurrentGroup()) === false
            ) {
                return false;
            }

            if (self::TRUNCATE === $mode) {
                $this->content->openWithTruncate();
            } elseif (self::APPEND === $mode) {
                $this->content->openForAppend();
            } else {
                if (!$this->content->isReadable(vfsStream::getCurrentUser(), vfsStream::getCurrentGroup())) {
                    if (($options & STREAM_REPORT_ERRORS) === STREAM_REPORT_ERRORS) {
                        trigger_error('Permission denied', E_USER_WARNING);
                    }
                    return false;
                }
                $this->content->open();
            }

            return true;
        }

        $content = $this->createFile($path, $mode, $options);
        if (false === $content) {
            return false;
        }

        $this->content = $content;
        return true;
    }

    /**
     * creates a file at given path
     *
     * @param   string  $path     the path to open
     * @param   string  $mode     mode for opening
     * @param   string  $options  options for opening
     * @return  bool
     */
    private function createFile($path, $mode = null, $options = null)
    {
        $names = $this->splitPath($path);
        if (empty($names['dirname']) === true) {
            if (($options & STREAM_REPORT_ERRORS) === STREAM_REPORT_ERRORS) {
                trigger_error('File ' . $names['basename'] . ' does not exist', E_USER_WARNING);
            }

            return false;
        }

        $dir = $this->getContentOfType($names['dirname'], vfsStreamContent::TYPE_DIR);
        if (null === $dir) {
            if (($options & STREAM_REPORT_ERRORS) === STREAM_REPORT_ERRORS) {
                trigger_error('Directory ' . $names['dirname'] . ' does not exist', E_USER_WARNING);
            }

            return false;
        } elseif ($dir->hasChild($names['basename']) === true) {
            if (($options & STREAM_REPORT_ERRORS) === STREAM_REPORT_ERRORS) {
                trigger_error('Directory ' . $names['dirname'] . ' already contains a director named ' . $names['basename'], E_USER_WARNING);
            }

            return false;
        }

        if (self::READ === $mode) {
            if (($options & STREAM_REPORT_ERRORS) === STREAM_REPORT_ERRORS) {
                trigger_error('Can not open non-existing file ' . $path . ' for reading', E_USER_WARNING);
            }

            return false;
        }

        if ($dir->isWritable(vfsStream::getCurrentUser(), vfsStream::getCurrentGroup()) === false) {
            if (($options & STREAM_REPORT_ERRORS) === STREAM_REPORT_ERRORS) {
                trigger_error('Can not create new file in non-writable path ' . $names['dirname'], E_USER_WARNING);
            }

            return false;
        }

        return vfsStream::newFile($names['basename'])->at($dir);
    }

    /**
     * calculates the file mode
     *
     * @param   string  $mode      opening mode: r, w, a or x
     * @param   bool    $extended  true if + was set with opening mode
     * @return  int
     */
    protected function calculateMode($mode, $extended)
    {
        if (true === $extended) {
            return self::ALL;
        }

        if (self::READ === $mode) {
            return self::READONLY;
        }

        return self::WRITEONLY;
    }

    /**
     * closes the stream
     */
    public function stream_close()
    {
        $this->content->lock(LOCK_UN);
    }

    /**
     * read the stream up to $count bytes
     *
     * @param   int     $count  amount of bytes to read
     * @return  string
     */
    public function stream_read($count)
    {
        if (self::WRITEONLY === $this->mode) {
            return '';
        }

        if ($this->content->isReadable(vfsStream::getCurrentUser(), vfsStream::getCurrentGroup()) === false) {
            return '';
        }

        return $this->content->read($count);
    }

    /**
     * writes data into the stream
     *
     * @param   string  $data
     * @return  int     amount of bytes written
     */
    public function stream_write($data)
    {
        if (self::READONLY === $this->mode) {
            return 0;
        }

        if ($this->content->isWritable(vfsStream::getCurrentUser(), vfsStream::getCurrentGroup()) === false) {
            return 0;
        }

        if (self::$quota->isLimited()) {
            $data = substr($data, 0, self::$quota->spaceLeft(self::$root->sizeSummarized()));
        }

        return $this->content->write($data);
    }

    /**
     * truncates a file to a given length
     *
     * @param   int  $size  length to truncate file to
     * @return  bool
     * @since   1.1.0
     */
    public function stream_truncate($size)
    {
        if (self::READONLY === $this->mode) {
            return false;
        }

        if ($this->content->isWritable(vfsStream::getCurrentUser(), vfsStream::getCurrentGroup()) === false) {
            return false;
        }

        if ($this->content->getType() !== vfsStreamContent::TYPE_FILE) {
            return false;
        }

        if (self::$quota->isLimited() && $this->content->size() < $size) {
            $maxSize = self::$quota->spaceLeft(self::$root->sizeSummarized());
            if (0 === $maxSize) {
                return false;
            }

            if ($size > $maxSize) {
                $size = $maxSize;
            }
        }

        return $this->content->truncate($size);
    }

    /**
     * sets metadata like owner, user or permissions
     *
     * @param   string  $path
     * @param   int     $option
     * @param   mixed   $var
     * @return  bool
     * @since   1.1.0
     */
    public function stream_metadata($path, $option, $var)
    {
        $path    = $this->resolvePath(vfsStream::path($path));
        $content = $this->getContent($path);
        switch ($option) {
            case STREAM_META_TOUCH:
                if (null === $content) {
                    $content = $this->createFile($path);
                }

                if (isset($var[0])) {
                    $content->lastModified($var[0]);
                }

                if (isset($var[1])) {
                    $content->lastAccessed($var[1]);
                }

                return true;

            case STREAM_META_OWNER_NAME:
                return false;

            case STREAM_META_OWNER:
                if (null === $content) {
                    return false;
                }

                $content->chown($var);
                return true;

            case STREAM_META_GROUP_NAME:
                return false;

            case STREAM_META_GROUP:
                if (null === $content) {
                    return false;
                }

                $content->chgrp($var);
                return true;

            case STREAM_META_ACCESS:
                if (null === $content) {
                    return false;
                }

                $content->chmod($var);
                return true;

            default:
                return false;
        }
    }

    /**
     * checks whether stream is at end of file
     *
     * @return  bool
     */
    public function stream_eof()
    {
        return $this->content->eof();
    }

    /**
     * returns the current position of the stream
     *
     * @return  int
     */
    public function stream_tell()
    {
        return $this->content->getBytesRead();
    }

    /**
     * seeks to the given offset
     *
     * @param   int   $offset
     * @param   int   $whence
     * @return  bool
     */
    public function stream_seek($offset, $whence)
    {
        return $this->content->seek($offset, $whence);
    }

    /**
     * flushes unstored data into storage
     *
     * @return  bool
     */
    public function stream_flush()
    {
        return true;
    }

    /**
     * returns status of stream
     *
     * @return  array
     */
    public function stream_stat()
    {
        $fileStat = array('dev'     => 0,
                          'ino'     => 0,
                          'mode'    => $this->content->getType() | $this->content->getPermissions(),
                          'nlink'   => 0,
                          'uid'     => $this->content->getUser(),
                          'gid'     => $this->content->getGroup(),
                          'rdev'    => 0,
                          'size'    => $this->content->size(),
                          'atime'   => $this->content->fileatime(),
                          'mtime'   => $this->content->filemtime(),
                          'ctime'   => $this->content->filectime(),
                          'blksize' => -1,
                          'blocks'  => -1
                    );
        return array_merge(array_values($fileStat), $fileStat);
    }

    /**
     * retrieve the underlaying resource
     *
     * Please note that this method always returns false as there is no
     * underlaying resource to return.
     *
     * @param   int  $cast_as
     * @since   0.9.0
     * @see     https://github.com/mikey179/vfsStream/issues/3
     * @return  bool
     */
    public function stream_cast($cast_as)
    {
        return false;
    }

    /**
     * set lock status for stream
     *
     * @param   int   $operation
     * @return  bool
     * @since   0.10.0
     * @see     https://github.com/mikey179/vfsStream/issues/6
     * @see     https://github.com/mikey179/vfsStream/issues/31
     */
    public function stream_lock($operation)
    {
        if ((LOCK_NB & $operation) == LOCK_NB) {
            $operation = $operation - LOCK_NB;
        }

        if (LOCK_EX === $operation && $this->content->isLocked()) {
            return false;
        } elseif (LOCK_SH === $operation && $this->content->hasExclusiveLock()) {
            return false;
        }

        $this->content->lock($operation);
        return true;
    }

    /**
     * sets options on the stream
     *
     * @param   int   $option  key of option to set
     * @param   int   $arg1
     * @param   int   $arg2
     * @return  bool
     * @since   0.10.0
     * @see     https://github.com/mikey179/vfsStream/issues/15
     * @see     http://www.php.net/manual/streamwrapper.stream-set-option.php
     */
    public function stream_set_option($option, $arg1, $arg2)
    {
        switch ($option) {
            case STREAM_OPTION_BLOCKING:
                // break omitted

            case STREAM_OPTION_READ_TIMEOUT:
                // break omitted

            case STREAM_OPTION_WRITE_BUFFER:
                // break omitted

            default:
                // nothing to do here
        }

        return false;
    }

    /**
     * remove the data under the given path
     *
     * @param   string  $path
     * @return  bool
     */
    public function unlink($path)
    {
        $realPath = $this->resolvePath(vfsStream::path($path));
        $content  = $this->getContent($realPath);
        if (null === $content || $content->isWritable(vfsStream::getCurrentUser(), vfsStream::getCurrentGroup()) === false) {
            return false;
        }

        if ($content->getType() !== vfsStreamContent::TYPE_FILE) {
            trigger_error('unlink(' . $path . '): Operation not permitted', E_USER_WARNING);
            return false;
        }

        return $this->doUnlink($realPath);
    }

    /**
     * removes a path
     *
     * @param   string  $path
     * @return  bool
     */
    protected function doUnlink($path)
    {
        if (self::$root->getName() === $path) {
            // delete root? very brave. :)
            self::$root = null;
            clearstatcache();
            return true;
        }

        $names   = $this->splitPath($path);
        $content = $this->getContent($names['dirname']);
        if ($content->isWritable(vfsStream::getCurrentUser(), vfsStream::getCurrentGroup()) === false) {
            return false;
        }

        clearstatcache();
        return $content->removeChild($names['basename']);
    }

    /**
     * rename from one path to another
     *
     * @param   string  $path_from
     * @param   string  $path_to
     * @return  bool
     * @author  Benoit Aubuchon
     */
    public function rename($path_from, $path_to)
    {
        $srcRealPath = $this->resolvePath(vfsStream::path($path_from));
        $dstRealPath = $this->resolvePath(vfsStream::path($path_to));
        $srcContent  = $this->getContent($srcRealPath);
        if (null == $srcContent) {
            trigger_error(' No such file or directory', E_USER_WARNING);
            return false;
        }
        $dstNames = $this->splitPath($dstRealPath);
        $dstParentContent = $this->getContent($dstNames['dirname']);
        if (null == $dstParentContent) {
            trigger_error('No such file or directory', E_USER_WARNING);
            return false;
        }
        if (!$dstParentContent->isWritable(vfsStream::getCurrentUser(), vfsStream::getCurrentGroup())) {
            trigger_error('Permission denied', E_USER_WARNING);
            return false;
        }
        if ($dstParentContent->getType() !== vfsStreamContent::TYPE_DIR) {
            trigger_error('Target is not a directory', E_USER_WARNING);
            return false;
        }

        $dstContent = clone $srcContent;
        // Renaming the filename
        $dstContent->rename($dstNames['basename']);
        // Copying to the destination
        $dstParentContent->addChild($dstContent);
        // Removing the source
        return $this->doUnlink($srcRealPath);
    }

    /**
     * creates a new directory
     *
     * @param   string  $path
     * @param   int     $mode
     * @param   int     $options
     * @return  bool
     */
    public function mkdir($path, $mode, $options)
    {
        $umask = vfsStream::umask();
        if (0 < $umask) {
            $permissions = $mode & ~$umask;
        } else {
            $permissions = $mode;
        }

        $path = $this->resolvePath(vfsStream::path($path));
        if (null !== $this->getContent($path)) {
            trigger_error('mkdir(): Path vfs://' . $path . ' exists', E_USER_WARNING);
            return false;
        }

        if (null === self::$root) {
            self::$root = vfsStream::newDirectory($path, $permissions);
            return true;
        }

        $maxDepth = count(explode('/', $path));
        $names    = $this->splitPath($path);
        $newDirs  = $names['basename'];
        $dir      = null;
        $i        = 0;
        while ($dir === null && $i < $maxDepth) {
            $dir     = $this->getContent($names['dirname']);
            $names   = $this->splitPath($names['dirname']);
            if (null == $dir) {
                $newDirs = $names['basename'] . '/' . $newDirs;
            }

            $i++;
        }

        if (null === $dir
          || $dir->getType() !== vfsStreamContent::TYPE_DIR
          || $dir->isWritable(vfsStream::getCurrentUser(), vfsStream::getCurrentGroup()) === false) {
            return false;
        }

        $recursive = ((STREAM_MKDIR_RECURSIVE & $options) !== 0) ? (true) : (false);
        if (strpos($newDirs, '/') !== false && false === $recursive) {
            return false;
        }

        vfsStream::newDirectory($newDirs, $permissions)->at($dir);
        return true;
    }

    /**
     * removes a directory
     *
     * @param   string  $path
     * @param   int     $options
     * @return  bool
     * @todo    consider $options with STREAM_MKDIR_RECURSIVE
     */
    public function rmdir($path, $options)
    {
        $path  = $this->resolvePath(vfsStream::path($path));
        $child = $this->getContentOfType($path, vfsStreamContent::TYPE_DIR);
        if (null === $child) {
            return false;
        }

        // can only remove empty directories
        if (count($child->getChildren()) > 0) {
            return false;
        }

        if (self::$root->getName() === $path) {
            // delete root? very brave. :)
            self::$root = null;
            clearstatcache();
            return true;
        }

        $names = $this->splitPath($path);
        $dir   = $this->getContentOfType($names['dirname'], vfsStreamContent::TYPE_DIR);
        if ($dir->isWritable(vfsStream::getCurrentUser(), vfsStream::getCurrentGroup()) === false) {
            return false;
        }

        clearstatcache();
        return $dir->removeChild($child->getName());
    }

    /**
     * opens a directory
     *
     * @param   string  $path
     * @param   int     $options
     * @return  bool
     */
    public function dir_opendir($path, $options)
    {
        $path      = $this->resolvePath(vfsStream::path($path));
        $this->dir = $this->getContentOfType($path, vfsStreamContent::TYPE_DIR);
        if (null === $this->dir || $this->dir->isReadable(vfsStream::getCurrentUser(), vfsStream::getCurrentGroup()) === false) {
            return false;
        }

        $this->dirIterator = $this->dir->getIterator();
        return true;
    }

    /**
     * reads directory contents
     *
     * @return  string
     */
    public function dir_readdir()
    {
        $dir = $this->dirIterator->current();
        if (null === $dir) {
            return false;
        }

        $this->dirIterator->next();
        return $dir->getName();
    }

    /**
     * reset directory iteration
     *
     * @return  bool
     */
    public function dir_rewinddir()
    {
        return $this->dirIterator->rewind();
    }

    /**
     * closes directory
     *
     * @return  bool
     */
    public function dir_closedir()
    {
        $this->dirIterator = null;
        return true;
    }

    /**
     * returns status of url
     *
     * @param   string  $path   path of url to return status for
     * @param   int     $flags  flags set by the stream API
     * @return  array
     */
    public function url_stat($path, $flags)
    {
        $content = $this->getContent($this->resolvePath(vfsStream::path($path)));
        if (null === $content) {
            if (($flags & STREAM_URL_STAT_QUIET) != STREAM_URL_STAT_QUIET) {
                trigger_error(' No such file or directory: ' . $path, E_USER_WARNING);
            }

            return false;

        }

        $fileStat = array('dev'     => 0,
                          'ino'     => 0,
                          'mode'    => $content->getType() | $content->getPermissions(),
                          'nlink'   => 0,
                          'uid'     => $content->getUser(),
                          'gid'     => $content->getGroup(),
                          'rdev'    => 0,
                          'size'    => $content->size(),
                          'atime'   => $content->fileatime(),
                          'mtime'   => $content->filemtime(),
                          'ctime'   => $content->filectime(),
                          'blksize' => -1,
                          'blocks'  => -1
                    );
        return array_merge(array_values($fileStat), $fileStat);
    }
}
?>