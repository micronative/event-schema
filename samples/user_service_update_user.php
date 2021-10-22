<?php

require_once('./vendor/autoload.php');

use Samples\MessageBroker\MockBroker;
use Samples\UserService\Entities\User;
use Samples\UserService\UserApp;

try {
    $broker = new MockBroker();
    $userApp = new UserApp($broker);
    $user = new User('John', 'John@bc.com');
    $userApp->updateUser($user);
} catch (Exception $e) {
    echo $e->getMessage();
}
