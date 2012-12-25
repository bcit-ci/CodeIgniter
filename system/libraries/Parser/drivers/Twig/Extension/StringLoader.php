<?php

/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Extension_StringLoader extends Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'template_from_string' => new Twig_Function_Function('twig_template_from_string', array('needs_environment' => true)),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'string_loader';
    }
}

/**
 * Loads a template from a string.
 *
 * <pre>
 * {% include template_from_string("Hello {{ name }}") }}
 * </pre>
 *
 * @param Twig_Environment $env      A Twig_Environment instance
 * @param string           $template A template as a string
 *
 * @return Twig_Template A Twig_Template instance
 */
function twig_template_from_string(Twig_Environment $env, $template)
{
    static $loader;

    if (null === $loader) {
        $loader = new Twig_Loader_String();
    }

    $current = $env->getLoader();
    $env->setLoader($loader);
    try {
        $template = $env->loadTemplate($template);
    } catch (Exception $e) {
        $env->setLoader($current);

        throw $e;
    }
    $env->setLoader($current);

    return $template;
}
