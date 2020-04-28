<?php

declare(strict_types=1);

namespace WPCPlugin\Contracts;

interface IDataSource
{
    public function updatePath(string $path);
    public function content();
}
