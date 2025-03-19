Error handling
==============

Errors during parsing or analysis are represented using the `PhpParser\Error` exception class. In addition to an error
message, an error can also store additional information about the location the error occurred at.

How much location information is available depends on the origin of the error and how many lexer attributes have been
enabled. At a minimum the start line of the error is usually available.

Column information
------------------

In order to receive information about not only the line, but also the column span an error occurred at, the file
position attributes in the lexer need to be enabled:

```php
$lexer = new PhpParser\Lexer(array(
    'usedAttributes' => array('comments', 'startLine', 'endLine', 'startFilePos', 'endFilePos'),
));
$parser = (new PhpParser\ParserFactory)->create(PhpParser\ParserFactory::PREFER_PHP7, $lexer);

try {
    $stmts = $parser->parse($code);
    // ...
} catch (PhpParser\Error $e) {
    // ...
}
```

Before using column information its availability needs to be checked with `$e->hasColumnInfo()`, as the precise
location of an error cannot always be determined. The methods for retrieving column information also have to be passed
the source code of the parsed file. An example for printing an error:

```php
if ($e->hasColumnInfo()) {
    echo $e->getRawMessage() . ' from ' . $e->getStartLine() . ':' . $e->getStartColumn($code)
        . ' to ' . $e->getEndLine() . ':' . $e->getEndColumn($code);
} else {
    echo $e->getMessage();
}
```

Both line numbers and column numbers are 1-based. EOF errors will be located at the position one past the end of the
file.

Error recovery
--------------

> **EXPERIMENTAL**

By default the parser will throw an exception upon encountering the first error during parsing. An alternative mode is
also supported, in which the parser will remember the error, but try to continue parsing the rest of the source code.

To enable this mode the `throwOnError` parser option needs to be disabled. Any errors that occurred during parsing can
then be retrieved using `$parser->getErrors()`. The `$parser->parse()` method will either return a partial syntax tree
or `null` if recovery fails.

A usage example:

```php
$parser = (new PhpParser\ParserFactory)->create(PhpParser\ParserFactory::PREFER_PHP7, null, array(
    'throwOnError' => false,
));

$stmts = $parser->parse($code);
$errors = $parser->getErrors();

foreach ($errors as $error) {
    // $error is an ordinary PhpParser\Error
}

if (null !== $stmts) {
    // $stmts is a best-effort partial AST
}
```

The error recovery implementation is experimental -- it currently won't be able to recover from many types of errors.
