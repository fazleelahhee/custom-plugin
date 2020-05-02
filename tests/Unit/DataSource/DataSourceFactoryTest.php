<?php

namespace WPCPlugin\Tests\Unit\DataSource;

use PHPUnit\Framework\TestCase;
use WPCPlugin\Contracts\IDataSourceFactory;
use WPCPlugin\DataSource\Api;
use WPCPlugin\DataSource\DataSourceFactory;
use WPCPlugin\DataSource\File;

class DataSourceFactoryTest extends TestCase
{
    public function provideFactory(): array
    {
        return [
            [new DataSourceFactory()],
        ];
    }

    /**
     * @dataProvider provideFactory
     *
     * @param IDataSourceFactory $dataSourceFactory
     */
    public function testCanCreateDataSourceFactory(IDataSourceFactory $dataSourceFactory)
    {
        $this->assertInstanceOf(Api::class, $dataSourceFactory->createApi());
        $this->assertInstanceOf(File::class, $dataSourceFactory->createFile());
    }
}
