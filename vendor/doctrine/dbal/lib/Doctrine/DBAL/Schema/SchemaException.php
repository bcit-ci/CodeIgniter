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

class SchemaException extends \Doctrine\DBAL\DBALException
{
    const TABLE_DOESNT_EXIST = 10;
    const TABLE_ALREADY_EXISTS = 20;
    const COLUMN_DOESNT_EXIST = 30;
    const COLUMN_ALREADY_EXISTS = 40;
    const INDEX_DOESNT_EXIST = 50;
    const INDEX_ALREADY_EXISTS = 60;
    const SEQUENCE_DOENST_EXIST = 70;
    const SEQUENCE_ALREADY_EXISTS = 80;
    const INDEX_INVALID_NAME = 90;
    const FOREIGNKEY_DOESNT_EXIST = 100;
    const NAMESPACE_ALREADY_EXISTS = 110;

    /**
     * @param string $tableName
     *
     * @return \Doctrine\DBAL\Schema\SchemaException
     */
    static public function tableDoesNotExist($tableName)
    {
        return new self("There is no table with name '".$tableName."' in the schema.", self::TABLE_DOESNT_EXIST);
    }

    /**
     * @param string $indexName
     *
     * @return \Doctrine\DBAL\Schema\SchemaException
     */
    static public function indexNameInvalid($indexName)
    {
        return new self("Invalid index-name $indexName given, has to be [a-zA-Z0-9_]", self::INDEX_INVALID_NAME);
    }

    /**
     * @param string $indexName
     * @param string $table
     *
     * @return \Doctrine\DBAL\Schema\SchemaException
     */
    static public function indexDoesNotExist($indexName, $table)
    {
        return new self("Index '$indexName' does not exist on table '$table'.", self::INDEX_DOESNT_EXIST);
    }

    /**
     * @param string $indexName
     * @param string $table
     *
     * @return \Doctrine\DBAL\Schema\SchemaException
     */
    static public function indexAlreadyExists($indexName, $table)
    {
        return new self("An index with name '$indexName' was already defined on table '$table'.", self::INDEX_ALREADY_EXISTS);
    }

    /**
     * @param string $columnName
     * @param string $table
     *
     * @return \Doctrine\DBAL\Schema\SchemaException
     */
    static public function columnDoesNotExist($columnName, $table)
    {
        return new self("There is no column with name '$columnName' on table '$table'.", self::COLUMN_DOESNT_EXIST);
    }

    /**
     * @param string $namespaceName
     *
     * @return \Doctrine\DBAL\Schema\SchemaException
     */
    static public function namespaceAlreadyExists($namespaceName)
    {
        return new self(
            sprintf("The namespace with name '%s' already exists.", $namespaceName),
            self::NAMESPACE_ALREADY_EXISTS
        );
    }

    /**
     * @param string $tableName
     *
     * @return \Doctrine\DBAL\Schema\SchemaException
     */
    static public function tableAlreadyExists($tableName)
    {
        return new self("The table with name '".$tableName."' already exists.", self::TABLE_ALREADY_EXISTS);
    }

    /**
     * @param string $tableName
     * @param string $columnName
     *
     * @return \Doctrine\DBAL\Schema\SchemaException
     */
    static public function columnAlreadyExists($tableName, $columnName)
    {
        return new self(
            "The column '".$columnName."' on table '".$tableName."' already exists.", self::COLUMN_ALREADY_EXISTS
        );
    }

    /**
     * @param string $sequenceName
     *
     * @return \Doctrine\DBAL\Schema\SchemaException
     */
    static public function sequenceAlreadyExists($sequenceName)
    {
        return new self("The sequence '".$sequenceName."' already exists.", self::SEQUENCE_ALREADY_EXISTS);
    }

    /**
     * @param string $sequenceName
     *
     * @return \Doctrine\DBAL\Schema\SchemaException
     */
    static public function sequenceDoesNotExist($sequenceName)
    {
        return new self("There exists no sequence with the name '".$sequenceName."'.", self::SEQUENCE_DOENST_EXIST);
    }

    /**
     * @param string $fkName
     * @param string $table
     *
     * @return \Doctrine\DBAL\Schema\SchemaException
     */
    static public function foreignKeyDoesNotExist($fkName, $table)
    {
        return new self("There exists no foreign key with the name '$fkName' on table '$table'.", self::FOREIGNKEY_DOESNT_EXIST);
    }

    /**
     * @param \Doctrine\DBAL\Schema\Table                $localTable
     * @param \Doctrine\DBAL\Schema\ForeignKeyConstraint $foreignKey
     *
     * @return \Doctrine\DBAL\Schema\SchemaException
     */
    static public function namedForeignKeyRequired(Table $localTable, ForeignKeyConstraint $foreignKey)
    {
        return new self(
            "The performed schema operation on ".$localTable->getName()." requires a named foreign key, ".
            "but the given foreign key from (".implode(", ", $foreignKey->getColumns()).") onto foreign table ".
            "'".$foreignKey->getForeignTableName()."' (".implode(", ", $foreignKey->getForeignColumns()).") is currently ".
            "unnamed."
        );
    }

    /**
     * @param string $changeName
     *
     * @return \Doctrine\DBAL\Schema\SchemaException
     */
    static public function alterTableChangeNotSupported($changeName)
    {
        return new self("Alter table change not supported, given '$changeName'");
    }
}
