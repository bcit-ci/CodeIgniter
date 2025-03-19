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
 *
 * @since  0.10.0
 */
class vfsStreamWrapperRecordingProxy extends vfsStreamWrapper
{
    /**
     * list of called methods for a stream
     *
     * @var  array
     */
    protected static $calledMethods = array();
    /**
     * currently opened path
     *
     * @var  string
     */
    protected $path;

    /**
     * records method call for given path
     *
     * @param  string  $method
     * @param  string  $path
     */
    protected static function recordMethodCall($method, $path)
    {
        if (isset(self::$calledMethods[$path]) === false) {
            self::$calledMethods[$path] = array();
        }

        self::$calledMethods[$path][] = $method;
    }

    /**
     * returns recorded method calls for given path
     *
     * @param   string         $path
     * @return  array<string>
     */
    public static function getMethodCalls($path)
    {
        if (isset(self::$calledMethods[$path]) === true) {
            return self::$calledMethods[$path];
        }

        return array();
    }

    /**
     * helper method for setting up vfsStream with the proxy
     *
     * @param   string              $rootDirName  optional  name of root directory
     * @param   int                 $permissions  optional  file permissions of root directory
     * @return  vfsStreamDirectory
     * @throws  vfsStreamException
     */
    public static function setup($rootDirName = 'root', $permissions = null)
    {
        self::$root = vfsStream::newDirectory($rootDirName, $permissions);
        if (true === self::$registered) {
            return self::$root;
        }

        if (@stream_wrapper_register(vfsStream::SCHEME, __CLASS__) === false) {
            throw new vfsStreamException('A handler has already been registered for the ' . vfsStream::SCHEME . ' protocol.');
        }

        self::$registered = true;
        return self::$root;
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
        $this->path = $path;
        self::recordMethodCall('stream_open', $this->path);
        return parent::stream_open($path, $mode, $options, $opened_path);
    }

    /**
     * closes the stream
     */
    public function stream_close()
    {
        self::recordMethodCall('stream_close', $this->path);
        return parent::stream_close();
    }

    /**
     * read the stream up to $count bytes
     *
     * @param   int     $count  amount of bytes to read
     * @return  string
     */
    public function stream_read($count)
    {
        self::recordMethodCall('stream_read', $this->path);
        return parent::stream_read($count);
    }

    /**
     * writes data into the stream
     *
     * @param   string  $data
     * @return  int     amount of bytes written
     */
    public function stream_write($data)
    {
        self::recordMethodCall('stream_write', $this->path);
        return parent::stream_write($data);
    }

    /**
     * checks whether stream is at end of file
     *
     * @return  bool
     */
    public function stream_eof()
    {
        self::recordMethodCall('stream_eof', $this->path);
        return parent::stream_eof();
    }

    /**
     * returns the current position of the stream
     *
     * @return  int
     */
    public function stream_tell()
    {
        self::recordMethodCall('stream_tell', $this->path);
        return parent::stream_tell();
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
        self::recordMethodCall('stream_seek', $this->path);
        return parent::stream_seek($offset, $whence);
    }

    /**
     * flushes unstored data into storage
     *
     * @return  bool
     */
    public function stream_flush()
    {
        self::recordMethodCall('stream_flush', $this->path);
        return parent::stream_flush();
    }

    /**
     * returns status of stream
     *
     * @return  array
     */
    public function stream_stat()
    {
        self::recordMethodCall('stream_stat', $this->path);
        return parent::stream_stat();
    }

    /**
     * retrieve the underlaying resource
     *
     * @param   int  $cast_as
     * @return  bool
     */
    public function stream_cast($cast_as)
    {
        self::recordMethodCall('stream_cast', $this->path);
        return parent::stream_cast($cast_as);
    }

    /**
     * set lock status for stream
     *
     * @param   int   $operation
     * @return  bool
     */
    public function stream_lock($operation)
    {
        self::recordMethodCall('stream_link', $this->path);
        return parent::stream_lock($operation);
    }

    /**
     * remove the data under the given path
     *
     * @param   string  $path
     * @return  bool
     */
    public function unlink($path)
    {
        self::recordMethodCall('unlink', $path);
        return parent::unlink($path);
    }

    /**
     * rename from one path to another
     *
     * @param   string  $path_from
     * @param   string  $path_to
     * @return  bool
     */
    public function rename($path_from, $path_to)
    {
        self::recordMethodCall('rename', $path_from);
        return parent::rename($path_from, $path_to);
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
        self::recordMethodCall('mkdir', $path);
        return parent::mkdir($path, $mode, $options);
    }

    /**
     * removes a directory
     *
     * @param   string  $path
     * @param   int     $options
     * @return  bool
     */
    public function rmdir($path, $options)
    {
        self::recordMethodCall('rmdir', $path);
        return parent::rmdir($path, $options);
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
        $this->path = $path;
        self::recordMethodCall('dir_opendir', $this->path);
        return parent::dir_opendir($path, $options);
    }

    /**
     * reads directory contents
     *
     * @return  string
     */
    public function dir_readdir()
    {
        self::recordMethodCall('dir_readdir', $this->path);
        return parent::dir_readdir();
    }

    /**
     * reset directory iteration
     *
     * @return  bool
     */
    public function dir_rewinddir()
    {
        self::recordMethodCall('dir_rewinddir', $this->path);
        return parent::dir_rewinddir();
    }

    /**
     * closes directory
     *
     * @return  bool
     */
    public function dir_closedir()
    {
        self::recordMethodCall('dir_closedir', $this->path);
        return parent::dir_closedir();
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
        self::recordMethodCall('url_stat', $path);
        return parent::url_stat($path, $flags);
    }
}
?>