<?php

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Dwoo.php';

function dwooAutoload($class)
{
	if (substr($class, 0, 5) === 'Dwoo_') {
		include DWOO_DIRECTORY . strtr($class, '_', DIRECTORY_SEPARATOR).'.php';
	}
}

spl_autoload_register('dwooAutoload');
