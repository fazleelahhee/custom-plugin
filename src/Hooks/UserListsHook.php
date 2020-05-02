<?php

namespace WPCPlugin\Hooks;

use WPCPlugin\Contracts\IDataSource;
use WPCPlugin\DataSource\DataSourceFactory;
use WPCPlugin\User;
use WPCPlugin\Utilities;

class UserListsHook extends AbstractHook
{
    /**
     * @var IDataSource
     */
    protected $dataSource;

    /**
     * Label to displaying in the front end.
     * @var array
     */
    protected $lableArray = [
        ['key' => 'id', 'label' => 'ID', 'link' => 'y'],
        ['key' => 'name', 'label' => 'Name', 'link' => 'y'],
        ['key' => 'username', 'label' => 'Username', 'link' => 'y'],
        ['key' => 'email', 'label' => 'Email', 'link' => 'n'],
        ['key' => 'phone', 'label' => 'Telephone', 'link' => 'n'],
    ];

    protected $endPoint = '';

    /**
     * @var $utilities Utilities
     */
    protected $utilities = '';

    public function __construct()
    {
        $this->addEndPoint()
            ->addDataSource()
            ->addUtilities();
    }

    /**
     * @param string $endpoint
     * @return UserListsHook
     */
    public function addEndPoint(string $endpoint = '/'): self
    {
        $this->endPoint = defined('WPC_PLUGIN_API_ENDPOINT') ? WPC_PLUGIN_API_ENDPOINT : $endpoint;
        return $this;
    }

    /**
     * @param IDataSource|null $dataSource
     * @return UserListsHook
     */
    public function addDataSource(IDataSource $dataSource = null): self
    {
        $this->dataSource = $dataSource;
        if (!$dataSource) {
            $dataSourceFactory = new DataSourceFactory();
            $this->dataSource = $dataSourceFactory->createApi();
        }
        return $this;
    }

    /**
     * @param Utilities|null $utilities
     * @return UserListsHook
     */
    public function addUtilities(Utilities $utilities = null): self
    {
        $this->utilities = $utilities;
        if (!$utilities) {
            $this->utilities = new Utilities();
        }
        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function init(): array
    {
        $output = [];
        $user = new User($this->dataSource, $this->endPoint);
        /**
         * Recursivelly sanitised all data come from external api.
         * because its not in our control and we don't know what type of data coming through
         *so its must be sanitised.
         */
        $output['data'] = $this->utilities->recursiveSanitizeField($user->allUser());

        $output['field_display'] = $this->lableArray;
        return apply_filters('wpcp_plugin_user_collection', $output);
    }
}
