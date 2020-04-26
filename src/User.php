<?php

namespace WPCPlugin;

use WPCPlugin\Contracts\IDataSource;

class User
{
    /**
     * User data source
     * @var $dataSource IDataSource
     */
    private $dataSource;

    /**
     * Data Source endpoint
     * @var $endPoint string
     */
    private $endpiont;

    /**
     * @param IDataSource $dataSource
     * @param string $endPoint
     */
    public function __construct(IDataSource $dataSource, string $endPoint)
    {
        $this->dataSource = $dataSource;
        $this->endpiont = $endPoint;
        return $this;
    }

    /**
     * Get alll users from data source
     *
     * @param string $collectionEndPoint
     * @return array
     * @throws \Exception
     */
    public function getUsers($collectionEndPoint = "users")
    {
        $response = $this->dataSource
            ->setPath($this->endpiont . "/" . $collectionEndPoint)
            ->getContent();
        $userCollection = json_decode($response, true);

        if (empty($userCollection)) {
            throw new \Exception("No user found.");
        }

        return $userCollection;
    }

    /**
     * Get User by User ID from data Source
     * @param int $userId
     * @param string $collectionEndPoint
     * @return array
     * @throws \Exception
     */
    public function getUserById(int $userId, $collectionEndPoint = "users")
    {
        $response = $this->dataSource
            ->setPath($this->endpiont . "/{$collectionEndPoint}/{$userId}")
            ->getContent();
        $user = json_decode($response, true);

        if (empty($user)) {
            throw new \Exception("No user found.");
        }

        return $user;
    }
}
