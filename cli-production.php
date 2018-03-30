<?php

/**
 * Codeigniter 3 CLI Bootstrap for production environment
 */

// CLI bootstrap only
if (php_sapi_name() !== 'cli') {
    
    die('Access Denied');
}
// Set env for ENVIRONMENT 
$_SERVER['CI_ENV'] = 'production';
// Load app bootstrap
require __DIR__ . '/index.php';
