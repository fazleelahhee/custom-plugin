<?php

namespace WPCPlugin\Tests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;

class WPCPluginTestCase extends TestCase
{
    protected $userCollectionJson;
    protected $userJson;

    public function setUp(): void
    {
        Monkey\setUp();
        $this->userCollectionJson = file_get_contents(__DIR__ . '/assets/users.json');
        $this->userJson =  file_get_contents(__DIR__ . '/assets/users/1.json');
    }

    public function tearDown(): void
    {
        Monkey\tearDown();
        $this->userCollectionJson = null;
        $this->userJson = null;
    }
}
