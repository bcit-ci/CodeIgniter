<?php

namespace PhpParser;

use PhpParser\Builder;
use PhpParser\Node\Stmt\Use_;

/**
 * The following methods use reserved keywords, so their implementation is defined with an underscore and made available
 * with the reserved name through __call() magic.
 *
 * @method Builder\Namespace_ namespace(string $name) Creates a namespace builder.
 * @method Builder\Class_     class(string $name)     Creates a class builder.
 * @method Builder\Interface_ interface(string $name) Creates an interface builder.
 * @method Builder\Trait_     trait(string $name)     Creates a trait builder.
 * @method Builder\Function_  function(string $name)  Creates a function builder.
 * @method Builder\Use_       use(string $name)       Creates a namespace/class use builder.
 */
class BuilderFactory
{
    /**
     * Creates a namespace builder.
     * 
     * @param null|string|Node\Name $name Name of the namespace
     *
     * @return Builder\Namespace_ The created namespace builder
     */
    protected function _namespace($name) {
        return new Builder\Namespace_($name);
    }

    /**
     * Creates a class builder.
     *
     * @param string $name Name of the class
     *
     * @return Builder\Class_ The created class builder
     */
    protected function _class($name) {
        return new Builder\Class_($name);
    }

    /**
     * Creates an interface builder.
     *
     * @param string $name Name of the interface
     *
     * @return Builder\Interface_ The created interface builder
     */
    protected function _interface($name) {
        return new Builder\Interface_($name);
    }

    /**
     * Creates a trait builder.
     *
     * @param string $name Name of the trait
     *
     * @return Builder\Trait_ The created trait builder
     */
    protected function _trait($name) {
        return new Builder\Trait_($name);
    }

    /**
     * Creates a method builder.
     *
     * @param string $name Name of the method
     *
     * @return Builder\Method The created method builder
     */
    public function method($name) {
        return new Builder\Method($name);
    }

    /**
     * Creates a parameter builder.
     *
     * @param string $name Name of the parameter
     *
     * @return Builder\Param The created parameter builder
     */
    public function param($name) {
        return new Builder\Param($name);
    }

    /**
     * Creates a property builder.
     *
     * @param string $name Name of the property
     *
     * @return Builder\Property The created property builder
     */
    public function property($name) {
        return new Builder\Property($name);
    }

    /**
     * Creates a function builder.
     *
     * @param string $name Name of the function
     *
     * @return Builder\Function_ The created function builder
     */
    protected function _function($name) {
        return new Builder\Function_($name);
    }

    /**
     * Creates a namespace/class use builder.
     *
     * @param string|Node\Name Name to alias
     *
     * @return Builder\Use_ The create use builder
     */
    protected function _use($name) {
        return new Builder\Use_($name, Use_::TYPE_NORMAL);
    }

    public function __call($name, array $args) {
        if (method_exists($this, '_' . $name)) {
            return call_user_func_array(array($this, '_' . $name), $args);
        }

        throw new \LogicException(sprintf('Method "%s" does not exist', $name));
    }
}
