<?php

namespace PhpParser;

class Error extends \RuntimeException
{
    protected $rawMessage;
    protected $attributes;

    /**
     * Creates an Exception signifying a parse error.
     *
     * @param string    $message    Error message
     * @param array|int $attributes Attributes of node/token where error occurred
     *                              (or start line of error -- deprecated)
     */
    public function __construct($message, $attributes = array()) {
        $this->rawMessage = (string) $message;
        if (is_array($attributes)) {
            $this->attributes = $attributes;
        } else {
            $this->attributes = array('startLine' => $attributes);
        }
        $this->updateMessage();
    }

    /**
     * Gets the error message
     *
     * @return string Error message
     */
    public function getRawMessage() {
        return $this->rawMessage;
    }

    /**
     * Gets the line the error starts in.
     *
     * @return int Error start line
     */
    public function getStartLine() {
        return isset($this->attributes['startLine']) ? $this->attributes['startLine'] : -1;
    }

    /**
     * Gets the line the error ends in.
     *
     * @return int Error end line
     */
    public function getEndLine() {
        return isset($this->attributes['endLine']) ? $this->attributes['endLine'] : -1;
    }


    /**
     * Gets the attributes of the node/token the error occurred at.
     *
     * @return array
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * Sets the line of the PHP file the error occurred in.
     *
     * @param string $message Error message
     */
    public function setRawMessage($message) {
        $this->rawMessage = (string) $message;
        $this->updateMessage();
    }

    /**
     * Sets the line the error starts in.
     *
     * @param int $line Error start line
     */
    public function setStartLine($line) {
        $this->attributes['startLine'] = (int) $line;
        $this->updateMessage();
    }

    /**
     * Returns whether the error has start and end column information.
     *
     * For column information enable the startFilePos and endFilePos in the lexer options.
     *
     * @return bool
     */
    public function hasColumnInfo() {
        return isset($this->attributes['startFilePos']) && isset($this->attributes['endFilePos']);
    }

    /**
     * Gets the start column (1-based) into the line where the error started.
     *
     * @param string $code Source code of the file
     * @return int
     */
    public function getStartColumn($code) {
        if (!$this->hasColumnInfo()) {
            throw new \RuntimeException('Error does not have column information');
        }

        return $this->toColumn($code, $this->attributes['startFilePos']);
    }

    /**
     * Gets the end column (1-based) into the line where the error ended.
     *
     * @param string $code Source code of the file
     * @return int
     */
    public function getEndColumn($code) {
        if (!$this->hasColumnInfo()) {
            throw new \RuntimeException('Error does not have column information');
        }

        return $this->toColumn($code, $this->attributes['endFilePos']);
    }

    private function toColumn($code, $pos) {
        if ($pos > strlen($code)) {
            throw new \RuntimeException('Invalid position information');
        }

        $lineStartPos = strrpos($code, "\n", $pos - strlen($code));
        if (false === $lineStartPos) {
            $lineStartPos = -1;
        }

        return $pos - $lineStartPos;
    }

    /**
     * Updates the exception message after a change to rawMessage or rawLine.
     */
    protected function updateMessage() {
        $this->message = $this->rawMessage;

        if (-1 === $this->getStartLine()) {
            $this->message .= ' on unknown line';
        } else {
            $this->message .= ' on line ' . $this->getStartLine();
        }
    }

    /** @deprecated Use getStartLine() instead */
    public function getRawLine() {
        return $this->getStartLine();
    }

    /** @deprecated Use setStartLine() instead */
    public function setRawLine($line) {
        $this->setStartLine($line);
    }
}
