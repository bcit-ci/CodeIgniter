<?php

/**
 * Codeigniter 3 CLI Bootstrap for production environment
 */

// CLI bootstrap only
if (php_sapi_name() !== 'cli') {
    
    die('Access Denied');
}
// Pre-override ENVIRONMENT constant
define('ENVIRONMENT', 'production');
// Load app bootstrap
require __DIR__ . '/index.php';
