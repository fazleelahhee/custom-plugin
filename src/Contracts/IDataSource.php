<?php

namespace WPCPlugin\Contracts;

interface IDataSource
{
    public function setPath($path);
    public function getContent();
}
