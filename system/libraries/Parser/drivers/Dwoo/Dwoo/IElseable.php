<?php

/**
 * interface that represents a block plugin that supports the else functionality
 *
 * the else block will enter an "hasElse" parameter inside the parameters array
 * of the closest parent implementing this interface, the hasElse parameter contains
 * the else output that should be appended to the block's content (see foreach or other
 * block for examples)
 *
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 *
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.0.0
 * @date       2008-10-23
 * @package    Dwoo
 */
interface Dwoo_IElseable
{
}
