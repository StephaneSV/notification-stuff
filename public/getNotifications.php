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
function getNotifications()
{
    $app = \App\App::getApp();
    $me = $app->getAuthUser();
    //    $me = null; // if you want to test the 401 response

    if ($me === null) {
        $apiResponse = new ApiResponse([]);
        $apiResponse->setHeaders(ApiResponse::HTTP_UNAUTHORIZED);
        $apiResponse->setMessage("Unauthenticated");
        echo json_encode($apiResponse);
        return;
    }

    $rep = new \App\Repository\NotificationRepositoryMysql($app->getMysqlConnection());
    $notifications = $rep->getUserNotifications($app->getAuthUser());

    header('Content-type: application/json; charset=utf-8');
    http_response_code(ApiResponse::HTTP_OK);
    $response = new ApiResponse(['data' => $notifications]);
    echo json_encode($response);
}

getNotifications();