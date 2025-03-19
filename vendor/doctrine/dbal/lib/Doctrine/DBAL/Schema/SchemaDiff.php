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

use \Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Schema Diff.
 *
 * @link      www.doctrine-project.org
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license   http://ez.no/licenses/new_bsd New BSD License
 * @since     2.0
 * @author    Benjamin Eberlei <kontakt@beberlei.de>
 */
class SchemaDiff
{
    /**
     * @var \Doctrine\DBAL\Schema\Schema
     */
    public $fromSchema;

    /**
     * All added namespaces.
     *
     * @var string[]
     */
    public $newNamespaces = array();

    /**
     * All removed namespaces.
     *
     * @var string[]
     */
    public $removedNamespaces = array();

    /**
     * All added tables.
     *
     * @var \Doctrine\DBAL\Schema\Table[]
     */
    public $newTables = array();

    /**
     * All changed tables.
     *
     * @var \Doctrine\DBAL\Schema\TableDiff[]
     */
    public $changedTables = array();

    /**
     * All removed tables.
     *
     * @var \Doctrine\DBAL\Schema\Table[]
     */
    public $removedTables = array();

    /**
     * @var \Doctrine\DBAL\Schema\Sequence[]
     */
    public $newSequences = array();

    /**
     * @var \Doctrine\DBAL\Schema\Sequence[]
     */
    public $changedSequences = array();

    /**
     * @var \Doctrine\DBAL\Schema\Sequence[]
     */
    public $removedSequences = array();

    /**
     * @var \Doctrine\DBAL\Schema\ForeignKeyConstraint[]
     */
    public $orphanedForeignKeys = array();

    /**
     * Constructs an SchemaDiff object.
     *
     * @param \Doctrine\DBAL\Schema\Table[]     $newTables
     * @param \Doctrine\DBAL\Schema\TableDiff[] $changedTables
     * @param \Doctrine\DBAL\Schema\Table[]     $removedTables
     * @param \Doctrine\DBAL\Schema\Schema|null $fromSchema
     */
    public function __construct($newTables = array(), $changedTables = array(), $removedTables = array(), Schema $fromSchema = null)
    {
        $this->newTables     = $newTables;
        $this->changedTables = $changedTables;
        $this->removedTables = $removedTables;
        $this->fromSchema    = $fromSchema;
    }

    /**
     * The to save sql mode ensures that the following things don't happen:
     *
     * 1. Tables are deleted
     * 2. Sequences are deleted
     * 3. Foreign Keys which reference tables that would otherwise be deleted.
     *
     * This way it is ensured that assets are deleted which might not be relevant to the metadata schema at all.
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return array
     */
    public function toSaveSql(AbstractPlatform $platform)
    {
        return $this->_toSql($platform, true);
    }

    /**
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return array
     */
    public function toSql(AbstractPlatform $platform)
    {
        return $this->_toSql($platform, false);
    }

    /**
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @param boolean                                   $saveMode
     *
     * @return array
     */
    protected function _toSql(AbstractPlatform $platform, $saveMode = false)
    {
        $sql = array();

        if ($platform->supportsSchemas()) {
            foreach ($this->newNamespaces as $newNamespace) {
                $sql[] = $platform->getCreateSchemaSQL($newNamespace);
            }
        }

        if ($platform->supportsForeignKeyConstraints() && $saveMode == false) {
            foreach ($this->orphanedForeignKeys as $orphanedForeignKey) {
                $sql[] = $platform->getDropForeignKeySQL($orphanedForeignKey, $orphanedForeignKey->getLocalTableName());
            }
        }

        if ($platform->supportsSequences() == true) {
            foreach ($this->changedSequences as $sequence) {
                $sql[] = $platform->getAlterSequenceSQL($sequence);
            }

            if ($saveMode === false) {
                foreach ($this->removedSequences as $sequence) {
                    $sql[] = $platform->getDropSequenceSQL($sequence);
                }
            }

            foreach ($this->newSequences as $sequence) {
                $sql[] = $platform->getCreateSequenceSQL($sequence);
            }
        }

        $foreignKeySql = array();
        foreach ($this->newTables as $table) {
            $sql = array_merge(
                $sql,
                $platform->getCreateTableSQL($table, AbstractPlatform::CREATE_INDEXES)
            );

            if ($platform->supportsForeignKeyConstraints()) {
                foreach ($table->getForeignKeys() as $foreignKey) {
                    $foreignKeySql[] = $platform->getCreateForeignKeySQL($foreignKey, $table);
                }
            }
        }
        $sql = array_merge($sql, $foreignKeySql);

        if ($saveMode === false) {
            foreach ($this->removedTables as $table) {
                $sql[] = $platform->getDropTableSQL($table);
            }
        }

        foreach ($this->changedTables as $tableDiff) {
            $sql = array_merge($sql, $platform->getAlterTableSQL($tableDiff));
        }

        return $sql;
    }
}
