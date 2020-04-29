<?php

namespace WPCPlugin\DataSource;

use WPCPlugin\Contracts\IDataSource;
use WPCPlugin\Contracts\IDataSourceFactory;

class DataSourceFactory implements IDataSourceFactory
{
    public function __construct()
    {
        return $this;
    }

    /**
     * @return IDataSource
     */
    public function createApi(): IDataSource
    {
        return new Api();
    }

    /**
     * @return IDataSource
     */
    public function createFile(): IDataSource
    {
        return new File();
    }
}
