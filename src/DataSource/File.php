<?php

namespace WPCPlugin\DataSource;

use WPCPlugin\Contracts\IDataSource;

class File implements IDataSource
{
    private $filePath = '';

    public function setPath($path)
    {
        $this->filePath = $path;
        return $this;
    }

    public function getContent()
    {
        return file_get_contents("{$this->filePath}.json");
    }
}
