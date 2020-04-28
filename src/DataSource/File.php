<?php

declare(strict_types=1);

namespace WPCPlugin\DataSource;

use WPCPlugin\Contracts\IDataSource;

class File implements IDataSource
{
    private $filePath = '';

    public function updatePath(string $path): IDataSource
    {
        $this->filePath = $path;
        return $this;
    }

    public function content(): string
    {
        return file_get_contents("{$this->filePath}.json");
    }
}
