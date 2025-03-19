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
 * An abstraction class for a foreign key constraint.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @author Steve Müller <st.mueller@dzh-online.de>
 * @link   www.doctrine-project.org
 * @since  2.0
 */
class ForeignKeyConstraint extends AbstractAsset implements Constraint
{
    /**
     * Instance of the referencing table the foreign key constraint is associated with.
     *
     * @var \Doctrine\DBAL\Schema\Table
     */
    protected $_localTable;

    /**
     * Asset identifier instances of the referencing table column names the foreign key constraint is associated with.
     * array($columnName => Identifier)
     *
     * @var Identifier[]
     */
    protected $_localColumnNames;

    /**
     * Table or asset identifier instance of the referenced table name the foreign key constraint is associated with.
     *
     * @var Table|Identifier
     */
    protected $_foreignTableName;

    /**
     * Asset identifier instances of the referenced table column names the foreign key constraint is associated with.
     * array($columnName => Identifier)
     *
     * @var Identifier[]
     */
    protected $_foreignColumnNames;

    /**
     * @var array Options associated with the foreign key constraint.
     */
    protected $_options;

    /**
     * Initializes the foreign key constraint.
     *
     * @param array        $localColumnNames   Names of the referencing table columns.
     * @param Table|string $foreignTableName   Referenced table.
     * @param array        $foreignColumnNames Names of the referenced table columns.
     * @param string|null  $name               Name of the foreign key constraint.
     * @param array        $options            Options associated with the foreign key constraint.
     */
    public function __construct(array $localColumnNames, $foreignTableName, array $foreignColumnNames, $name = null, array $options = array())
    {
        $this->_setName($name);
        $identifierConstructorCallback = function ($column) {
            return new Identifier($column);
        };
        $this->_localColumnNames = $localColumnNames
            ? array_combine($localColumnNames, array_map($identifierConstructorCallback, $localColumnNames))
            : array();

        if ($foreignTableName instanceof Table) {
            $this->_foreignTableName = $foreignTableName;
        } else {
            $this->_foreignTableName = new Identifier($foreignTableName);
        }

        $this->_foreignColumnNames = $foreignColumnNames
            ? array_combine($foreignColumnNames, array_map($identifierConstructorCallback, $foreignColumnNames))
            : array();
        $this->_options = $options;
    }

    /**
     * Returns the name of the referencing table
     * the foreign key constraint is associated with.
     *
     * @return string
     */
    public function getLocalTableName()
    {
        return $this->_localTable->getName();
    }

    /**
     * Sets the Table instance of the referencing table
     * the foreign key constraint is associated with.
     *
     * @param \Doctrine\DBAL\Schema\Table $table Instance of the referencing table.
     *
     * @return void
     */
    public function setLocalTable(Table $table)
    {
        $this->_localTable = $table;
    }

    /**
     * @return Table
     */
    public function getLocalTable()
    {
        return $this->_localTable;
    }

    /**
     * Returns the names of the referencing table columns
     * the foreign key constraint is associated with.
     *
     * @return array
     */
    public function getLocalColumns()
    {
        return array_keys($this->_localColumnNames);
    }

    /**
     * Returns the quoted representation of the referencing table column names
     * the foreign key constraint is associated with.
     *
     * But only if they were defined with one or the referencing table column name
     * is a keyword reserved by the platform.
     * Otherwise the plain unquoted value as inserted is returned.
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The platform to use for quotation.
     *
     * @return array
     */
    public function getQuotedLocalColumns(AbstractPlatform $platform)
    {
        $columns = array();

        foreach ($this->_localColumnNames as $column) {
            $columns[] = $column->getQuotedName($platform);
        }

        return $columns;
    }

    /**
     * Returns unquoted representation of local table column names for comparison with other FK
     *
     * @return array
     */
    public function getUnquotedLocalColumns()
    {
        return array_map(array($this, 'trimQuotes'), $this->getLocalColumns());
    }

    /**
     * Returns unquoted representation of foreign table column names for comparison with other FK
     *
     * @return array
     */
    public function getUnquotedForeignColumns()
    {
        return array_map(array($this, 'trimQuotes'), $this->getForeignColumns());
    }

    /**
     * {@inheritdoc}
     *
     * @see getLocalColumns
     */
    public function getColumns()
    {
        return $this->getLocalColumns();
    }

