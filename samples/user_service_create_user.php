<?php

require_once('./vendor/autoload.php');

use Samples\MessageBroker\MockBroker;
use Samples\UserService\UserApp;

try {
    $broker = new MockBroker();
    $userApp = new UserApp($broker);
    $userApp->createUser('Ken', 'ken@bc.com');
} catch (Exception $e) {
    echo $e->getMessage();
}
