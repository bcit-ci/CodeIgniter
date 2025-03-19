<?php
/*
 *  $Id: $
 *
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
 * Doctrine\DBAL\ConnectionException
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    www.doctrine-project.org
 * @since   2.4
 * @author  Lars Strojny <lars@strojny.net>
 */
class SQLParserUtilsException extends DBALException
{
    /**
     * @param string $paramName
     *
     * @return \Doctrine\DBAL\SQLParserUtilsException
     */
    public static function missingParam($paramName)
    {
        return new self(sprintf('Value for :%1$s not found in params array. Params array key should be "%1$s"', $paramName));
    }

    /**
     * @param string $typeName
     *
     * @return \Doctrine\DBAL\SQLParserUtilsException
     */
    public static function missingType($typeName)
    {
        return new self(sprintf('Value for :%1$s not found in types array. Types array key should be "%1$s"', $typeName));
    }
}
