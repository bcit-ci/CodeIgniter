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

namespace Doctrine\DBAL\Driver\SQLAnywhere;

use Doctrine\DBAL\Driver\AbstractDriverException;

/**
 * SAP Sybase SQL Anywhere driver exception.
 *
 * @author Steve Müller <st.mueller@dzh-online.de>
 * @link   www.doctrine-project.org
 * @since  2.5
 */
class SQLAnywhereException extends AbstractDriverException
{
    /**
     * Helper method to turn SQL Anywhere error into exception.
     *
     * @param resource|null $conn The SQL Anywhere connection resource to retrieve the last error from.
     * @param resource|null $stmt The SQL Anywhere statement resource to retrieve the last error from.
     *
     * @return SQLAnywhereException
     *
     * @throws \InvalidArgumentException
     */
    public static function fromSQLAnywhereError($conn = null, $stmt = null)
    {
        if (null !== $conn && ! (is_resource($conn) && get_resource_type($conn) === 'SQLAnywhere connection')) {
            throw new \InvalidArgumentException('Invalid SQL Anywhere connection resource given: ' . $conn);
        }

        if (null !== $stmt && ! (is_resource($stmt) && get_resource_type($stmt) === 'SQLAnywhere statement')) {
            throw new \InvalidArgumentException('Invalid SQL Anywhere statement resource given: ' . $stmt);
        }

        $state   = $conn ? sasql_sqlstate($conn) : sasql_sqlstate();
        $code    = null;
        $message = null;

        /**
         * Try retrieving the last error from statement resource if given
         */
        if ($stmt) {
            $code    = sasql_stmt_errno($stmt);
            $message = sasql_stmt_error($stmt);
        }

        /**
         * Try retrieving the last error from the connection resource
         * if either the statement resource is not given or the statement
         * resource is given but the last error could not be retrieved from it (fallback).
         * Depending on the type of error, it is sometimes necessary to retrieve
         * it from the connection resource even though it occurred during
         * a prepared statement.
         */
        if ($conn && ! $code) {
            $code    = sasql_errorcode($conn);
            $message = sasql_error($conn);
        }

        /**
         * Fallback mode if either no connection resource is given
         * or the last error could not be retrieved from the given
         * connection / statement resource.
         */
        if ( ! $conn || ! $code) {
            $code    = sasql_errorcode();
            $message = sasql_error();
        }

        if ($message) {
            return new self('SQLSTATE [' . $state . '] [' . $code . '] ' . $message, $state, $code);
        }

        return new self('SQL Anywhere error occurred but no error message was retrieved from driver.', $state, $code);
    }
}
