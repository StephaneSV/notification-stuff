<?php

use App\Api\ApiResponse;

require __DIR__ . '/../vendor/autoload.php';

$app = new \App\App();
$app->init();

/**
 * Let's consider this is a controller function
 * @return void
 */
function getNotificationCounts()
{
    // Auth verification
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
    $counts = $rep->getNotificationCounts($app->getAuthUser());

    $apiResponse = new ApiResponse(['data' => $counts]);
    $apiResponse->setMessage('Notification counts for auth user');
    echo json_encode($apiResponse);
}

getNotificationCounts();