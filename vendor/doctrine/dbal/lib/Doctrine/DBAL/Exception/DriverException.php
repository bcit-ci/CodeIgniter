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

namespace Doctrine\DBAL\Exception;

use Doctrine\DBAL\DBALException;

/**
 * Base class for all errors detected in the driver.
 *
 * @author Steve Müller <st.mueller@dzh-online.de>
 * @link   www.doctrine-project.org
 * @since  2.5
 */
class DriverException extends DBALException
{
    /**
     * The previous DBAL driver exception.
     *
     * @var \Doctrine\DBAL\Driver\DriverException
     */
    private $driverException;

    /**
     * Constructor.
     *
     * @param string                                $message         The exception message.
     * @param \Doctrine\DBAL\Driver\DriverException $driverException The DBAL driver exception to chain.
     */
    public function __construct($message, \Doctrine\DBAL\Driver\DriverException $driverException)
    {
        $exception = null;

        if ($driverException instanceof \Exception) {
            $exception = $driverException;
        }

        parent::__construct($message, 0, $exception);

        $this->driverException = $driverException;
    }

    /**
     * Returns the driver specific error code if given.
     *
     * Returns null if no error code was given by the driver.
     *
     * @return integer|string|null
     */
    public function getErrorCode()
    {
        return $this->driverException->getErrorCode();
    }

    /**
     * Returns the SQLSTATE the driver was in at the time the error occurred, if given.
     *
     * Returns null if no SQLSTATE was given by the driver.
     *
     * @return string|null
     */
    public function getSQLState()
    {
        return $this->driverException->getSQLState();
    }
}
