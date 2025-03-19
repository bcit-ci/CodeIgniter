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

namespace Doctrine\DBAL\Platforms\Keywords;

/**
 * Microsoft SQL Server 2000 reserved keyword dictionary.
 *
 * @license BSD http://www.opensource.org/licenses/bsd-license.php
 * @link    www.doctrine-project.com
 * @since   2.0
 * @author  Benjamin Eberlei <kontakt@beberlei.de>
 * @author  David Coallier <davidc@php.net>
 * @author  Steve Müller <st.mueller@dzh-online.de>
 */
class SQLServerKeywords extends KeywordList
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'SQLServer';
    }

    /**
     * {@inheritdoc}
     *
     * @link http://msdn.microsoft.com/en-us/library/aa238507%28v=sql.80%29.aspx
     */
    protected function getKeywords()
    {
        return array(
            'ADD',
            'ALL',
            'ALTER',
            'AND',
            'ANY',
            'AS',
            'ASC',
            'AUTHORIZATION',
            'BACKUP',
            'BEGIN',
            'BETWEEN',
            'BREAK',
            'BROWSE',
            'BULK',
            'BY',
            'CASCADE',
            'CASE',
            'CHECK',
            'CHECKPOINT',
            'CLOSE',
            'CLUSTERED',
            'COALESCE',
            'COLLATE',
            'COLUMN',
            'COMMIT',
            'COMPUTE',
            'CONSTRAINT',
            'CONTAINS',
            'CONTAINSTABLE',
            'CONTINUE',
            'CONVERT',
            'CREATE',
            'CROSS',
            'CURRENT',
            'CURRENT_DATE',
            'CURRENT_TIME',
            'CURRENT_TIMESTAMP',
            'CURRENT_USER',
            'CURSOR',
            'DATABASE',
            'DBCC',
            'DEALLOCATE',
            'DECLARE',
            'DEFAULT',
            'DELETE',
            'DENY',
            'DESC',
            'DISK',
            'DISTINCT',
            'DISTRIBUTED',
            'DOUBLE',
            'DROP',
            'DUMP',
            'ELSE',
            'END',
            'ERRLVL',
            'ESCAPE',
            'EXCEPT',
            'EXEC',
            'EXECUTE',
            'EXISTS',
            'EXIT',
            'EXTERNAL',
            'FETCH',
            'FILE',
            'FILLFACTOR',
            'FOR',
            'FOREIGN',
            'FREETEXT',
            'FREETEXTTABLE',
            'FROM',
            'FULL',
            'FUNCTION',
            'GOTO',
            'GRANT',
            'GROUP',
            'HAVING',
            'HOLDLOCK',
            'IDENTITY',
            'IDENTITY_INSERT',
            'IDENTITYCOL',
            'IF',
            'IN',
            'INDEX',
            'INNER',
            'INSERT',
            'INTERSECT',
            'INTO',
            'IS',
            'JOIN',
            'KEY',
            'KILL',
            'LEFT',
            'LIKE',
            'LINENO',
            'LOAD',
            'NATIONAL',
            'NOCHECK ',
            'NONCLUSTERED',
            'NOT',
            'NULL',
            'NULLIF',
            'OF',
            'OFF',
            'OFFSETS',
            'ON',
            'OPEN',
            'OPENDATASOURCE',
            'OPENQUERY',
            'OPENROWSET',
            'OPENXML',
            'OPTION',
            'OR',
            'ORDER',
            'OUTER',
            'OVER',
            'PERCENT',
            'PIVOT',
            'PLAN',
            'PRECISION',
            'PRIMARY',
            'PRINT',
            'PROC',
            'PROCEDURE',
            'PUBLIC',
            'RAISERROR',
            'READ',
            'READTEXT',
            'RECONFIGURE',
            'REFERENCES',
            'REPLICATION',
            'RESTORE',
            'RESTRICT',
            'RETURN',
            'REVERT',
            'REVOKE',
            'RIGHT',
            'ROLLBACK',
            'ROWCOUNT',
            'ROWGUIDCOL',
            'RULE',
            'SAVE',
            'SCHEMA',
            'SECURITYAUDIT',
            'SELECT',
            'SESSION_USER',
            'SET',
            'SETUSER',
            'SHUTDOWN',
            'SOME',
            'STATISTICS',
            'SYSTEM_USER',
            'TABLE',
            'TABLESAMPLE',
            'TEXTSIZE',
            'THEN',
            'TO',
            'TOP',
            'TRAN',
            'TRANSACTION',
            'TRIGGER',
            'TRUNCATE',
            'TSEQUAL',
            'UNION',
            'UNIQUE',
            'UNPIVOT',
            'UPDATE',
            'UPDATETEXT',
            'USE',
            'USER',
            'VALUES',
            'VARYING',
            'VIEW',
            'WAITFOR',
            'WHEN',
            'WHERE',
            'WHILE',
            'WITH',
            'WRITETEXT'
        );
    }
}