    /**
     * Returns the quoted representation of the referencing table column names
     * the foreign key constraint is associated with.
     *
     * But only if they were defined with one or the referencing table column name
     * is a keyword reserved by the platform.
     * Otherwise the plain unquoted value as inserted is returned.
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The platform to use for quotation.
     *
     * @see getQuotedLocalColumns
     *
     * @return array
     */
    public function getQuotedColumns(AbstractPlatform $platform)
    {
        return $this->getQuotedLocalColumns($platform);
    }

    /**
     * Returns the name of the referenced table
     * the foreign key constraint is associated with.
     *
     * @return string
     */
    public function getForeignTableName()
    {
        return $this->_foreignTableName->getName();
    }

    /**
     * Returns the non-schema qualified foreign table name.
     *
     * @return string
     */
    public function getUnqualifiedForeignTableName()
    {
        $parts = explode(".", $this->_foreignTableName->getName());

        return strtolower(end($parts));
    }

    /**
     * Returns the quoted representation of the referenced table name
     * the foreign key constraint is associated with.
     *
     * But only if it was defined with one or the referenced table name
     * is a keyword reserved by the platform.
     * Otherwise the plain unquoted value as inserted is returned.
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The platform to use for quotation.
     *
     * @return string
     */
    public function getQuotedForeignTableName(AbstractPlatform $platform)
    {
        return $this->_foreignTableName->getQuotedName($platform);
    }

    /**
     * Returns the names of the referenced table columns
     * the foreign key constraint is associated with.
     *
     * @return array
     */
    public function getForeignColumns()
    {
        return array_keys($this->_foreignColumnNames);
    }

    /**
     * Returns the quoted representation of the referenced table column names
     * the foreign key constraint is associated with.
     *
     * But only if they were defined with one or the referenced table column name
     * is a keyword reserved by the platform.
     * Otherwise the plain unquoted value as inserted is returned.
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The platform to use for quotation.
     *
     * @return array
     */
    public function getQuotedForeignColumns(AbstractPlatform $platform)
    {
        $columns = array();

        foreach ($this->_foreignColumnNames as $column) {
            $columns[] = $column->getQuotedName($platform);
        }

        return $columns;
    }

    /**
     * Returns whether or not a given option
     * is associated with the foreign key constraint.
     *
     * @param string $name Name of the option to check.
     *
     * @return boolean
     */
    public function hasOption($name)
    {
        return isset($this->_options[$name]);
    }

    /**
     * Returns an option associated with the foreign key constraint.
     *
     * @param string $name Name of the option the foreign key constraint is associated with.
     *
     * @return mixed
     */
    public function getOption($name)
    {
        return $this->_options[$name];
    }

    /**
     * Returns the options associated with the foreign key constraint.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Returns the referential action for UPDATE operations
     * on the referenced table the foreign key constraint is associated with.
     *
     * @return string|null
     */
    public function onUpdate()
    {
        return $this->onEvent('onUpdate');
    }

    /**
     * Returns the referential action for DELETE operations
     * on the referenced table the foreign key constraint is associated with.
     *
     * @return string|null
     */
    public function onDelete()
    {
        return $this->onEvent('onDelete');
    }

    /**
     * Returns the referential action for a given database operation
     * on the referenced table the foreign key constraint is associated with.
     *
     * @param string $event Name of the database operation/event to return the referential action for.
     *
     * @return string|null
     */
    private function onEvent($event)
    {
        if (isset($this->_options[$event])) {
            $onEvent = strtoupper($this->_options[$event]);

            if ( ! in_array($onEvent, array('NO ACTION', 'RESTRICT'))) {
                return $onEvent;
            }
        }

        return false;
    }

    /**
     * Checks whether this foreign key constraint intersects the given index columns.
     *
     * Returns `true` if at least one of this foreign key's local columns
     * matches one of the given index's columns, `false` otherwise.
     *
     * @param Index $index The index to be checked against.
     *
     * @return boolean
     */
    public function intersectsIndexColumns(Index $index)
    {
        foreach ($index->getColumns() as $indexColumn) {
            foreach ($this->_localColumnNames as $localColumn) {
                if (strtolower($indexColumn) === strtolower($localColumn->getName())) {
                    return true;
                }
            }
        }

        return false;
    }
}
