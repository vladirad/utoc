<?php

/**
 * Plugin Name: Ultimate Table of Contents Plugin
 * Plugin URI: https://devstetic.com
 * Description: Ultimate Table of Contents Plugin
 * Version: 1.0.0
 * Author: Vladimir Radisic
 * Author URI: https://codeable.io
 * Text Domain: utoc
 * Domain Path: /languages
 *
 * @package  Ultimate Table of Contents Plugin
 * @category Plugin
 * @author   Vladimir Radisic
 * @version  1.0.0
 */

require_once(__DIR__ . '/vendor/autoload.php');

$utoc = new \Devstetic\Utoc\UltimateTableOfContents();

if (!function_exists('utoc_preview_toc_block')) {
    function utoc_preview_toc_block()
    {
        register_block_type(__DIR__);
    }

    add_action('init', 'utoc_preview_toc_block');
}
