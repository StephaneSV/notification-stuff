<?php

use App\Api\ApiResponse;

/**
 * Let's consider this is a controller function
 * @param $app
 * @return void
 */
function getNotifications($app)
{
    $rep = new \App\Repository\NotificationRepositoryMysql($app->getMysqlConnection());
    $notifications = $rep->getNotifications($app->getAuthUser());

    header('Content-type: application/json; charset=utf-8');
    http_response_code(ApiResponse::HTTP_OK);
    $response = new ApiResponse(['data' => $notifications]);
    echo json_encode($response);
}

getNotificationCounts($app);