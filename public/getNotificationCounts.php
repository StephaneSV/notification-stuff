<?php

use App\Api\ApiResponse;

require __DIR__ . '/../vendor/autoload.php';

$app = new \App\App();
$app->init();

/**
 * Let's consider this is a controller function
 * @param $app
 * @return void
 */
function getNotificationCounts($app)
{
    $rep = new \App\Repository\NotificationRepositoryMysql($app->getMysqlConnection());
    $counts = $rep->getNotificationCounts($app->getAuthUser());

    header('Content-type: application/json; charset=utf-8');
    http_response_code(ApiResponse::HTTP_OK);
    $apiResponse = new ApiResponse(['data' => $counts]);
    $apiResponse->setMessage('Notification counts for auth user');
    echo json_encode($apiResponse);
}

getNotificationCounts($app);