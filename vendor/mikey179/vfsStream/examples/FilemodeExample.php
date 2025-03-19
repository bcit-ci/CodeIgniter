<?php
/**
 * This file is part of vfsStream.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  org\bovigo\vfs
 */
namespace org\bovigo\vfs\example;
/**
 * Example class.
 */
class FilemodeExample
{
    /**
     * id of the example
     *
     * @type  string
     */
    protected $id;
    /**
     * a directory where we do something..
     *
     * @type  string
     */
    protected $directory;
    /**
     * file mode for newly created directories
     *
     * @type  int
     */
    protected $fileMode;

    /**
     * constructor
     *
     * @param  string  $id
     * @param  int     $fileMode  optional
     */
    public function __construct($id,  $fileMode = 0700)
    {
        $this->id       = $id;
        $this->fileMode = $fileMode;
    }

    /**
     * sets the directory
     *
     * @param  string  $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory . DIRECTORY_SEPARATOR . $this->id;
        if (file_exists($this->directory) === false) {
            mkdir($this->directory, $this->fileMode, true);
        }
    }

    // more source code here...
}
?>