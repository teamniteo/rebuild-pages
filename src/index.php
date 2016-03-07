<?php
/*
    Plugin Name: Rebuild Pages from Majestic
    Description: Rebuild pages with backlinks by importing Majestic CSV export.
    Version: 1.0
    Runtime: 5.5
    Author: NiteoWeb Ltd.
    Author URI:  www.niteoweb.com
 */

function a_58ca0fbbe30f381e1909cf6cd3e78588($class)
{
    $prefix = 'Niteoweb\RebuildPages';
    $base_dir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace("\\", DIRECTORY_SEPARATOR, $relative_class) . '.php';
    if (file_exists($file)) {
        require_once(realpath($file));
    }
}
spl_autoload_register('a_58ca0fbbe30f381e1909cf6cd3e78588');

// Inside WordPress
if (defined('ABSPATH')) {
    new Niteoweb\RebuildPages\App;
}
