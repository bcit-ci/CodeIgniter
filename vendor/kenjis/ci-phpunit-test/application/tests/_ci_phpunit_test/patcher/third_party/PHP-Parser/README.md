PHP Parser
==========

[![Build Status](https://travis-ci.org/nikic/PHP-Parser.svg?branch=master)](https://travis-ci.org/nikic/PHP-Parser) [![Coverage Status](https://coveralls.io/repos/github/nikic/PHP-Parser/badge.svg?branch=master)](https://coveralls.io/github/nikic/PHP-Parser?branch=master)

This is a PHP 5.2 to PHP 7.0 parser written in PHP. Its purpose is to simplify static code analysis and
manipulation.

[**Documentation for version 2.x**][doc_master] (stable; for running on PHP >= 5.4; for parsing PHP 5.2 to PHP 7.0).

[Documentation for version 1.x][doc_1_x] (stable; for running on PHP >= 5.3; for parsing PHP 5.2 to PHP 5.6).

In a Nutshell
-------------

The parser turns PHP source code into an abstract syntax tree. For example, if you pass the following code into the
parser:

```php
<?php
echo 'Hi', 'World';
hello\world('foo', 'bar' . 'baz');
```

You'll get a syntax tree looking roughly like this:

```php
array(
    0: Stmt_Echo(
        exprs: array(
            0: Scalar_String(
                value: Hi
            )
            1: Scalar_String(
                value: World
            )
        )
    )
    1: Expr_FuncCall(
        name: Name(
            parts: array(
                0: hello
                1: world
            )
        )
        args: array(
            0: Arg(
                value: Scalar_String(
                    value: foo
                )
                byRef: false
            )
            1: Arg(
                value: Expr_Concat(
                    left: Scalar_String(
                        value: bar
                    )
                    right: Scalar_String(
                        value: baz
                    )
                )
                byRef: false
            )
        )
    )
)
```

You can then work with this syntax tree, for example to statically analyze the code (e.g. to find
programming errors or security issues).

Additionally, you can convert a syntax tree back to PHP code. This allows you to do code preprocessing
(like automatedly porting code to older PHP versions).

Installation
------------

The preferred installation method is [composer](https://getcomposer.org):

    php composer.phar require nikic/php-parser

Documentation
-------------

 1. [Introduction](doc/0_Introduction.markdown)
 2. [Usage of basic components](doc/2_Usage_of_basic_components.markdown)
 3. [Other node tree representations](doc/3_Other_node_tree_representations.markdown)
 4. [Code generation](doc/4_Code_generation.markdown)

Component documentation:

 1. [Error](doc/component/Error.markdown)
 2. [Lexer](doc/component/Lexer.markdown)

 [doc_1_x]: https://github.com/nikic/PHP-Parser/tree/1.x/doc
 [doc_master]: https://github.com/nikic/PHP-Parser/tree/master/doc
