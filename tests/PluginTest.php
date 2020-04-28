<?php

namespace WPCPlugin\Tests;

use PHPUnit\Framework\TestCase;
use WPCPlugin\Plugin;

class PluginTest extends TestCase
{
    /**
     * @var $plugin Plugin
     */
    private $plugin;

    public function setUp(): void
    {
        $this->plugin = new Plugin(__FILE__);
    }

    public function tearDown(): void
    {
        $this->plugin = null;
    }

    public function testIsInitFunctionExists()
    {
        $this->assertTrue(
            method_exists($this->plugin, 'init'),
            'Plugin Class does not have method init()'
        );
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
