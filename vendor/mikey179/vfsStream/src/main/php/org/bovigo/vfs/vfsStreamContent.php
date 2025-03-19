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
 * Interface for stream contents.
 */
interface vfsStreamContent
{
    /**
     * stream content type: file
     *
     * @see  getType()
     */
    const TYPE_FILE = 0100000;
    /**
     * stream content type: directory
     *
     * @see  getType()
     */
    const TYPE_DIR  = 0040000;
    /**
     * stream content type: symbolic link
     *
     * @see  getType();
     */
    #const TYPE_LINK = 0120000;

    /**
     * returns the file name of the content
     *
     * @return  string
     */
    public function getName();

    /**
     * renames the content
     *
     * @param  string  $newName
     */
    public function rename($newName);

    /**
     * checks whether the container can be applied to given name
     *
     * @param   string  $name
     * @return  bool
     */
    public function appliesTo($name);

    /**
     * returns the type of the container
     *
     * @return  int
     */
    public function getType();

    /**
     * returns size of content
     *
     * @return  int
     */
    public function size();

    /**
     * sets the last modification time of the stream content
     *
     * @param   int  $filemtime
     * @return  vfsStreamContent
     */
    public function lastModified($filemtime);

    /**
     * returns the last modification time of the stream content
     *
     * @return  int
     */
    public function filemtime();

    /**
     * adds content to given container
     *
     * @param   vfsStreamContainer  $container
     * @return  vfsStreamContent
     */
    public function at(vfsStreamContainer $container);

    /**
     * change file mode to given permissions
     *
     * @param   int  $permissions
     * @return  vfsStreamContent
     */
    public function chmod($permissions);

    /**
     * returns permissions
     *
     * @return  int
     */
    public function getPermissions();

    /**
     * checks whether content is readable
     *
     * @param   int   $user   id of user to check for
     * @param   int   $group  id of group to check for
     * @return  bool
     */
    public function isReadable($user, $group);

    /**
     * checks whether content is writable
     *
     * @param   int   $user   id of user to check for
     * @param   int   $group  id of group to check for
     * @return  bool
     */
    public function isWritable($user, $group);

    /**
     * checks whether content is executable
     *
     * @param   int   $user   id of user to check for
     * @param   int   $group  id of group to check for
     * @return  bool
     */
    public function isExecutable($user, $group);

    /**
     * change owner of file to given user
     *
     * @param   int  $user
     * @return  vfsStreamContent
     */
    public function chown($user);

    /**
     * checks whether file is owned by given user
     *
     * @param   int  $user
     * @return  bool
     */
    public function isOwnedByUser($user);

    /**
     * returns owner of file
     *
     * @return  int
     */
    public function getUser();

    /**
     * change owner group of file to given group
     *
     * @param   int  $group
     * @return  vfsStreamContent
     */
    public function chgrp($group);

    /**
     * checks whether file is owned by group
     *
     * @param   int   $group
     * @return  bool
     */
    public function isOwnedByGroup($group);

    /**
     * returns owner group of file
     *
     * @return  int
     */
    public function getGroup();
}
?>