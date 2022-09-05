<?php

require_once('./vendor/autoload.php');

use Samples\MessageBroker\Broker;
use Samples\TaskService\TaskApp;

try {
    $broker = new Broker(dirname(__FILE__) . '/MessageBroker/storage');
    $taskApp = new TaskApp($broker);
    $taskApp->listen();
} catch (Exception $e) {
    echo $e->getMessage();
}
