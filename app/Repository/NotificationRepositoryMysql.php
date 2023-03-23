<?php

namespace App\Repository;

use App\Model\User;
use App\Service\MySQLConnection;

class NotificationRepositoryMysql
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
     * @return array
     */
    public function getUserNotifications(User $user): array
    {
    }

    /**
     * @param User $user
     * @return \stdClass
     */
    public function getNotificationCounts(User $user): \stdClass
    {
        $result = new \stdClass;
        $result->unread = 0;
        $result->read = 0;

        $sql = 'SELECT COUNT(`id`) as nb 
FROM `notifications` 
WHERE `id_user`=? 
  AND `expires` > NOW() 
GROUP BY `new`';
        $pdoStatement = $this->connection->pdo->prepare($sql);
        $pdoStatement->execute([$user->id]);
        foreach ($pdoStatement as $resultLine) {
            if ($resultLine->new === 1) {
                $result->unread = $resultLine->nb;
            } else {
                $result->read = $resultLine->nb;
            }
        }

        return $result;
    }

}