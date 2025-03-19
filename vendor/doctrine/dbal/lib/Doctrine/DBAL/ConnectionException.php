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

namespace Doctrine\DBAL;

/**
 * @link   www.doctrine-project.org
 * @since  2.0
 * @author Jonathan H. Wage <jonwage@gmail.com
 */
class ConnectionException extends DBALException
{
    /**
     * @return \Doctrine\DBAL\ConnectionException
     */
    public static function commitFailedRollbackOnly()
    {
        return new self("Transaction commit failed because the transaction has been marked for rollback only.");
    }

    /**
     * @return \Doctrine\DBAL\ConnectionException
     */
    public static function noActiveTransaction()
    {
        return new self("There is no active transaction.");
    }

    /**
     * @return \Doctrine\DBAL\ConnectionException
     */
    public static function savepointsNotSupported()
    {
        return new self("Savepoints are not supported by this driver.");
    }

    /**
     * @return \Doctrine\DBAL\ConnectionException
     */
    public static function mayNotAlterNestedTransactionWithSavepointsInTransaction()
    {
        return new self("May not alter the nested transaction with savepoints behavior while a transaction is open.");
    }
}
