<?php

namespace App;

use App\Repository\UserRepositoryMysql;
use App\Model\User;
use App\Service\MySQLConnection;

class App
{
    /**
     * @var bool
     */
    private static bool $initialised = false;

    /**
     * @var MySQLConnection
     */
    private MySQLConnection $mySQLConnection;

    /**
     * @var \App\Model\User
     */
    private User $authUser;

    /**
     * Initialise the application
     * @return void
     * @throws \Exception
     */
    public function init()
    {
        if (self::$initialised === false) {
            // DB connection
            $this->mySQLConnection = new MySQLConnection('webapp', 'root', 'password');

            // Let's simulate a getAuthenticatedUser that returns a Model User
            $userRep = new UserRepositoryMysql($this->mySQLConnection);
            $this->authUser = $userRep->getUser(2345);
            // The throwable \Exception is not handled here, but should in a prod context (with adequate logging, and http response)
            // end of getAuthenticatedUser

            self::$initialised = true;
        }
    }

    /**
     * @return User
     */
    public function getAuthUser(): User
    {
        return $this->authUser;
    }

    /**
     * @return \App\Service\MySQLConnection
     */
    public function getMysqlConnection(): MySQLConnection
    {
        return $this->mySQLConnection;
    }
}