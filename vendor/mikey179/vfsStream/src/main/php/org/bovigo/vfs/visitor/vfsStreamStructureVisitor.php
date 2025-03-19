<?php
/**
 * This file is part of vfsStream.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  org\bovigo\vfs
 */
namespace org\bovigo\vfs\visitor;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
/**
 * Visitor which traverses a content structure recursively to create an array structure from it.
 *
 * @since  0.10.0
 * @see    https://github.com/mikey179/vfsStream/issues/10
 */
class vfsStreamStructureVisitor extends vfsStreamAbstractVisitor
{
    /**
     * collected structure
     *
     * @type  array
     */
    protected $structure = array();
    /**
     * poiting to currently iterated directory
     *
     * @type  array
     */
    protected $current;

    /**
     * constructor
     *
     * @api
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * visit a file and process it
     *
     * @param   vfsStreamFile  $file
     * @return  vfsStreamStructureVisitor
     */
    public function visitFile(vfsStreamFile $file)
    {
        $this->current[$file->getName()] = $file->getContent();
        return $this;
    }

    /**
     * visit a directory and process it
     *
     * @param   vfsStreamDirectory  $dir
     * @return  vfsStreamStructureVisitor
     */
    public function visitDirectory(vfsStreamDirectory $dir)
    {
        $this->current[$dir->getName()] = array();
        $tmp           =& $this->current;
        $this->current =& $tmp[$dir->getName()];
        foreach ($dir as $child) {
            $this->visit($child);
        }

        $this->current =& $tmp;
        return $this;
    }

    /**
     * returns structure of visited contents
     *
     * @return  array
     * @api
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * resets structure so visitor could be reused
     *
     * @return  vfsStreamStructureVisitor
     */
    public function reset()
    {
        $this->structure = array();
        $this->current   =& $this->structure;
        return $this;
    }
}
?>