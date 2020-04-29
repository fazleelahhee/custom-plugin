<?php

namespace WPCPlugin\Contracts;

interface IDataSourceFactory
{
    public function createApi(): IDataSource;
    public function createFile(): IDataSource;
}
