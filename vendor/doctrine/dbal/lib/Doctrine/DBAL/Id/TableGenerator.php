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

namespace Doctrine\DBAL\Id;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;

/**
 * Table ID Generator for those poor languages that are missing sequences.
 *
 * WARNING: The Table Id Generator clones a second independent database
 * connection to work correctly. This means using the generator requests that
 * generate IDs will have two open database connections. This is necessary to
 * be safe from transaction failures in the main connection. Make sure to only
 * ever use one TableGenerator otherwise you end up with many connections.
 *
 * TableID Generator does not work with SQLite.
 *
 * The TableGenerator does not take care of creating the SQL Table itself. You
 * should look at the `TableGeneratorSchemaVisitor` to do this for you.
 * Otherwise the schema for a table looks like:
 *
 * CREATE sequences (
 *   sequence_name VARCHAR(255) NOT NULL,
 *   sequence_value INT NOT NULL DEFAULT '1',
 *   sequence_increment_by INT NOT NULL DEFAULT '1',
 *   PRIMARY KEY (table_name)
 * );
 *
 * Technically this generator works as follows:
 *
 * 1. Use a robust transaction serialization level.
 * 2. Open transaction
 * 3. Acquire a read lock on the table row (SELECT .. FOR UPDATE)
 * 4. Increment current value by one and write back to database
 * 5. Commit transaction
 *
 * If you are using a sequence_increment_by value that is larger than one the
 * ID Generator will keep incrementing values until it hits the incrementation
 * gap before issuing another query.
 *
 * If no row is present for a given sequence a new one will be created with the
 * default values 'value' = 1 and 'increment_by' = 1
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class TableGenerator
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $conn;

    /**
     * @var string
     */
    private $generatorTableName;

    /**
     * @var array
     */
    private $sequences = array();

    /**
     * @param \Doctrine\DBAL\Connection $conn
     * @param string                    $generatorTableName
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct(Connection $conn, $generatorTableName = 'sequences')
    {
        $params = $conn->getParams();
        if ($params['driver'] == 'pdo_sqlite') {
            throw new \Doctrine\DBAL\DBALException("Cannot use TableGenerator with SQLite.");
        }
        $this->conn = DriverManager::getConnection($params, $conn->getConfiguration(), $conn->getEventManager());
        $this->generatorTableName = $generatorTableName;
    }

    /**
     * Generates the next unused value for the given sequence name.
     *
     * @param string $sequenceName
     *
     * @return integer
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function nextValue($sequenceName)
    {
        if (isset($this->sequences[$sequenceName])) {
            $value = $this->sequences[$sequenceName]['value'];
            $this->sequences[$sequenceName]['value']++;
            if ($this->sequences[$sequenceName]['value'] >= $this->sequences[$sequenceName]['max']) {
                unset ($this->sequences[$sequenceName]);
            }

            return $value;
        }

        $this->conn->beginTransaction();

        try {
            $platform = $this->conn->getDatabasePlatform();
            $sql = "SELECT sequence_value, sequence_increment_by " .
                   "FROM " . $platform->appendLockHint($this->generatorTableName, \Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE) . " " .
                   "WHERE sequence_name = ? " . $platform->getWriteLockSQL();
            $stmt = $this->conn->executeQuery($sql, array($sequenceName));

            if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $row = array_change_key_case($row, CASE_LOWER);

                $value = $row['sequence_value'];
                $value++;

                if ($row['sequence_increment_by'] > 1) {
                    $this->sequences[$sequenceName] = array(
                        'value' => $value,
                        'max' => $row['sequence_value'] + $row['sequence_increment_by']
                    );
                }

                $sql = "UPDATE " . $this->generatorTableName . " ".
                       "SET sequence_value = sequence_value + sequence_increment_by " .
                       "WHERE sequence_name = ? AND sequence_value = ?";
                $rows = $this->conn->executeUpdate($sql, array($sequenceName, $row['sequence_value']));

                if ($rows != 1) {
                    throw new \Doctrine\DBAL\DBALException("Race-condition detected while updating sequence. Aborting generation");
                }
            } else {
                $this->conn->insert(
                    $this->generatorTableName,
                    array('sequence_name' => $sequenceName, 'sequence_value' => 1, 'sequence_increment_by' => 1)
                );
                $value = 1;
            }

            $this->conn->commit();

        } catch (\Exception $e) {
            $this->conn->rollback();
            throw new \Doctrine\DBAL\DBALException("Error occurred while generating ID with TableGenerator, aborted generation: " . $e->getMessage(), 0, $e);
        }

        return $value;
    }
}
