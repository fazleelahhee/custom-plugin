<?php

namespace WPCPlugin\Hooks;

use WPCPlugin\Contracts\IDataSource;
use WPCPlugin\DataSource\DataSourceFactory;
use WPCPlugin\Utilities;

class UserHook extends AbstractHook
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
        0 => ['key' => 'id', 'label' => 'ID'],
        1 => ['key' => 'name', 'label' => 'Name'],
        2 => ['key' => 'username', 'label' => 'Username'],
        3 => ['key' => 'email', 'label' => 'Email'],
        4 => ['key' => 'phone', 'label' => 'Telephone'],
        5 => ['key' => 'website', 'label' => 'Website'],
        6 => ['key' => 'address', 'label' => 'Address'],
        7 => ['key' => 'company', 'label' => 'Company'],
    ];

    protected $endPoint = '';

    /**
     * @var $utilities Utilities
     */
    protected $utilities;

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
     * phpcs:disable WordPress.Security.NonceVerification.Recommended
     * @return null
     * @throws \Exception
     */
    public function init(): array
    {
        $output = [];
        $user = new \WPCPlugin\User($this->dataSource, $this->endPoint);
        $userId = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;

        if (empty($userId)) {
            throw new \Exception('User id is empty');
        }

        $response = $this->utilities->recursiveSanitizeField($user->findUserById($userId));

        foreach ($response as $key => $value) {
            $output['data'][$key] = $value;
            if (in_array($key, ['address', 'company'], true)) {
                $value = \array_diff_key($value, ['geo' => "xy",
                    'catchPhrase' => "xy",
                    'bs' => "xy", ]);
                $output['data'][$key] = implode('<br>', $value);
            }
        }

        $output['field_display'] = $this->lableArray;
        return apply_filters('wpcp_plugin_single_user', $output);
    }
}
