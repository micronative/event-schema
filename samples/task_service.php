<?php

require_once('./vendor/autoload.php');

use Samples\MessageBroker\MockBroker;
use Samples\TaskService\TaskApp;

try {
    $broker = new MockBroker();
    $taskApp = new TaskApp($broker);
    $taskApp->listen();
} catch (Exception $e) {
    echo $e->getMessage();
}
