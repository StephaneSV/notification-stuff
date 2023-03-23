<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new \App\App();
$app->init();

$rep = new \App\Repository\NotificationRepositoryMysql($app->getMysqlConnection());
$counts = $rep->getNotificationCounts($app->getAuthUser());
var_dump($counts);