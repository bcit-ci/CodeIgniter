<?php

/**
 * Inheritance Runtime Methods processBlock, endChild, init
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 *
 **/
class Smarty_Internal_Runtime_Inheritance
{

    /**
     * State machine
     * - 0 idle next extends will create a new inheritance tree
     * - 1 processing child template
     * - 2 wait for next inheritance template
     * - 3 assume parent template, if child will loaded goto state 1
     *     a call to a sub template resets the state to 0
     *
     * @var int
     */
    public $state = 0;

    /**
     * Array of block parameter of known {block} tags
     *
     * @var array
     */
    public $blockParameter = array();

    /**
     * inheritance template nesting level
     *
     * @var int
     */
    public $inheritanceLevel = 0;

    /**
     * inheritance template index
     *
     * @var int
     */
    public $tplIndex = - 1;

    /**
     * Array of compiled template file path
     * - key template index
     * only used when caching is enabled
     *
     * @var []string
     */
    public $compiledFilePath = array();

    /**
     * Current {block} nesting level
     *
     * @var int
     */
    public $blockNesting = 0;

    /**
     * Initialize inheritance
     *
     * @param \Smarty_Internal_Template $tpl        template object of caller
     * @param bool                      $initChild  if true init for child template
     * @param array                     $blockNames outer level block name
     *
     */
    public function init(Smarty_Internal_Template $tpl, $initChild, $blockNames = array())
    {
        // if template was from an inner block or template is a parent template create new inheritance root
        if ($initChild && ($this->blockNesting || $this->state == 3)) {
            $tpl->ext->_inheritance = new Smarty_Internal_Runtime_Inheritance();
            $tpl->ext->_inheritance->init($tpl, $initChild, $blockNames);
            return;
        }
        // start of child sub template(s)
        if ($initChild) {
            $this->state = 1;
            if (!$this->inheritanceLevel) {
                //grab any output of child templates
                ob_start();
            }
            $this->inheritanceLevel ++;
        }
        // in parent state {include} will not increment template index
        if ($this->state != 3) {
            $this->tplIndex ++;
        }
        // if state was waiting for parent change state to parent
        if ($this->state == 2) {
            $this->state = 3;
        }
    }

    /**
     * End of child template(s)
     * - if outer level is reached flush output buffer and switch to wait for parent template state
     *
     * @param \Smarty_Internal_Template $tpl template object of caller
     */
    public function endChild(Smarty_Internal_Template $tpl)
    {
        $this->inheritanceLevel --;
        if (!$this->inheritanceLevel) {
            ob_end_clean();
            $this->state = 2;
        }
    }

    /**
     * Process inheritance {block} tag
     *
     * $type 0 = {block}:
     *  - search in inheritance template hierarchy for child blocks
     *    if found call it, otherwise call current block
     *  - ignored for outer level blocks in child templates
     *
     * $type 1 = {block}:
     *  - nested {block}
     *  - search in inheritance template hierarchy for child blocks
     *    if found call it, otherwise call current block
     *
     * $type 2 = {$smarty.block.child}:
     *  - search in inheritance template hierarchy for child blocks
     *    if found call it, otherwise ignore
     *
     * $type 3 = {block append} {block prepend}:
     *  - call parent block
     *
     * $type 4 = {$smarty.block.parent}:
     *  - call parent block
     *
     * @param \Smarty_Internal_Template $tpl       template object of caller
     * @param int                       $type      call type see above
     * @param string                    $name      block name
     * @param array                     $block     block parameter
     * @param array                     $callStack call stack with block parameters
     *
     * @throws \SmartyException
     */
    public function processBlock(Smarty_Internal_Template $tpl, $type = 0, $name, $block, $callStack = array())
    {
        if (!isset($this->blockParameter[ $name ])) {
            $this->blockParameter[ $name ] = array();
        }
        if ($this->state == 1) {
            $block[ 2 ] = count($this->blockParameter[ $name ]);
            $block[ 3 ] = $this->tplIndex;
            $this->blockParameter[ $name ][] = $block;
            return;
        }
        if ($type == 3) {
            if (!empty($callStack)) {
                $block = array_shift($callStack);
            } else {
                return;
            }
        } elseif ($type == 4) {
            if (!empty($callStack)) {
                array_shift($callStack);
                if (empty($callStack)) {
                    throw new SmartyException("inheritance: tag {\$smarty.block.parent} used in parent template block '{$name}'");
                }
                $block = array_shift($callStack);
            } else {
                return;
            }
        } else {
            $index = 0;
            $blockParameter = &$this->blockParameter[ $name ];
            if ($type == 0) {
                $index = $block[ 2 ] = count($blockParameter);
                $block[ 3 ] = $this->tplIndex;
                $callStack = array(&$block);
            } elseif ($type == 1) {
                $block[ 3 ] = $callStack[ 0 ][ 3 ];
                for ($i = 0; $i < count($blockParameter); $i ++) {
                    if ($blockParameter[ $i ][ 3 ] <= $block[ 3 ]) {
                        $index = $blockParameter[ $i ][ 2 ];
                    }
                }
                $block[ 2 ] = $index;
                $callStack = array(&$block);
            } elseif ($type == 2) {
                $index = $callStack[ 0 ][ 2 ];
                if ($index == 0) {
                    return;
                }
                $callStack = $block = array(1 => false);
            }
            $index --;
            // find lowest level child block
            while ($index >= 0 && ($type || !$block[ 1 ])) {
                $block = &$blockParameter[ $index ];
                array_unshift($callStack, $block);
                if ($block[ 1 ]) {
                    break;
                }
                $index --;
            }
            if (isset($block[ 'hide' ]) && $index <= 0) {
                return;
            }
        }
        $this->blockNesting ++;
        // {block append} ?
        if (isset($block[ 'append' ])) {
            $appendStack = $callStack;
            if ($type == 0) {
                array_shift($appendStack);
            }
            $this->processBlock($tpl, 3, $name, null, $appendStack);
        }
        // call block of current stack level
        if (isset($block[6])) {
            $block[6]($tpl, $callStack);
        } else {
            $block[0]($tpl, $callStack);
        }
        // {block prepend} ?
        if (isset($block[ 'prepend' ])) {
            $prependStack = $callStack;
            if ($type == 0) {
                array_shift($prependStack);
            }
            $this->processBlock($tpl, 3, $name, null, $prependStack);
        }
        $this->blockNesting --;
    }
}
