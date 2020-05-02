<?php

namespace WPCPlugin\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPCPlugin\Tests\WPCPluginTestCase;
use WPCPlugin\User;
use WPCPlugin\DataSource\File;

class UserTest extends WPCPluginTestCase
{
    /**
     * @var $user User
     */

    private $user;

    private $mockApi;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockApi = \Mockery::mock('WPCPlugin\Contracts\IDataSource');
        $this->mockApi->shouldReceive('updatePath')
            ->andReturnSelf();
        $this->user = new User($this->mockApi, '/');
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

        $this->mockApi->shouldReceive('content')
            ->andReturn("{}");

        $this->user->allUser("empty-users");
    }

    public function testUserNotFoundException()
    {
        $this->expectException(\Exception::class);

        $this->mockApi->shouldReceive('content')
            ->andReturn("{}");

        $this->user->findUserById(12);
    }

    public function testCanHaveExpectedNoOfArray()
    {
        $this->mockApi->shouldReceive('content')
            ->andReturn($this->userCollectionJson);
        $this->assertEquals(10, count($this->user->allUser()));
    }

    public function testExpectedKeyExistsInUserArray()
    {
        $this->mockApi->shouldReceive('content')
            ->andReturn($this->userJson);

        $user = $this->user->findUserById(1);
        $this->assertArrayHasKey('username', $user);
    }

    public function tearDown(): void
    {
        $this->user = null;
        $this->mockApi = null;
    }
}
