<?php

App::import('vendor', 'dwoo', array("file" => 'dwoo/dwooAutoload.php'));

/**
 * Dwoo adapter for CakePHP
 *
 * Based on SmartyView by Mark John S. Buenconsejo <mjwork@simpleteq.com>
 *
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 *
 * This file is released under the LGPL
 * "GNU Lesser General Public License"
 * More information can be found here:
 * {@link http://www.gnu.org/copyleft/lesser.html}
 *
 * @author     Mark John S. Buenconsejo <mjwork@simpleteq.com>
 * @author     Giangi <giangi@qwerg.com>
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://www.gnu.org/copyleft/lesser.html  GNU Lesser General Public License
 * @link       http://dwoo.org/
 * @version    1.1.0
 * @date       2009-07-18
 * @package    Dwoo
 */
class DwooView extends View
{
	protected $_sv_template_dir;
	protected $_sv_layout_dir;
	protected $_sv_compile_dir;
	protected $_sv_cache_dir;
	protected $_sv_compile_id;

	protected $_dwoo;

	public $sv_processedTpl;

 	public function __construct(&$controller)
	{
		parent::__construct($controller);

		$this->ext = '.tpl';

		$this->_sv_template_dir = array
		(
			VIEWS . $this->viewPath . DS . $this->subDir,
			VIEWS . $this->viewPath,
			VIEWS
		);

		$this->_sv_layout_dir = array
		(
			LAYOUTS . $this->subDir,
			VIEWS
		);

		$this->_sv_compile_dir = TMP . 'dwoo' . DS . 'compile';
		$this->_sv_cache_dir = TMP . 'dwoo' . DS . 'cache';

		$this->_dwoo = new Dwoo($this->_sv_compile_dir, $this->_sv_cache_dir);

		$this->_sv_compile_id = $controller->name;

		$this->_dwoo->sv_this = $this;
        $this->_dwoo->setSecurityPolicy();

		return;
	}

	/**
	 * changes the template directory
	 */
	public function setTemplateDir($path = VIEW) {
		$old = $this->_sv_template_dir;
		$this->_sv_template_dir  = $path;

		return $old;
	}

	public function getTemplateDir() {
		return $this->_sv_template_dir ;
	}

	public function _render($___viewFn, $___data_for_view, $___play_safe = true, $loadHelpers = true)
	{
		// let's determine if this is a layout call or a template call
		// and change the template dir accordingly
		$layout = false;
		if(isset($___data_for_view['content_for_layout'])) {
			$this->_sv_template_dir = $this->_sv_layout_dir;
			$layout = true;
		}

		$tpl  = new Dwoo_Template_File($___viewFn);
		$data = $___data_for_view;

		$data['view'] = $this;

		if ($this->helpers != false && $loadHelpers === true) {
			$loadedHelpers = array();
			$loadedHelpers = $this->_loadHelpers($loadedHelpers, $this->helpers);

			foreach (array_keys($loadedHelpers) as $helper) {
				$camelBackedHelper = strtolower(substr($helper, 0, 1)) . substr($helper, 1);

				${$camelBackedHelper} = $loadedHelpers[$helper];

				if (is_array(${$camelBackedHelper}->helpers) && !empty(${$camelBackedHelper}->helpers)) {
					$subHelpers = ${$camelBackedHelper}->helpers;
					foreach ($subHelpers as $subHelper) {
						${$camelBackedHelper}->{$subHelper} = $loadedHelpers[$subHelper];
					}
				}

				if(isset($this->passedArgs)) {
					${$camelBackedHelper}->passedArgs = $this->passedArgs;
				}

				$this->loaded[$camelBackedHelper] = ${$camelBackedHelper};

				$data[$camelBackedHelper] = ${$camelBackedHelper};
			}
		}

		if ($this->helpers != false && $loadHelpers === true) {
			foreach ($loadedHelpers as $helper) {
				if (is_object($helper)) {
					if (is_subclass_of($helper, 'Helper') || is_subclass_of($helper, 'helper')) {
						$helper->beforeRender();
					}
				}
			}
		}

		return $this->_dwoo->get($tpl, $data);
	}

	public function get(){
		return $this->_dwoo;
	}
}
