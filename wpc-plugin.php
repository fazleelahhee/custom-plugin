<?php

/**
 * Plugin Name: Inpsyde Custom Plugin
 * Plugin URI: https://bitbucket.org/fazleelahee/inpsyde-custom-plugin/
 * Description: Populate user list and individual infromation from external api.
 *
 * Version: 1.0.0
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

//set external api endpoint to retrieve data.
if (!defined('WPC_PLUGIN_API_ENDPOINT')) {
    define('WPC_PLUGIN_API_ENDPOINT', 'https://jsonplaceholder.typicode.com');
}

/**
 * Function for getting plugin class
 *
 * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
 * phpcs:disable NeutronStandard.Globals.DisallowGlobalFunctions.GlobalFunctions
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
        /**
         * Register namespace with SPL auto loader, instead of adding file individually.
         * Its more scalable and safest options I think.
         */
        include_once "wpcp-autoload-register.php";
    }

    $plugin = new $pluginClassName();

    $dataSourceFactory = new \WPCPlugin\DataSource\DataSourceFactory();
    $plugin->init()
        ->addDataSource($dataSourceFactory->createApi());

    return $plugin;
}

/**
 * Run
 */
if (function_exists('add_action')) {
    add_action('plugins_loaded', 'wpc_plugin');
}
