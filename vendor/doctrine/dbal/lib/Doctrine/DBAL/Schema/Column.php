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

use Doctrine\DBAL\Types\Type;

/**
 * Object representation of a database column.
 *
 * @link   www.doctrine-project.org
 * @since  2.0
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class Column extends AbstractAsset
{
    /**
     * @var Type
     */
    protected $_type;

    /**
     * @var integer|null
     */
    protected $_length = null;

    /**
     * @var integer
     */
    protected $_precision = 10;

    /**
     * @var integer
     */
    protected $_scale = 0;

    /**
     * @var boolean
     */
    protected $_unsigned = false;

    /**
     * @var boolean
     */
    protected $_fixed = false;

    /**
     * @var boolean
     */
    protected $_notnull = true;

    /**
     * @var string|null
     */
    protected $_default = null;

    /**
     * @var boolean
     */
    protected $_autoincrement = false;

    /**
     * @var array
     */
    protected $_platformOptions = array();

    /**
     * @var string|null
     */
    protected $_columnDefinition = null;

    /**
     * @var string|null
     */
    protected $_comment = null;

    /**
     * @var array
     */
    protected $_customSchemaOptions = array();

    /**
     * Creates a new Column.
     *
     * @param string $columnName
     * @param Type   $type
     * @param array  $options
     */
    public function __construct($columnName, Type $type, array $options=array())
    {
        $this->_setName($columnName);
        $this->setType($type);
        $this->setOptions($options);
    }

    /**
     * @param array $options
     *
     * @return Column
     */
    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            $method = "set".$name;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * @param Type $type
     *
     * @return Column
     */
    public function setType(Type $type)
    {
        $this->_type = $type;

        return $this;
    }

    /**
     * @param integer|null $length
     *
     * @return Column
     */
    public function setLength($length)
    {
        if ($length !== null) {
            $this->_length = (int) $length;
        } else {
            $this->_length = null;
        }

        return $this;
    }

    /**
     * @param integer $precision
     *
     * @return Column
     */
    public function setPrecision($precision)
    {
        if (!is_numeric($precision)) {
            $precision = 10; // defaults to 10 when no valid precision is given.
        }

        $this->_precision = (int) $precision;

        return $this;
    }

    /**
     * @param integer $scale
     *
     * @return Column
     */
    public function setScale($scale)
    {
        if (!is_numeric($scale)) {
            $scale = 0;
        }

        $this->_scale = (int) $scale;

        return $this;
    }

    /**
     * @param boolean $unsigned
     *
     * @return Column
     */
    public function setUnsigned($unsigned)
    {
        $this->_unsigned = (bool) $unsigned;

        return $this;
    }

    /**
     * @param boolean $fixed
     *
     * @return Column
     */
    public function setFixed($fixed)
    {
        $this->_fixed = (bool) $fixed;

        return $this;
    }

    /**
     * @param boolean $notnull
     *
     * @return Column
     */
    public function setNotnull($notnull)
    {
        $this->_notnull = (bool) $notnull;

        return $this;
    }

    /**
     * @param mixed $default
     *
     * @return Column
     */
    public function setDefault($default)
    {
        $this->_default = $default;

        return $this;
    }

    /**
     * @param array $platformOptions
     *
     * @return Column
     */
    public function setPlatformOptions(array $platformOptions)
    {
        $this->_platformOptions = $platformOptions;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return Column
     */
    public function setPlatformOption($name, $value)
    {
        $this->_platformOptions[$name] = $value;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return Column
     */
    public function setColumnDefinition($value)
    {
        $this->_columnDefinition = $value;

        return $this;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return integer|null
     */
    public function getLength()
    {
        return $this->_length;
    }

    /**
     * @return integer
     */
    public function getPrecision()
    {
        return $this->_precision;
    }

    /**
     * @return integer
     */
    public function getScale()
    {
        return $this->_scale;
    }

    /**
     * @return boolean
     */
    public function getUnsigned()
    {
        return $this->_unsigned;
    }

    /**
     * @return boolean
     */
    public function getFixed()
    {
        return $this->_fixed;
    }

    /**
     * @return boolean
     */
    public function getNotnull()
    {
        return $this->_notnull;
    }

    /**
     * @return string|null
     */
    public function getDefault()
    {
        return $this->_default;
    }

    /**
     * @return array
     */
    public function getPlatformOptions()
    {
        return $this->_platformOptions;
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function hasPlatformOption($name)
    {
        return isset($this->_platformOptions[$name]);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getPlatformOption($name)
    {
        return $this->_platformOptions[$name];
    }

    /**
     * @return string|null
     */
    public function getColumnDefinition()
    {
        return $this->_columnDefinition;
    }

    /**
     * @return boolean
     */
    public function getAutoincrement()
    {
        return $this->_autoincrement;
    }

    /**
     * @param boolean $flag
     *
     * @return Column
     */
    public function setAutoincrement($flag)
    {
        $this->_autoincrement = $flag;

        return $this;
    }

    /**
     * @param string $comment
     *
     * @return Column
     */
    public function setComment($comment)
    {
        $this->_comment = $comment;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment()
    {
        return $this->_comment;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return Column
     */
    public function setCustomSchemaOption($name, $value)
    {
        $this->_customSchemaOptions[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function hasCustomSchemaOption($name)
    {
        return isset($this->_customSchemaOptions[$name]);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getCustomSchemaOption($name)
    {
        return $this->_customSchemaOptions[$name];
    }

    /**
     * @param array $customSchemaOptions
     *
     * @return Column
     */
    public function setCustomSchemaOptions(array $customSchemaOptions)
    {
        $this->_customSchemaOptions = $customSchemaOptions;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomSchemaOptions()
    {
        return $this->_customSchemaOptions;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(array(
            'name'          => $this->_name,
            'type'          => $this->_type,
            'default'       => $this->_default,
            'notnull'       => $this->_notnull,
            'length'        => $this->_length,
            'precision'     => $this->_precision,
            'scale'         => $this->_scale,
            'fixed'         => $this->_fixed,
            'unsigned'      => $this->_unsigned,
            'autoincrement' => $this->_autoincrement,
            'columnDefinition' => $this->_columnDefinition,
            'comment' => $this->_comment,
        ), $this->_platformOptions, $this->_customSchemaOptions);
    }
}
