<?php

namespace App\Repository;

use App\Model\Notification;
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
     * @param User $user
     * @return \stdClass
     */
    public function getNotificationCounts(User $user): \stdClass
    {
        $result = new \stdClass;
        $result->unread = 0;
        $result->read = 0;

        $sql = 'SELECT COUNT(`id`) as nb, new 
FROM `notifications` 
WHERE `id_user`=? 
  AND (`expires` IS NULL OR `expires` > NOW()) 
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

    /**
     * @param int $id
     * @return Notification|null
     */
    public function find(int $id): ?Notification
    {
        $sql = 'SELECT id, id_notification_type, id_user, id_content_type, id_content, expires, description, new, date_creation
FROM `notifications` 
WHERE `id`=?';

        $pdoStatement = $this->connection->pdo->prepare($sql);
        $pdoStatement->execute([$id]);
        $results = $pdoStatement->fetchAll();
        if (count($results) === 0) {
            return null;
        }

        if (count($results) === 1) {
            $notificationData = $results[0];
            $notification = new Notification();
            // Currently acting like a DTO
            $notification->id = $id;
            $notification->id_notification_type = $notificationData->id_notification_type;
            $notification->id_user = $notificationData->id_user;
            $notification->id_content_type = $notificationData->id_content_type;
            $notification->id_content = $notificationData->id_content;
            $notification->expires = $notificationData->expires;
            $notification->description = $notificationData->description;
            $notification->new = $notificationData->new;
            $notification->date_creation = $notificationData->date_creation;

            return $notification;
        }


        throw new \Exception('Probable SQL injection vulnerability');
    }

    /**
     * @param User $user
     * @return array
     */
    public function getUserNotifications(User $user): array
    {
        $sql = 'SELECT id, id_notification_type, id_user, id_content_type, id_content, expires, description, new
FROM `notifications` 
WHERE `id`=?
AND `expires` > NOW() 
ORDER BY date_creation DESC';

        return [];
    }

    /**
     * @param Notification $notif
     * @return int
     */
    public function setNotificationRead(Notification $notif)
    {
        if ($notif->new === 1) {
            $sql = 'UPDATE notifications SET new=0 where id = ?';
            $pdoStatement = $this->connection->pdo->prepare($sql);
            $pdoStatement->execute([$notif->id]);
            return 1;
        }

        return 0;
    }
}