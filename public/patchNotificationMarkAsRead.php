<?php

use App\Api\ApiResponse;

require __DIR__ . '/../vendor/autoload.php';

$app = new \App\App();
$app->init();


/**
 * @param int $idNotification
 * @return void|null
 * @throws Exception
 */
function patchNotificationMarkAsRead(int $idNotification)
{
    $app = \App\App::getApp();
    $me = $app->getAuthUser();
    if ($me === null) {
        $apiResponse = new ApiResponse([]);
        $apiResponse->setHeaders(ApiResponse::HTTP_UNAUTHORIZED);
        $apiResponse->setMessage("Unauthenticated");
        echo json_encode($apiResponse);
        return;
    }

    $rep = new \App\Repository\NotificationRepositoryMysql($app->getMysqlConnection());

    $notif = $rep->find($idNotification);
    if ($notif === null) {
        $apiResponse = new ApiResponse([]);
        $apiResponse->setMessage('Notification not found');
        $apiResponse->setHeaders(ApiResponse::HTTP_NOT_FOUND);
        echo json_encode($apiResponse);
        return;
    }

    // Access verification
    $me = $app->getAuthUser();
    if ($notif->id_user !== $me->id) {
        $apiResponse = new ApiResponse([]);
        $apiResponse->setMessage("you don't have access to this resource");
        $apiResponse->setHeaders(ApiResponse::HTTP_FORBIDDEN);
        echo json_encode($apiResponse);
        return;
    }

    $rep->setNotificationRead($notif);
    $apiResponse = new ApiResponse([]);
    $apiResponse->setHeaders(ApiResponse::HTTP_NO_CONTENT);
    return null;
}

// That's dirty, but I won't code controller and route just for this example
patchNotificationMarkAsRead($_GET['id']);
