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
 * Oracle Keywordlist.
 *
 * @link   www.doctrine-project.org
 * @since  2.0
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @author David Coallier <davidc@php.net>
 */
class OracleKeywords extends KeywordList
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Oracle';
    }

    /**
     * {@inheritdoc}
     */
    protected function getKeywords()
    {
        return array(
            'ACCESS',
            'ELSE',
            'MODIFY',
            'START',
            'ADD',
            'EXCLUSIVE',
            'NOAUDIT',
            'SELECT',
            'ALL',
            'EXISTS',
            'NOCOMPRESS',
            'SESSION',
            'ALTER',
            'FILE',
            'NOT',
            'SET',
            'AND',
            'FLOAT',
            'NOTFOUND ',
            'SHARE',
            'ANY',
            'FOR',
            'NOWAIT',
            'SIZE',
            'ARRAYLEN',
            'FROM',
            'NULL',
            'SMALLINT',
            'AS',
            'GRANT',
            'NUMBER',
            'SQLBUF',
            'ASC',
            'GROUP',
            'OF',
            'SUCCESSFUL',
            'AUDIT',
            'HAVING',
            'OFFLINE ',
            'SYNONYM',
            'BETWEEN',
            'IDENTIFIED',
            'ON',
            'SYSDATE',
            'BY',
            'IMMEDIATE',
            'ONLINE',
            'TABLE',
            'CHAR',
            'IN',
            'OPTION',
            'THEN',
            'CHECK',
            'INCREMENT',
            'OR',
            'TO',
            'CLUSTER',
            'INDEX',
            'ORDER',
            'TRIGGER',
            'COLUMN',
            'INITIAL',
            'PCTFREE',
            'UID',
            'COMMENT',
            'INSERT',
            'PRIOR',
            'UNION',
            'COMPRESS',
            'INTEGER',
            'PRIVILEGES',
            'UNIQUE',
            'CONNECT',
            'INTERSECT',
            'PUBLIC',
            'UPDATE',
            'CREATE',
            'INTO',
            'RAW',
            'USER',
            'CURRENT',
            'IS',
            'RENAME',
            'VALIDATE',
            'DATE',
            'LEVEL',
            'RESOURCE',
            'VALUES',
            'DECIMAL',
            'LIKE',
            'REVOKE',
            'VARCHAR',
            'DEFAULT',
            'LOCK',
            'ROW',
            'VARCHAR2',
            'DELETE',
            'LONG',
            'ROWID',
            'VIEW',
            'DESC',
            'MAXEXTENTS',
            'ROWLABEL',
            'WHENEVER',
            'DISTINCT',
            'MINUS',
            'ROWNUM',
            'WHERE',
            'DROP',
            'MODE',
            'ROWS',
            'WITH',
            'RANGE',
        );
    }
}
