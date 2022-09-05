<?php

require_once('./vendor/autoload.php');

use Micronative\MockBroker\Broker;
use Samples\UserService\UserApp;

try {
    $broker = new Broker(dirname(__FILE__) . '/MessageBroker/storage');
    $userApp = new UserApp($broker);
    $userApp->createUser('Ken', 'ken@bc.com');
} catch (Exception $e) {
    echo $e->getMessage();
}
