<?php

namespace WPCPlugin\DataSource;

use WPCPlugin\Contracts\iDataSource;

class Api implements IDataSource
{
    private $endpoint = '';

    public function setPath($path)
    {
        $this->endpoint = $path;
        return $this;
    }

    public function getContent()
    {
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
        return $output;
    }
}
