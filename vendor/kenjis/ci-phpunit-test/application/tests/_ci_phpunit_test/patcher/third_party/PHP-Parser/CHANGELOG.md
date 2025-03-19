Version 2.0.2-dev
-----------------

Nothing yet.

Version 2.0.1 (2016-02-28)
--------------------------

### Fixed

* `declare() {}` and `declare();` are not semantically equivalent and will now result in different
  ASTs. The format case will have an empty `stmts` array, while the latter will set `stmts` to
  `null`.
* Magic constants are now supported as semi-reserved keywords.
* A shebang line like `#!/usr/bin/env php` is now allowed at the start of a namespaced file.
  Previously this generated an exception.
* The `prettyPrintFile()` method will not strip a trailing `?>` from the raw data that follows a
  `__halt_compiler()` statement.
* The `prettyPrintFile()` method will not strip an opening `<?php` if the file starts with a
  comment followed by InlineHTML.

Version 2.0.0 (2015-12-04)
--------------------------

### Changed

* String parts of encapsed strings are now represented using `Scalar\EncapsStringPart` nodes.
  Previously raw strings were used. This affects the `parts` child of `Scalar\Encaps` and
  `Expr\ShellExec`. The change has been done to allow assignment of attributes to encapsed string
  parts.

Version 2.0.0-beta1 (2015-10-21)
--------------------------------

### Fixed

