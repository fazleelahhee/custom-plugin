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
if ( ! defined( 'WPINC' ) ) {
	die;
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

	if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50400) {
		return null;
	}


	$pluginClassName = 'WPCPlugin\Plugin';
	/** @var WPCPlugin\Plugin $plugin */

	if (! class_exists($pluginClassName)) {
		include_once "wpcp-autoload-register.php";
		die("Plugin not exists");
	}

	die("Plgin exists");
//	$plugin = new $pluginClassName(__FILE__);
//	$plugin->init();
//
//	return $plugin;
}

/**
 * Run
 */
if (function_exists('add_action')) {
	add_action('plugins_loaded', 'wpc_plugin');
}