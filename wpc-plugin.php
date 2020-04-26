<?php

/**
 * Plugin Name: Inpsyde Custom Plugin
 * Plugin URI: https://bitbucket.org/fazleelahee/inpsyde-custom-plugin/
 * Description: This plugin created for to test my knowledge and ability to working as a PHP & WordPress developer.
 * Version: 1.0
 * Author: Fazle Elahee
 * Author URI: http://fazleelahee.xyz/
 **/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

//set plugin name
define('WPC_PLUGIN_NAME', 'wpc-plugin');

//set plugin url
define("WPC_PLUGIN_URL", trailingslashit(plugin_dir_url(__FILE__)));

//set base directory
define("WPC_PLUGIN_BASE_PATH", __DIR__);

//set custom plugin endpiont if not exists.
if (!defined('WPC_PLUGIN_ENDPOINT')) {
    define('WPC_PLUGIN_ENDPOINT', 'wpcp-plugin');
}

//set external api endpoint to retrieve data.
if (!defined('WPC_PLUGIN_API_ENDPOINT')) {
    define('WPC_PLUGIN_API_ENDPOINT', 'https://jsonplaceholder.typicode.com');
}

/**
 * Function for getting plugin class
 *
 */
function wpc_plugin()
{
    static $plugin;

    if (null !== $plugin) {
        return $plugin;
    }

    if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 70300) {
        return null;
    }

    $pluginClassName = 'WPCPlugin\Plugin';
    /** @var WPCPlugin\Plugin $plugin */

    if (!class_exists($pluginClassName)) {
        include_once "wpcp-autoload-register.php";
    }

    $plugin = new $pluginClassName(__FILE__);
    $plugin->init()
        ->setDataSource(new \WPCPlugin\DataSource\Api());

    return $plugin;
}

/**
 * Run
 */
if (function_exists('add_action')) {
    add_action('plugins_loaded', 'wpc_plugin');
}
