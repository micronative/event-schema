<?php

require_once('./vendor/autoload.php');

use Micronative\MockBroker\Broker;
use Samples\TaskService\Container;
use Samples\TaskService\TaskApp;

try {
    $broker = new Broker(dirname(__FILE__) . '/MessageBroker/storage');
    $container = new Container();
    $taskApp = new TaskApp($broker, $container);
    $taskApp->listen();
} catch (Exception $e) {
    echo $e->getMessage();
}
