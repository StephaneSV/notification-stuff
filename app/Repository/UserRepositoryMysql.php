<?php

namespace App\Repository;

use App\Model\User;
use App\Service\MySQLConnection;

class UserRepositoryMysql
{
    private MySQLConnection $connection;

    /**
     * @param MySQLConnection $mySQLConnection
     */
    public function __construct(MySQLConnection $mySQLConnection)
    {
        $this->connection = $mySQLConnection;
    }

    /**
     * @param int $userId
     * @return \App\Model\User|null
     * @throws \Exception
     */
    public function getUser(int $userId): ?User
    {
        $pdoStatement = $this->connection->pdo
            ->prepare('SELECT id, email FROM users WHERE id=?');
        $pdoStatement->execute([$userId]);
        $results = $pdoStatement->fetchAll();
        if (count($results) === 1) {
            $userData = $results[0];
            $user = new User();
            $user->id = $userData->id;
            $user->email = $userData->email;
            return $user;
        }
        if (count($results) === 0) {
            return null;
        }

        throw new \Exception('Probable SQL injection vulnerability');
    }
}