* Fixed issue with too many newlines being stripped at the end of heredoc/nowdoc strings in some
  cases. (#227)

### Changed

* Update group use support to be in line with recent PHP 7.0 builds.
* Renamed `php-parse.php` to `php-parse` and registered it as a composer bin.
* Use composer PSR-4 autoloader instead of custom autoloader.
* Specify phpunit as a dev dependency.

### Added

* Added `shortArraySyntax` option to pretty printer, to print all arrays using short syntax.

Version 2.0.0-alpha1 (2015-07-14)
---------------------------------

A more detailed description of backwards incompatible changes can be found in the
[upgrading guide](UPGRADE-2.0.md).

### Removed

* Removed support for running on PHP 5.4. It is however still possible to parse PHP 5.2 and PHP 5.3
  code while running on a newer version.
* Removed legacy class name aliases. This includes the old non-namespaced class names and the old
  names for classes that were renamed for PHP 7 compatibility.
* Removed support for legacy node format. All nodes must have a `getSubNodeNames()` method now.

### Added

* Added support for remaining PHP 7 features that were not present in 1.x:
  * Group use declarations. These are represented using `Stmt\GroupUse` nodes. Furthermore a `type`
    attribute was added to `Stmt\UseUse` to handle mixed group use declarations.
  * Uniform variable syntax.
  * Generalized yield operator.
  * Scalar type declarations. These are presented using `'bool'`, `'int'`, `'float'` and `'string'`
    as the type. The PHP 5 parser also accepts these, however they'll be `Name` instances there.
  * Unicode escape sequences.
* Added `PhpParser\ParserFactory` class, which should be used to create parser instances.
* Added `Name::concat()` which concatenates two names.
* Added `Name->slice()` which takes a subslice of a name.

### Changed

* `PhpParser\Parser` is now an interface, implemented by `Parser\Php5`, `Parser\Php7` and
  `Parser\Multiple`. The `Multiple` parser will try multiple parsers, until one succeeds.
* Token constants are now defined on `PhpParser\Parser\Tokens` rather than `PhpParser\Parser`.
* The `Name->set()`, `Name->append()`, `Name->prepend()` and `Name->setFirst()` methods are
  deprecated in favor of `Name::concat()` and `Name->slice()`.
* The `NodeTraverser` no longer clones nodes by default. The old behavior can be restored by
  passing `true` to the constructor.
* The constructor for `Scalar` nodes no longer has a default value. E.g. `new LNumber()` should now
  be written as `new LNumber(0)`.

Version 1.4.0 (2015-07-14)
--------------------------

### Added

* Added interface `PhpParser\Node\FunctionLike`, which is implemented by `Stmt\ClassMethod`,
  `Stmt\Function_` and `Expr\Closure` nodes. This interface provides getters for their common
  subnodes.
* Added `Node\Stmt\ClassLike::getMethod()` to look up a specific method on a class/interface/trait.

### Fixed

* Fixed `isPublic()` return value for implicitly public properties and methods that define and
  additional modifier like `static` or `abstract`.
* Properties are now accepted by the trait builder.
* Fixed `__HALT_COMPILER_OFFSET__` support on HHVM.

Version 1.3.0 (2015-05-02)
--------------------------

### Added

* Errors can now store the attributes of the node/token where the error occurred. Previously only the start line was
  stored.
* If file positions are enabled in the lexer, errors can now provide column information if it is available. See
  [documentation](https://github.com/nikic/PHP-Parser/blob/master/doc/component/Error.markdown#column-information).
* The parser now provides an experimental error recovery mode, which can be enabled by disabling the `throwOnError`
  parser option. In this mode the parser will try to construct a partial AST even if the code is not valid PHP. See
  [documentation](https://github.com/nikic/PHP-Parser/blob/master/doc/component/Error.markdown#error-recovery).
* Added support for PHP 7 `yield from` expression. It is represented by `Expr\YieldFrom`.
* Added support for PHP 7 anonymous classes. These are represented by ordinary `Stmt\Class_` nodes with the name set to
  `null`. Furthermore this implies that `Expr\New_` can now contain a `Stmt\Class_` in its `class` subnode.

### Fixed

* Fixed registration of PHP 7 aliases, for the case where the old name was used before the new name.
* Fixed handling of precedence when pretty-printing `print` expressions.
* Floating point numbers are now pretty-printed with a higher precision.
* Checks for special class names like `self` are now case-insensitive.

Version 1.2.2 (2015-04-03)
--------------------------

* The `NameResolver` now resolves parameter type hints when entering the function/method/closure node. As such other
  visitors running after it will be able to make use of the resolved names at that point already.
* The autoloader no longer sets the `unserialize_callback_func` ini option on registration - this is not necessary and
  may cause issues when running PhpUnit tests with process isolation.

Version 1.2.1 (2015-03-24)
--------------------------

* Fixed registration of the aliases introduced in 1.2.0. Previously the old class names could not be used in
  `instanceof` checks under some circumstances.

Version 1.2.0 (2015-03-22)
--------------------------

### Changed

* To ensure compatibility with PHP 7, the following node classes have been renamed:

        OLD                             => NEW
        PhpParser\Node\Expr\Cast\Bool   => PhpParser\Node\Expr\Cast\Bool_
        PhpParser\Node\Expr\Cast\Int    => PhpParser\Node\Expr\Cast\Int_
        PhpParser\Node\Expr\Cast\Object => PhpParser\Node\Expr\Cast\Object_
        PhpParser\Node\Expr\Cast\String => PhpParser\Node\Expr\Cast\String_
        PhpParser\Node\Scalar\String    => PhpParser\Node\Scalar\String_

  **The previous class names are still supported as aliases.** However it is strongly encouraged to use the new names
  in order to make your code compatible with PHP 7.

* Subnodes are now stored using real properties instead of an array. This improves performance and memory usage of the
  initial parse and subsequent node tree operations. The `NodeAbstract` class still supports the old way of specifying
  subnodes, however this is *deprecated*. In any case properties that are assigned to a node after creation will no
  longer be considered as subnodes.

* Methods and property declarations will no longer set the `Stmt\Class_::MODIFIER_PUBLIC` flag if no visibility is
  explicitly given. However the `isPublic()` method will continue to return true. This allows you to distinguish whether
  a method/property is explicitly or implicitly public and control the pretty printer output more precisely.

* The `Stmt\Class_`, `Stmt\Interface_` and `Stmt\Trait_` nodes now inherit from `Stmt\ClassLike`, which provides a
  `getMethods()` method. Previously this method was only available on `Stmt\Class_`.

* Support including the `bootstrap.php` file multiple times.

* Make documentation and tests part of the release tarball again.

* Improve support for HHVM and PHP 7.

### Added

* Added support for PHP 7 return type declarations. This adds an additional `returnType` subnode to `Stmt\Function_`,
  `Stmt\ClassMethod` and `Expr\Closure`.

* Added support for the PHP 7 null coalesce operator `??`. The operator is represented by `Expr\BinaryOp\Coalesce`.

* Added support for the PHP 7 spaceship operator `<=>`. The operator is represented by `Expr\BinaryOp\Spaceship`.

* Added use builder.

* Added global namespace support to the namespace builder.

* Added a constructor flag to `NodeTraverser`, which disables cloning of nodes.

Version 1.1.0 (2015-01-18)
--------------------------

* Methods that do not specify an explicit visibility (e.g. `function method()`) will now have the `MODIFIER_PUBLIC`
  flag set. This also means that their `isPublic()` method will return true.

* Declaring a property as abstract or final is now an error.

* The `Lexer` and `Lexer\Emulative` classes now accept an `$options` array in their constructors. Currently only the
  `usedAttributes` option is supported, which determines which attributes will be added to AST nodes. In particular
  it is now possible to add information on the token and file positions corresponding to a node. For more details see
  the [Lexer component](https://github.com/nikic/PHP-Parser/blob/master/doc/component/Lexer.markdown) documentation.

* Node visitors can now return `NodeTraverser::DONT_TRAVERSE_CHILDREN` from `enterNode()` in order to skip all children
  of the current node, for all visitors.

* Added builders for traits and namespaces.

* The class, interface, trait, function, method and property builders now support adding doc comments using the
  `setDocComment()` method.

* Added support for fully-qualified and namespace-relative names in builders. No longer allow use of name component
  arrays.

* Do not add documentation and tests to distribution archive files.

Version 1.0.2 (2014-11-04)
--------------------------

* The `NameResolver` visitor now also resolves names in trait adaptations (aliases and precedence declarations).

* Remove stray whitespace when pretty-printing trait adaptations that only change visibility.

Version 1.0.1 (2014-10-14)
--------------------------

* Disallow `new` expressions without a class name. Previously `new;` was accidentally considered to be valid code.

* Support T_ONUMBER token used by HHVM.

* Add ability to directly pass code to the `php-parse.php` script.

* Prevent truncation of `var_dump()` output in the `php-parse.php` script if XDebug is used.

Version 1.0.0 (2014-09-12)
--------------------------

* [BC] Removed deprecated `Template` and `TemplateLoader` classes.

* Fixed XML unserializer to properly work with new namespaced node names.

Version 1.0.0-beta2 (2014-08-31)
--------------------------------

* [PHP 5.6] Updated support for constant scalar expressions to comply with latest changes. This means that arrays
  and array dimension fetches are now supported as well.

* [PHP 5.6] Direct array dereferencing of constants is supported now, i.e. both `FOO[0]` and `Foo::BAR[0]` are valid
  now.

* Fixed handling of special class names (`self`, `parent` and `static`) in the name resolver to be case insensitive.
  Additionally the name resolver now enforces that special class names are only used as unqualified names, e.g. `\self`
  is considered invalid.

* The case of references to the `static` class name is now preserved. Previously `static` was always lowercased,
  regardless of the case used in the source code.

* The autoloader now only requires a file if it exists. This allows usages like
  `class_exists('PhpParser\NotExistingClass')`.

* Added experimental `bin/php-parse.php` script, which is intended to help exploring and debugging the node tree.

* Separated the parser implemention (in `lib/PhpParser/ParserAbstract.php`) and the generated data (in
  `lib/PhpParser/Parser.php`). Furthermore the parser now uses meaningful variable names and contains comments
  explaining their usage.

Version 1.0.0-beta1 (2014-03-27)
--------------------------------

* [BC] PHP-Parser now requires PHP 5.3 or newer to run. It is however still possible to *parse* PHP 5.2 source code,
  while running on a newer version.

* [BC] The library has been moved to use namespaces with the `PhpParser` vendor prefix. However, the old names using
  underscores are still available as aliases, as such most code should continue running on the new version without
  further changes.

  However, code performing dispatch operations on `Node::getType()` may be affected by some of the name changes. For
  example a `+` node will now return type `Expr_BinaryOp_Plus` instead of `Expr_Plus`. In particular this may affect
  custom pretty printers.

  Due to conflicts with reserved keywords, some class names now end with an underscore, e.g. `PHPParser_Node_Stmt_Class`
  is now `PhpParser\Node\Stmt\Class_`. (But as usual, the old name is still available)

* [PHP 5.6] Added support for the power operator `**` (node `Expr\BinaryOp\Pow`) and the compound power assignment
  operator `**=` (node `Expr\AssignOp\Pow`).

* [PHP 5.6] Added support for variadic functions: `Param` nodes now have `variadic` as a boolean subnode.

* [PHP 5.6] Added support for argument unpacking: `Arg` nodes now have `unpack` as a boolean subnode.

* [PHP 5.6] Added support for aliasing of functions and constants. `Stmt\Use_` nodes now have an integral `type`
  subnode, which is one of `Stmt\Use_::TYPE_NORMAL` (`use`), `Stmt\Use_::TYPE_FUNCTION` (`use function`) or
  `Stmt\Use_::TYPE_CONSTANT` (`use const`).

  The `NameResolver` now also supports resolution of such aliases.

* [PHP 5.6] Added support for constant scalar expressions. This means that certain expressions are now allowed as the
  initializer for constants, properties, parameters, static variables, etc.

* [BC] Improved pretty printing of empty statements lists, which are now printed as `{\n}` instead of `{\n    \n}`.
  This changes the behavior of the protected `PrettyPrinterAbstract::pStmts()` method, so custom pretty printing code
  making use it of may need to be adjusted.

* Changed the order of some subnodes to be consistent with their order in the sour code. For example `Stmt\If->cond`
  will now appear before `Stmt\If->stmts` etc.

* Added `Scalar\MagicConstant->getName()`, which returns the name of the magic constant (e.g. `__CLASS__`).

**The following changes are also included in 0.9.5**:

* [BC] Deprecated `PHPParser_Template` and `PHPParser_TemplateLoader`. This functionality does not belong in the main project
  and - as far as I know - nobody is using it.

* Add `NodeTraverser::removeVisitor()` method, which removes a visitor from the node traverser. This also modifies the
  corresponding `NodeTraverserInterface`.

* Fix alias resolution in `NameResolver`: Class names are now correctly handled as case-insensitive.

* The undefined variable error, which is used to the lexer to reset the error state, will no longer interfere with
  custom error handlers.

---

**This changelog only includes changes from the 1.0 and 2.0 series. For older changes see the
[0.9 series changelog][https://github.com/nikic/PHP-Parser/blob/0.9/CHANGELOG.md].**