<?php

namespace WPCPlugin\Tests;

use PHPUnit\Framework\TestCase;
use WPCPlugin\User;
use WPCPlugin\DataSource\File;

class UserTest extends TestCase
{

    /**
     * @var $user User
     */
    private $user;

    public function setUp(): void
    {
        $this->user = new User(new File(), __DIR__ . '/assets');
    }

    public function testIsCreatedValidClass()
    {
        $this->assertInstanceOf(
            User::class,
            $this->user
        );
    }

    public function testIsGetUsersFunctionExists()
    {
        $this->assertTrue(
            method_exists($this->user, 'allUser'),
            'User Class does not have method GetUsers'
        );
    }

    public function testIsGetUserByIdFunctionExists()
    {
        $this->assertTrue(
            method_exists($this->user, 'findUserById'),
            'User Class does not have method getUserById'
        );
    }

    public function testUserCollectionNotFoundException()
    {
        $this->expectException(\Exception::class);
        $this->user->allUser("empty-users");
    }

    public function testUserNotFoundException()
    {
        $this->expectException(\Exception::class);
        $this->user->findUserById(12);
    }

    public function testCanHaveExpectedNoOfArray()
    {
        $this->assertEquals(10, count($this->user->allUser()));
    }

    public function testExpectedKeyExistsInUserArray()
    {
        $user = $this->user->findUserById(1);
        $this->assertArrayHasKey('username', $user);
    }

    public function tearDown(): void
    {
        $this->user = null;
    }
}
