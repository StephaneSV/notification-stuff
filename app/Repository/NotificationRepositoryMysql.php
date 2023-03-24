<?php

namespace App\Repository;

use App\Model\ContentType;
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
     * @throws \Exception
     */
    public function find(int $id): ?Notification
    {
        $sql = 'SELECT `id`, `id_notification_type`, `id_user`, `id_content_type`, `id_content`, `expires`, `description`, `new`, `date_creation`
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
        $sql = 'SELECT N.`id`, N.`id_notification_type`, N.id_user, N.`id_content_type`, N.`id_content`, N.`expires`,
       N.`description`, N.`new`, N.`date_creation`, 
       CT.`name` as contentType, NT.name as notificationType
FROM `notifications` N
    INNER JOIN notification_types NT on N.id_notification_type = NT.id
    LEFT JOIN content_types CT on N.id_content_type = CT.id
WHERE N.`id_user`= ?
AND (N.`expires` IS NULL OR N.`expires` > NOW()) 
ORDER BY `date_creation` DESC';

        $pdoStatement = $this->connection->pdo->prepare($sql);
        $pdoStatement->execute([$user->id]);
        $notifications = $pdoStatement->fetchAll();
        $arrayOfIdAlbum = [];
        $arrayOfIdPlaylist = [];
        $arrayOfIdPodcast = [];
        $arrayOfIdTrack = [];
        foreach ($notifications as $notification) {
            if ($notification->id_content !== null) {
                $idContent = $notification->id_content;
                // We have attached content
                switch ($notification->id_content_type) {
                    case ContentType::ID_ALBUM:
                        $arrayOfIdAlbum[$idContent] = $idContent;
                        break;
                    case ContentType::ID_PLAYLIST:
                        $arrayOfIdPlaylist[$idContent] = $idContent;
                        break;
                    case ContentType::ID_PODCAST:
                        $arrayOfIdPodcast[$idContent] = $idContent;
                        break;
                    case ContentType::ID_TRACK:
                        $arrayOfIdTrack[$idContent] = $idContent;
                        break;
                }
            }
        }

        $albums = [];
        $playlists = [];
        $podcasts = [];
        $tracks = [];
        // Should be in AlbumRepository, but don't have time to code this sorry
        if (count($arrayOfIdAlbum) > 0) {
            // We could go further and get other attributes from `artists` table to make a richer attached content
            $sql = 'SELECT Al.`id`, Al.`name`, Al.`url_cover`, Ar.`name` as artistName
FROM `albums` Al
    LEFT JOIN artists Ar on Al.id_artist = Ar.id 
WHERE Al.id IN (' . implode(',', $arrayOfIdAlbum) . ')';

            $pdoStatement = $this->connection->pdo->query($sql);
            $albumsFromDb = $pdoStatement->fetchAll();
            foreach ($albumsFromDb as $album) {
                $albums[$album->id] = $album;
            }
        }
        // Same for playlists
        // Same for podcasts
        // Same for tracks

        // Let's loop again and build the final result
        $result = [];
        foreach ($notifications as $notification) {
            $resultNotif = new \stdClass(); // Ideally should be a dedicated DTO for this specific output
            $resultNotif->id = $notification->id;
            $resultNotif->id_notification_type = $notification->id_notification_type;
            $resultNotif->id_user = $user->id;
            $resultNotif->id_content_type = $notification->id_content_type;
            $resultNotif->id_content = $notification->id_content;
            $resultNotif->expires = $notification->expires;
            $resultNotif->description = $notification->description;
            $resultNotif->new = $notification->new;
            $resultNotif->date_creation = $notification->date_creation;

            // We prepare the attached content
            if ($notification->id_content !== null) {
                $idContent = $notification->id_content;
                switch ($notification->id_content_type) {
                    case ContentType::ID_ALBUM:
                        $resultNotif->attachedContent = $albums[$idContent] ?? null;
                        break;
                    case ContentType::ID_PLAYLIST:
                        $resultNotif->attachedContent = $playlists[$idContent] ?? null;
                        break;
                    case ContentType::ID_PODCAST:
                        $resultNotif->attachedContent = $podcasts[$idContent] ?? null;
                        break;
                    case ContentType::ID_TRACK:
                        $resultNotif->attachedContent = $tracks[$idContent] ?? null;
                        break;
                } // switch
            } // if ($notification->id_content !== null)
            $result[] = $resultNotif;
        } // foreach $notif

        return $result;
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