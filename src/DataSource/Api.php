<?php

declare(strict_types=1);

namespace WPCPlugin\DataSource;

use WPCPlugin\Contracts\IDataSource;

class Api implements IDataSource
{
    private $endpoint = '';

    private $cache = false;
    private $expire =  3600;

    public function __construct()
    {
        if (defined('WPCPLUGIN_API_CACHE')) {
            $this->cache = WPCPLUGIN_API_CACHE;
        }
    }

    public function updatePath(string $path): IDataSource
    {
        $this->endpoint = $path;
        return $this;
    }

    public function content(): string
    {
        $cacheKey = WPC_PLUGIN_NAME . '-' . md5($this->endpoint);
        //phpcs:disable WordPress.CodeAnalysis.AssignmentInCondition.Found
        if ($this->cache or false === ($output = get_transient($cacheKey))) {
            // create curl resource
            $chr = curl_init();
            // set url
            curl_setopt($chr, CURLOPT_URL, $this->endpoint);
            //return the transfer as a string
            curl_setopt($chr, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($chr, CURLOPT_SSL_VERIFYPEER, 0);
            // $output contains the output string
            $output = curl_exec($chr);

            // close curl resource to free up system resources
            curl_close($chr);
            if ($this->cache) {
                set_transient($cacheKey, $output, $this->expire);
            }
        }
        return $output;
    }
}
