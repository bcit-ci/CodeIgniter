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
 * Example class to demonstrate testing of failure behaviour with vfsStream.
 */
class FailureExample
{
    /**
     * filename to write data
     *
     * @type  string
     */
    protected $filename;

    /**
     * constructor
     *
     * @param  string  $id
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * sets the directory
     *
     * @param  string  $directory
     */
    public function writeData($data)
    {
        $bytes = @file_put_contents($this->filename, $data);
        if (false === $bytes) {
            return 'could not write data';
        }

        return 'ok';
    }

    // more source code here...
}
?>