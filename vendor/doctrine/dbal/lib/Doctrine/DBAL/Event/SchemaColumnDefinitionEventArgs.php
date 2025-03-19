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

namespace Doctrine\DBAL\Event;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;

/**
 * Event Arguments used when the portable column definition is generated inside Doctrine\DBAL\Schema\AbstractSchemaManager.
 *
 * @link   www.doctrine-project.org
 * @since  2.2
 * @author Jan Sorgalla <jsorgalla@googlemail.com>
 */
class SchemaColumnDefinitionEventArgs extends SchemaEventArgs
{
    /**
     * @var \Doctrine\DBAL\Schema\Column|null
     */
    private $_column = null;

    /**
     * Raw column data as fetched from the database.
     *
     * @var array
     */
    private $_tableColumn;

    /**
     * @var string
     */
    private $_table;

    /**
     * @var string
     */
    private $_database;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $_connection;

    /**
     * @param array                     $tableColumn
     * @param string                    $table
     * @param string                    $database
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(array $tableColumn, $table, $database, Connection $connection)
    {
        $this->_tableColumn = $tableColumn;
        $this->_table       = $table;
        $this->_database    = $database;
        $this->_connection  = $connection;
    }

    /**
     * Allows to clear the column which means the column will be excluded from
     * tables column list.
     *
     * @param null|\Doctrine\DBAL\Schema\Column $column
     *
     * @return \Doctrine\DBAL\Event\SchemaColumnDefinitionEventArgs
     */
    public function setColumn(Column $column = null)
    {
        $this->_column = $column;

        return $this;
    }

    /**
     * @return \Doctrine\DBAL\Schema\Column|null
     */
    public function getColumn()
    {
        return $this->_column;
    }

    /**
     * @return array
     */
    public function getTableColumn()
    {
        return $this->_tableColumn;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->_database;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * @return \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    public function getDatabasePlatform()
    {
        return $this->_connection->getDatabasePlatform();
    }
}
