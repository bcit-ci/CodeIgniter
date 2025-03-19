<?php

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class Name extends NodeAbstract
{
    /** @var string[] Parts of the name */
    public $parts;

    /**
     * Constructs a name node.
     *
     * @param string|array $parts      Parts of the name (or name as string)
     * @param array        $attributes Additional attributes
     */
    public function __construct($parts, array $attributes = array()) {
        if (!is_array($parts)) {
            $parts = explode('\\', $parts);
        }

        parent::__construct($attributes);
        $this->parts = $parts;
    }

    public function getSubNodeNames() {
        return array('parts');
    }

    /**
     * Gets the first part of the name, i.e. everything before the first namespace separator.
     *
     * @return string First part of the name
     */
    public function getFirst() {
        return $this->parts[0];
    }

    /**
     * Gets the last part of the name, i.e. everything after the last namespace separator.
     *
     * @return string Last part of the name
     */
    public function getLast() {
        return $this->parts[count($this->parts) - 1];
    }

    /**
     * Checks whether the name is unqualified. (E.g. Name)
     *
     * @return bool Whether the name is unqualified
     */
    public function isUnqualified() {
        return 1 == count($this->parts);
    }

    /**
     * Checks whether the name is qualified. (E.g. Name\Name)
     *
     * @return bool Whether the name is qualified
     */
    public function isQualified() {
        return 1 < count($this->parts);
    }

    /**
     * Checks whether the name is fully qualified. (E.g. \Name)
     *
     * @return bool Whether the name is fully qualified
     */
    public function isFullyQualified() {
        return false;
    }

    /**
     * Checks whether the name is explicitly relative to the current namespace. (E.g. namespace\Name)
     *
     * @return bool Whether the name is relative
     */
    public function isRelative() {
        return false;
    }

    /**
     * Returns a string representation of the name by imploding the namespace parts with a separator.
     *
     * @param string $separator The separator to use (defaults to the namespace separator \)
     *
     * @return string String representation
     */
    public function toString($separator = '\\') {
        return implode($separator, $this->parts);
    }

    /**
     * Returns a string representation of the name by imploding the namespace parts with the
     * namespace separator.
     *
     * @return string String representation
     */
    public function __toString() {
        return implode('\\', $this->parts);
    }

    /**
     * Sets the whole name.
     *
     * @deprecated Create a new Name instead, or manually modify the $parts property
     *
     * @param string|array|self $name The name to set the whole name to
     */
    public function set($name) {
        $this->parts = self::prepareName($name);
    }

    /**
     * Prepends a name to this name.
     *
     * @deprecated Use Name::concat($name1, $name2) instead
     *
     * @param string|array|self $name Name to prepend
     */
    public function prepend($name) {
        $this->parts = array_merge(self::prepareName($name), $this->parts);
    }

    /**
     * Appends a name to this name.
     *
     * @deprecated Use Name::concat($name1, $name2) instead
     *
     * @param string|array|self $name Name to append
     */
    public function append($name) {
        $this->parts = array_merge($this->parts, self::prepareName($name));
    }

    /**
     * Sets the first part of the name.
     *
     * @deprecated Use concat($first, $name->slice(1)) instead
     *
     * @param string|array|self $name The name to set the first part to
     */
    public function setFirst($name) {
        array_splice($this->parts, 0, 1, self::prepareName($name));
    }

    /**
     * Sets the last part of the name.
     *
     * @param string|array|self $name The name to set the last part to
     */
    public function setLast($name) {
        array_splice($this->parts, -1, 1, self::prepareName($name));
    }

    /**
     * Gets a slice of a name (similar to array_slice).
     *
     * This method returns a new instance of the same type as the original and with the same
     * attributes.
     *
     * If the slice is empty, a Name with an empty parts array is returned. While this is
     * meaningless in itself, it works correctly in conjunction with concat().
     *
     * @param int $offset Offset to start the slice at
     *
     * @return static Sliced name
     */
    public function slice($offset) {
        // TODO negative offset and length
        if ($offset < 0 || $offset > count($this->parts)) {
            throw new \OutOfBoundsException(sprintf('Offset %d is out of bounds', $offset));
        }

        return new static(array_slice($this->parts, $offset), $this->attributes);
    }

    /**
     * Concatenate two names, yielding a new Name instance.
     *
     * The type of the generated instance depends on which class this method is called on, for
     * example Name\FullyQualified::concat() will yield a Name\FullyQualified instance.
     *
     * @param string|array|self $name1      The first name
     * @param string|array|self $name2      The second name
     * @param array             $attributes Attributes to assign to concatenated name
     *
     * @return static Concatenated name
     */
    public static function concat($name1, $name2, array $attributes = []) {
        return new static(
            array_merge(self::prepareName($name1), self::prepareName($name2)), $attributes
        );
    }

    /**
     * Prepares a (string, array or Name node) name for use in name changing methods by converting
     * it to an array.
     *
     * @param string|array|self $name Name to prepare
     *
     * @return array Prepared name
     */
    private static function prepareName($name) {
        if (is_string($name)) {
            return explode('\\', $name);
        } elseif (is_array($name)) {
            return $name;
        } elseif ($name instanceof self) {
            return $name->parts;
        }

        throw new \InvalidArgumentException(
            'When changing a name you need to pass either a string, an array or a Name node'
        );
    }
}
