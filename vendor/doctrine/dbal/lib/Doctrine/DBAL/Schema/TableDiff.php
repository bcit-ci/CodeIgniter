<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\DBAL\Schema;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Table Diff.
 *
 * @link   www.doctrine-project.org
 * @since  2.0
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class TableDiff
{
    /**
     * @var string
     */
    public $name = null;

    /**
     * @var string|boolean
     */
    public $newName = false;

    /**
     * All added fields.
     *
     * @var \Doctrine\DBAL\Schema\Column[]
     */
    public $addedColumns;

    /**
     * All changed fields.
     *
     * @var \Doctrine\DBAL\Schema\ColumnDiff[]
     */
    public $changedColumns = array();

    /**
     * All removed fields.
     *
     * @var \Doctrine\DBAL\Schema\Column[]
     */
    public $removedColumns = array();

    /**
     * Columns that are only renamed from key to column instance name.
     *
     * @var \Doctrine\DBAL\Schema\Column[]
     */
    public $renamedColumns = array();

    /**
     * All added indexes.
     *
     * @var \Doctrine\DBAL\Schema\Index[]
     */
    public $addedIndexes = array();

    /**
     * All changed indexes.
     *
     * @var \Doctrine\DBAL\Schema\Index[]
     */
    public $changedIndexes = array();

    /**
     * All removed indexes
     *
     * @var \Doctrine\DBAL\Schema\Index[]
     */
    public $removedIndexes = array();

    /**
     * Indexes that are only renamed but are identical otherwise.
     *
     * @var \Doctrine\DBAL\Schema\Index[]
     */
    public $renamedIndexes = array();

    /**
     * All added foreign key definitions
     *
     * @var \Doctrine\DBAL\Schema\ForeignKeyConstraint[]
     */
    public $addedForeignKeys = array();

    /**
     * All changed foreign keys
     *
     * @var \Doctrine\DBAL\Schema\ForeignKeyConstraint[]
     */
    public $changedForeignKeys = array();

    /**
     * All removed foreign keys
     *
     * @var \Doctrine\DBAL\Schema\ForeignKeyConstraint[]
     */
    public $removedForeignKeys = array();

    /**
     * @var \Doctrine\DBAL\Schema\Table
     */
    public $fromTable;

    /**
     * Constructs an TableDiff object.
     *
     * @param string                             $tableName
     * @param \Doctrine\DBAL\Schema\Column[]     $addedColumns
     * @param \Doctrine\DBAL\Schema\ColumnDiff[] $changedColumns
     * @param \Doctrine\DBAL\Schema\Column[]     $removedColumns
     * @param \Doctrine\DBAL\Schema\Index[]      $addedIndexes
     * @param \Doctrine\DBAL\Schema\Index[]      $changedIndexes
     * @param \Doctrine\DBAL\Schema\Index[]      $removedIndexes
     * @param \Doctrine\DBAL\Schema\Table|null   $fromTable
     */
    public function __construct($tableName, $addedColumns = array(),
        $changedColumns = array(), $removedColumns = array(), $addedIndexes = array(),
        $changedIndexes = array(), $removedIndexes = array(), Table $fromTable = null)
    {
        $this->name = $tableName;
        $this->addedColumns = $addedColumns;
        $this->changedColumns = $changedColumns;
        $this->removedColumns = $removedColumns;
        $this->addedIndexes = $addedIndexes;
        $this->changedIndexes = $changedIndexes;
        $this->removedIndexes = $removedIndexes;
        $this->fromTable = $fromTable;
    }

    /**
     * @param AbstractPlatform $platform The platform to use for retrieving this table diff's name.
     *
     * @return \Doctrine\DBAL\Schema\Identifier
     */
    public function getName(AbstractPlatform $platform)
    {
        return new Identifier(
            $this->fromTable instanceof Table ? $this->fromTable->getQuotedName($platform) : $this->name
        );
    }

    /**
     * @return \Doctrine\DBAL\Schema\Identifier|boolean
     */
    public function getNewName()
    {
        return $this->newName ? new Identifier($this->newName) : $this->newName;
    }
}
