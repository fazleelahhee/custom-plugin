<?php

namespace WPCPlugin\Tests\Unit\Hooks;

use WPCPlugin\Hooks\UserHook;
use WPCPlugin\Tests\WPCPluginTestCase;
use Brain\Monkey\Filters;

class UserHookTest extends WPCPluginTestCase
{
    /**
     * @var $userTestHook UserHook
     */
    private $userTestHook;

    public function setUp(): void
    {
        parent::setUp();
        $this->userTestHook = new UserHook();

        $mockDataSource = \Mockery::mock('WPCPlugin\Contracts\IDataSource');
        $mockDataSource->shouldReceive('updatePath')
            ->andReturnSelf();
        $mockDataSource->shouldReceive('content')
            ->andReturn($this->userJson);
        $this->userTestHook->addDataSource($mockDataSource);
    }

    public function testNoUserIdException()
    {
        $_REQUEST['user_id'] = 0;
        $this->expectException(\Exception::class);
        $this->userTestHook->init();
    }

    public function testApplyFilterHookAdded()
    {
        $_REQUEST['user_id'] = 1;
        $response = $this->userTestHook->init();
        $this->assertTrue(Filters\applied('wpcp_plugin_single_user') > 0);
        $this->assertEquals(2, count($response));
        $this->assertArrayHasKey('field_display', $response);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(8, count($response['data']));
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->userTestHook = null;
    }
}
