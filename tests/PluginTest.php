<?php

namespace WPCPlugin\Tests;

use PHPUnit\Framework\TestCase;
use WPCPlugin\DataSource\DataSourceFactory;
use WPCPlugin\Plugin;
use Brain\Monkey;

class PluginTest extends TestCase
{
    /**
     * @var $plugin Plugin
     */
    private $plugin;

    public function setUp(): void
    {
        Monkey\setUp();
        $this->plugin = new Plugin();
        $dataSourceFactory = new DataSourceFactory();
        $this->plugin->addDataSource($dataSourceFactory ->createFile());
    }

    public function tearDown(): void
    {
        Monkey\tearDown();
        $this->plugin = null;
    }

    public function testIsInitFunctionExists()
    {
        $this->assertTrue(
            method_exists($this->plugin, 'init'),
            'Plugin Class does not have method init()'
        );
    }

    public function testAddHooksFunctionExists()
    {
        $this->assertTrue(
            method_exists($this->plugin, 'addHooks'),
            'Plugin Class does not have method addHooks()'
        );
    }

    public function testAddHooksActuallyAddsHooks()
    {
        $this->plugin->addHooks();

        $this->assertTrue(has_action('init', [ $this->plugin, 'rewriteInit' ]));
        $this->assertTrue(has_action('init', [ $this->plugin, 'template' ]));

        $this->assertTrue(has_action('wp_ajax_wpcplugin_user_collection', [
            $this->plugin, 'userCollection',
        ]));

        $this->assertTrue(has_action('wp_ajax_nopriv_wpcplugin_user_collection', [
            $this->plugin, 'userCollection',
        ]));

        $this->assertTrue(has_action('wp_ajax_wpcplugin_user', [ $this->plugin, 'user' ]));
        $this->assertTrue(has_action('wp_ajax_nopriv_wpcplugin_user', [ $this->plugin, 'user' ]));

        $this->assertTrue(has_action('wp_head', [$this->plugin, 'addAjaxUrl']));
        $this->assertTrue(has_action('wp_enqueue_scripts', [ $this->plugin, 'wpcpEnqueueScripts' ]));
    }

    public function testIsReWriteInitFunctionExists()
    {
        $this->assertTrue(
            method_exists($this->plugin, 'rewriteInit'),
            'Plugin Class does not have method rewriteInit()'
        );
    }

    public function testIsQueryVarsFunctionExists()
    {
        $this->assertTrue(
            method_exists($this->plugin, 'recursiveSanitizeField'),
            'Plugin Class does not have method recursiveSanitizeField()'
        );
    }

    public function testIsTemplateFunctionExists()
    {
        $this->assertTrue(
            method_exists($this->plugin, 'template'),
            'Plugin Class does not have method template()'
        );
    }
}
