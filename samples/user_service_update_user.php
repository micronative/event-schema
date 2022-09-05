<?php

require_once('./vendor/autoload.php');

use Micronative\MockBroker\Broker;
use Samples\UserService\Entities\User;
use Samples\UserService\UserApp;

try {
    $broker = new Broker(dirname(__FILE__) . '/MessageBroker/storage');
    $userApp = new UserApp($broker);
    $user = new User('John', 'John@bc.com');
    $userApp->updateUser($user);
} catch (Exception $e) {
    echo $e->getMessage();
}
