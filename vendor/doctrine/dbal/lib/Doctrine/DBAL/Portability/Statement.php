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

namespace Doctrine\DBAL\Portability;

use PDO;

/**
 * Portability wrapper for a Statement.
 *
 * @link   www.doctrine-project.org
 * @since  2.0
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class Statement implements \IteratorAggregate, \Doctrine\DBAL\Driver\Statement
{
    /**
     * @var integer
     */
    private $portability;

    /**
     * @var \Doctrine\DBAL\Driver\Statement
     */
    private $stmt;

    /**
     * @var integer
     */
    private $case;

    /**
     * @var integer
     */
    private $defaultFetchMode = PDO::FETCH_BOTH;

    /**
     * Wraps <tt>Statement</tt> and applies portability measures.
     *
     * @param \Doctrine\DBAL\Driver\Statement       $stmt
     * @param \Doctrine\DBAL\Portability\Connection $conn
     */
    public function __construct($stmt, Connection $conn)
    {
        $this->stmt = $stmt;
        $this->portability = $conn->getPortability();
        $this->case = $conn->getFetchCase();
    }

    /**
     * {@inheritdoc}
     */
    public function bindParam($column, &$variable, $type = null, $length = null)
    {
        return $this->stmt->bindParam($column, $variable, $type, $length);
    }
    /**
     * {@inheritdoc}
     */

    public function bindValue($param, $value, $type = null)
    {
        return $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function closeCursor()
    {
        return $this->stmt->closeCursor();
    }

    /**
     * {@inheritdoc}
     */
    public function columnCount()
    {
        return $this->stmt->columnCount();
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode()
    {
        return $this->stmt->errorCode();
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
        return $this->stmt->errorInfo();
    }

    /**
     * {@inheritdoc}
     */
    public function execute($params = null)
    {
        return $this->stmt->execute($params);
    }

    /**
     * {@inheritdoc}
     */
    public function setFetchMode($fetchMode, $arg1 = null, $arg2 = null)
    {
        $this->defaultFetchMode = $fetchMode;

        return $this->stmt->setFetchMode($fetchMode, $arg1, $arg2);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $data = $this->fetchAll();

        return new \ArrayIterator($data);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($fetchMode = null)
    {
        $fetchMode = $fetchMode ?: $this->defaultFetchMode;

        $row = $this->stmt->fetch($fetchMode);

        $row = $this->fixRow($row,
            $this->portability & (Connection::PORTABILITY_EMPTY_TO_NULL|Connection::PORTABILITY_RTRIM),
            !is_null($this->case) && ($fetchMode == PDO::FETCH_ASSOC || $fetchMode == PDO::FETCH_BOTH) && ($this->portability & Connection::PORTABILITY_FIX_CASE)
        );

        return $row;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($fetchMode = null, $columnIndex = 0)
    {
        $fetchMode = $fetchMode ?: $this->defaultFetchMode;

        if ($columnIndex != 0) {
            $rows = $this->stmt->fetchAll($fetchMode, $columnIndex);
        } else {
            $rows = $this->stmt->fetchAll($fetchMode);
        }

        $iterateRow = $this->portability & (Connection::PORTABILITY_EMPTY_TO_NULL|Connection::PORTABILITY_RTRIM);
        $fixCase = !is_null($this->case) && ($fetchMode == PDO::FETCH_ASSOC || $fetchMode == PDO::FETCH_BOTH) && ($this->portability & Connection::PORTABILITY_FIX_CASE);
        if ( ! $iterateRow && !$fixCase) {
            return $rows;
        }

        foreach ($rows as $num => $row) {
            $rows[$num] = $this->fixRow($row, $iterateRow, $fixCase);
        }

        return $rows;
    }

    /**
     * @param mixed   $row
     * @param integer $iterateRow
     * @param boolean $fixCase
     *
     * @return array
     */
    protected function fixRow($row, $iterateRow, $fixCase)
    {
        if ( ! $row) {
            return $row;
        }

        if ($fixCase) {
            $row = array_change_key_case($row, $this->case);
        }

        if ($iterateRow) {
            foreach ($row as $k => $v) {
                if (($this->portability & Connection::PORTABILITY_EMPTY_TO_NULL) && $v === '') {
                    $row[$k] = null;
                } elseif (($this->portability & Connection::PORTABILITY_RTRIM) && is_string($v)) {
                    $row[$k] = rtrim($v);
                }
            }
        }

        return $row;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($columnIndex = 0)
    {
        $value = $this->stmt->fetchColumn($columnIndex);

        if ($this->portability & (Connection::PORTABILITY_EMPTY_TO_NULL|Connection::PORTABILITY_RTRIM)) {
            if (($this->portability & Connection::PORTABILITY_EMPTY_TO_NULL) && $value === '') {
                $value = null;
            } elseif (($this->portability & Connection::PORTABILITY_RTRIM) && is_string($value)) {
                $value = rtrim($value);
            }
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
}
