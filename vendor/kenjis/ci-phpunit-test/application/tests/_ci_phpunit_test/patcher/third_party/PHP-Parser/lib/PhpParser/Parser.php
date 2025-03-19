<?php

namespace PhpParser;

interface Parser {
    /**
     * Parses PHP code into a node tree.
     *
     * @param string $code The source code to parse
     *
     * @return Node[]|null Array of statements (or null if the 'throwOnError' option is disabled and the parser was
     *                     unable to recover from an error).
     */
    public function parse($code);

    /**
     * Get array of errors that occurred during the last parse.
     *
     * This method may only return multiple errors if the 'throwOnError' option is disabled.
     *
     * @return Error[]
     */
    public function getErrors();
}
