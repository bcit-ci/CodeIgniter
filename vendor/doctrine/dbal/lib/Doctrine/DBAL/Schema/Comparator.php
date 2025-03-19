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

use Doctrine\DBAL\Types;

/**
 * Compares two Schemas and return an instance of SchemaDiff.
 *
 * @link   www.doctrine-project.org
 * @since  2.0
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class Comparator
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $fromSchema
     * @param \Doctrine\DBAL\Schema\Schema $toSchema
     *
     * @return \Doctrine\DBAL\Schema\SchemaDiff
     */
    static public function compareSchemas(Schema $fromSchema, Schema $toSchema)
    {
        $c = new self();

        return $c->compare($fromSchema, $toSchema);
    }

    /**
     * Returns a SchemaDiff object containing the differences between the schemas $fromSchema and $toSchema.
     *
     * The returned differences are returned in such a way that they contain the
     * operations to change the schema stored in $fromSchema to the schema that is
     * stored in $toSchema.
     *
     * @param \Doctrine\DBAL\Schema\Schema $fromSchema
     * @param \Doctrine\DBAL\Schema\Schema $toSchema
     *
     * @return \Doctrine\DBAL\Schema\SchemaDiff
     */
    public function compare(Schema $fromSchema, Schema $toSchema)
    {
        $diff = new SchemaDiff();
        $diff->fromSchema = $fromSchema;

        $foreignKeysToTable = array();

        foreach ($toSchema->getNamespaces() as $namespace) {
            if ( ! $fromSchema->hasNamespace($namespace)) {
                $diff->newNamespaces[$namespace] = $namespace;
            }
        }

        foreach ($fromSchema->getNamespaces() as $namespace) {
            if ( ! $toSchema->hasNamespace($namespace)) {
                $diff->removedNamespaces[$namespace] = $namespace;
            }
        }

        foreach ($toSchema->getTables() as $table) {
            $tableName = $table->getShortestName($toSchema->getName());
            if ( ! $fromSchema->hasTable($tableName)) {
                $diff->newTables[$tableName] = $toSchema->getTable($tableName);
            } else {
                $tableDifferences = $this->diffTable($fromSchema->getTable($tableName), $toSchema->getTable($tableName));
                if ($tableDifferences !== false) {
                    $diff->changedTables[$tableName] = $tableDifferences;
                }
            }
        }

        /* Check if there are tables removed */
        foreach ($fromSchema->getTables() as $table) {
            $tableName = $table->getShortestName($fromSchema->getName());

            $table = $fromSchema->getTable($tableName);
            if ( ! $toSchema->hasTable($tableName)) {
                $diff->removedTables[$tableName] = $table;
            }

            // also remember all foreign keys that point to a specific table
            foreach ($table->getForeignKeys() as $foreignKey) {
                $foreignTable = strtolower($foreignKey->getForeignTableName());
                if (!isset($foreignKeysToTable[$foreignTable])) {
                    $foreignKeysToTable[$foreignTable] = array();
                }
                $foreignKeysToTable[$foreignTable][] = $foreignKey;
            }
        }

        foreach ($diff->removedTables as $tableName => $table) {
            if (isset($foreignKeysToTable[$tableName])) {
                $diff->orphanedForeignKeys = array_merge($diff->orphanedForeignKeys, $foreignKeysToTable[$tableName]);

                // deleting duplicated foreign keys present on both on the orphanedForeignKey
                // and the removedForeignKeys from changedTables
                foreach ($foreignKeysToTable[$tableName] as $foreignKey) {
                    // strtolower the table name to make if compatible with getShortestName
                    $localTableName = strtolower($foreignKey->getLocalTableName());
                    if (isset($diff->changedTables[$localTableName])) {
                        foreach ($diff->changedTables[$localTableName]->removedForeignKeys as $key => $removedForeignKey) {
                            unset($diff->changedTables[$localTableName]->removedForeignKeys[$key]);
                        }
                    }
                }
            }
        }

        foreach ($toSchema->getSequences() as $sequence) {
            $sequenceName = $sequence->getShortestName($toSchema->getName());
            if ( ! $fromSchema->hasSequence($sequenceName)) {
                if ( ! $this->isAutoIncrementSequenceInSchema($fromSchema, $sequence)) {
                    $diff->newSequences[] = $sequence;
                }
            } else {
                if ($this->diffSequence($sequence, $fromSchema->getSequence($sequenceName))) {
                    $diff->changedSequences[] = $toSchema->getSequence($sequenceName);
                }
            }
        }

        foreach ($fromSchema->getSequences() as $sequence) {
            if ($this->isAutoIncrementSequenceInSchema($toSchema, $sequence)) {
                continue;
            }

            $sequenceName = $sequence->getShortestName($fromSchema->getName());

            if ( ! $toSchema->hasSequence($sequenceName)) {
                $diff->removedSequences[] = $sequence;
            }
        }

        return $diff;
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema   $schema
     * @param \Doctrine\DBAL\Schema\Sequence $sequence
     *
     * @return boolean
     */
    private function isAutoIncrementSequenceInSchema($schema, $sequence)
    {
        foreach ($schema->getTables() as $table) {
            if ($sequence->isAutoIncrementsFor($table)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Doctrine\DBAL\Schema\Sequence $sequence1
     * @param \Doctrine\DBAL\Schema\Sequence $sequence2
     *
     * @return boolean
     */
    public function diffSequence(Sequence $sequence1, Sequence $sequence2)
    {
        if ($sequence1->getAllocationSize() != $sequence2->getAllocationSize()) {
            return true;
        }

        if ($sequence1->getInitialValue() != $sequence2->getInitialValue()) {
            return true;
        }

        return false;
    }

    /**
     * Returns the difference between the tables $table1 and $table2.
     *
     * If there are no differences this method returns the boolean false.
     *
     * @param \Doctrine\DBAL\Schema\Table $table1
     * @param \Doctrine\DBAL\Schema\Table $table2
     *
     * @return boolean|\Doctrine\DBAL\Schema\TableDiff
     */
    public function diffTable(Table $table1, Table $table2)
    {
        $changes = 0;
        $tableDifferences = new TableDiff($table1->getName());
        $tableDifferences->fromTable = $table1;

        $table1Columns = $table1->getColumns();
        $table2Columns = $table2->getColumns();

        /* See if all the fields in table 1 exist in table 2 */
        foreach ($table2Columns as $columnName => $column) {
            if ( !$table1->hasColumn($columnName)) {
                $tableDifferences->addedColumns[$columnName] = $column;
                $changes++;
            }
        }
        /* See if there are any removed fields in table 2 */
        foreach ($table1Columns as $columnName => $column) {
            // See if column is removed in table 2.
            if ( ! $table2->hasColumn($columnName)) {
                $tableDifferences->removedColumns[$columnName] = $column;
                $changes++;
                continue;
            }

            // See if column has changed properties in table 2.
            $changedProperties = $this->diffColumn($column, $table2->getColumn($columnName));

            if ( ! empty($changedProperties)) {
                $columnDiff = new ColumnDiff($column->getName(), $table2->getColumn($columnName), $changedProperties);
                $columnDiff->fromColumn = $column;
                $tableDifferences->changedColumns[$column->getName()] = $columnDiff;
                $changes++;
            }
        }

        $this->detectColumnRenamings($tableDifferences);

        $table1Indexes = $table1->getIndexes();
        $table2Indexes = $table2->getIndexes();

        /* See if all the indexes in table 1 exist in table 2 */
        foreach ($table2Indexes as $indexName => $index) {
            if (($index->isPrimary() && $table1->hasPrimaryKey()) || $table1->hasIndex($indexName)) {
                continue;
            }

            $tableDifferences->addedIndexes[$indexName] = $index;
            $changes++;
        }
        /* See if there are any removed indexes in table 2 */
        foreach ($table1Indexes as $indexName => $index) {
            // See if index is removed in table 2.
            if (($index->isPrimary() && ! $table2->hasPrimaryKey()) ||
                ! $index->isPrimary() && ! $table2->hasIndex($indexName)
            ) {
                $tableDifferences->removedIndexes[$indexName] = $index;
                $changes++;
                continue;
            }

            // See if index has changed in table 2.
            $table2Index = $index->isPrimary() ? $table2->getPrimaryKey() : $table2->getIndex($indexName);

            if ($this->diffIndex($index, $table2Index)) {
                $tableDifferences->changedIndexes[$indexName] = $table2Index;
                $changes++;
            }
        }

        $this->detectIndexRenamings($tableDifferences);

        $fromFkeys = $table1->getForeignKeys();
        $toFkeys = $table2->getForeignKeys();

        foreach ($fromFkeys as $key1 => $constraint1) {
            foreach ($toFkeys as $key2 => $constraint2) {
                if ($this->diffForeignKey($constraint1, $constraint2) === false) {
                    unset($fromFkeys[$key1]);
                    unset($toFkeys[$key2]);
                } else {
                    if (strtolower($constraint1->getName()) == strtolower($constraint2->getName())) {
                        $tableDifferences->changedForeignKeys[] = $constraint2;
                        $changes++;
                        unset($fromFkeys[$key1]);
                        unset($toFkeys[$key2]);
                    }
                }
            }
        }

        foreach ($fromFkeys as $constraint1) {
            $tableDifferences->removedForeignKeys[] = $constraint1;
            $changes++;
        }

        foreach ($toFkeys as $constraint2) {
            $tableDifferences->addedForeignKeys[] = $constraint2;
            $changes++;
        }

        return $changes ? $tableDifferences : false;
    }

    /**
     * Try to find columns that only changed their name, rename operations maybe cheaper than add/drop
     * however ambiguities between different possibilities should not lead to renaming at all.
     *
     * @param \Doctrine\DBAL\Schema\TableDiff $tableDifferences
     *
     * @return void
     */
    private function detectColumnRenamings(TableDiff $tableDifferences)
    {
        $renameCandidates = array();
        foreach ($tableDifferences->addedColumns as $addedColumnName => $addedColumn) {
            foreach ($tableDifferences->removedColumns as $removedColumn) {
                if (count($this->diffColumn($addedColumn, $removedColumn)) == 0) {
                    $renameCandidates[$addedColumn->getName()][] = array($removedColumn, $addedColumn, $addedColumnName);
                }
            }
        }

        foreach ($renameCandidates as $candidateColumns) {
            if (count($candidateColumns) == 1) {
                list($removedColumn, $addedColumn) = $candidateColumns[0];
                $removedColumnName = strtolower($removedColumn->getName());
                $addedColumnName = strtolower($addedColumn->getName());

                if ( ! isset($tableDifferences->renamedColumns[$removedColumnName])) {
                    $tableDifferences->renamedColumns[$removedColumnName] = $addedColumn;
                    unset($tableDifferences->addedColumns[$addedColumnName]);
                    unset($tableDifferences->removedColumns[$removedColumnName]);
                }
            }
        }
    }

    /**
     * Try to find indexes that only changed their name, rename operations maybe cheaper than add/drop
     * however ambiguities between different possibilities should not lead to renaming at all.
     *
     * @param \Doctrine\DBAL\Schema\TableDiff $tableDifferences
     *
     * @return void
     */
    private function detectIndexRenamings(TableDiff $tableDifferences)
    {
        $renameCandidates = array();

        // Gather possible rename candidates by comparing each added and removed index based on semantics.
        foreach ($tableDifferences->addedIndexes as $addedIndexName => $addedIndex) {
            foreach ($tableDifferences->removedIndexes as $removedIndex) {
                if (! $this->diffIndex($addedIndex, $removedIndex)) {
                    $renameCandidates[$addedIndex->getName()][] = array($removedIndex, $addedIndex, $addedIndexName);
                }
            }
        }

        foreach ($renameCandidates as $candidateIndexes) {
            // If the current rename candidate contains exactly one semantically equal index,
            // we can safely rename it.
            // Otherwise it is unclear if a rename action is really intended,
            // therefore we let those ambiguous indexes be added/dropped.
            if (count($candidateIndexes) === 1) {
                list($removedIndex, $addedIndex) = $candidateIndexes[0];

                $removedIndexName = strtolower($removedIndex->getName());
                $addedIndexName = strtolower($addedIndex->getName());

                if (! isset($tableDifferences->renamedIndexes[$removedIndexName])) {
                    $tableDifferences->renamedIndexes[$removedIndexName] = $addedIndex;
                    unset($tableDifferences->addedIndexes[$addedIndexName]);
                    unset($tableDifferences->removedIndexes[$removedIndexName]);
                }
            }
        }
    }

    /**
     * @param \Doctrine\DBAL\Schema\ForeignKeyConstraint $key1
     * @param \Doctrine\DBAL\Schema\ForeignKeyConstraint $key2
     *
     * @return boolean
     */
    public function diffForeignKey(ForeignKeyConstraint $key1, ForeignKeyConstraint $key2)
    {
        if (array_map('strtolower', $key1->getUnquotedLocalColumns()) != array_map('strtolower', $key2->getUnquotedLocalColumns())) {
            return true;
        }

        if (array_map('strtolower', $key1->getUnquotedForeignColumns()) != array_map('strtolower', $key2->getUnquotedForeignColumns())) {
            return true;
        }

        if ($key1->getUnqualifiedForeignTableName() !== $key2->getUnqualifiedForeignTableName()) {
            return true;
        }

        if ($key1->onUpdate() != $key2->onUpdate()) {
            return true;
        }

        if ($key1->onDelete() != $key2->onDelete()) {
            return true;
        }

        return false;
    }

    /**
     * Returns the difference between the fields $field1 and $field2.
     *
     * If there are differences this method returns $field2, otherwise the
     * boolean false.
     *
     * @param \Doctrine\DBAL\Schema\Column $column1
     * @param \Doctrine\DBAL\Schema\Column $column2
     *
     * @return array
     */
    public function diffColumn(Column $column1, Column $column2)
    {
        $properties1 = $column1->toArray();
        $properties2 = $column2->toArray();

        $changedProperties = array();

        foreach (array('type', 'notnull', 'unsigned', 'autoincrement') as $property) {
            if ($properties1[$property] != $properties2[$property]) {
                $changedProperties[] = $property;
            }
        }

        if ($properties1['default'] != $properties2['default'] ||
            // Null values need to be checked additionally as they tell whether to create or drop a default value.
            // null != 0, null != false, null != '' etc. This affects platform's table alteration SQL generation.
            (null === $properties1['default'] && null !== $properties2['default']) ||
            (null === $properties2['default'] && null !== $properties1['default'])
        ) {
            $changedProperties[] = 'default';
        }

        if (($properties1['type'] instanceof Types\StringType && ! $properties1['type'] instanceof Types\GuidType) ||
            $properties1['type'] instanceof Types\BinaryType
        ) {
            // check if value of length is set at all, default value assumed otherwise.
            $length1 = $properties1['length'] ?: 255;
            $length2 = $properties2['length'] ?: 255;
            if ($length1 != $length2) {
                $changedProperties[] = 'length';
            }

            if ($properties1['fixed'] != $properties2['fixed']) {
                $changedProperties[] = 'fixed';
            }
        } elseif ($properties1['type'] instanceof Types\DecimalType) {
            if (($properties1['precision'] ?: 10) != ($properties2['precision'] ?: 10)) {
                $changedProperties[] = 'precision';
            }
            if ($properties1['scale'] != $properties2['scale']) {
                $changedProperties[] = 'scale';
            }
        }

        // A null value and an empty string are actually equal for a comment so they should not trigger a change.
        if ($properties1['comment'] !== $properties2['comment'] &&
            ! (null === $properties1['comment'] && '' === $properties2['comment']) &&
            ! (null === $properties2['comment'] && '' === $properties1['comment'])
        ) {
            $changedProperties[] = 'comment';
        }

        $customOptions1 = $column1->getCustomSchemaOptions();
        $customOptions2 = $column2->getCustomSchemaOptions();

        foreach (array_merge(array_keys($customOptions1), array_keys($customOptions2)) as $key) {
            if ( ! array_key_exists($key, $properties1) || ! array_key_exists($key, $properties2)) {
                $changedProperties[] = $key;
            } elseif ($properties1[$key] !== $properties2[$key]) {
                $changedProperties[] = $key;
            }
        }

        $platformOptions1 = $column1->getPlatformOptions();
        $platformOptions2 = $column2->getPlatformOptions();

        foreach (array_keys(array_intersect_key($platformOptions1, $platformOptions2)) as $key) {
            if ($properties1[$key] !== $properties2[$key]) {
                $changedProperties[] = $key;
            }
        }

        return array_unique($changedProperties);
    }

    /**
     * Finds the difference between the indexes $index1 and $index2.
     *
     * Compares $index1 with $index2 and returns $index2 if there are any
     * differences or false in case there are no differences.
     *
     * @param \Doctrine\DBAL\Schema\Index $index1
     * @param \Doctrine\DBAL\Schema\Index $index2
     *
     * @return boolean
     */
    public function diffIndex(Index $index1, Index $index2)
    {
        if ($index1->isFullfilledBy($index2) && $index2->isFullfilledBy($index1)) {
            return false;
        }

        return true;
    }
}
