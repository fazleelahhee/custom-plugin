<?php

namespace WPCPlugin\Tests\Unit\Hooks;

use WPCPlugin\Hooks\UserListsHook;
use WPCPlugin\Tests\WPCPluginTestCase;
use Brain\Monkey\Filters;

class UserListsHookTest extends WPCPluginTestCase
{
    /**
     * @var $userTestHook UserListsHook
     */
    private $userTestHook;

    public function setUp(): void
    {
        parent::setUp();
        $this->userTestHook = new UserListsHook();

        $mockDataSource = \Mockery::mock('WPCPlugin\Contracts\IDataSource');
        $mockDataSource->shouldReceive('updatePath')
            ->andReturnSelf();
        $mockDataSource->shouldReceive('content')
            ->andReturn($this->userCollectionJson);
        $this->userTestHook->addDataSource($mockDataSource);
    }

    public function testApplyFilterHookAdded()
    {
        $_REQUEST['user_id'] = 1;
        $response = $this->userTestHook->init();
        $this->assertTrue(Filters\applied('wpcp_plugin_user_collection') > 0);
        $this->assertEquals(2, count($response));
        $this->assertArrayHasKey('field_display', $response);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(10, count($response['data']));
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
