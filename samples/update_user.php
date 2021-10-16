<?php

require_once('./vendor/autoload.php');

use Samples\MessageBroker\MockBroker;
use Samples\TaskService\TaskApp;
use Samples\UserService\Entities\User;
use Samples\UserService\UserApp;

try {
    $broker = new MockBroker();
    $userApp = new UserApp($broker);
    $taskApp = new TaskApp($broker);

    $user = new User('Ken', 'ken.ngo@gmail.com');
    $userApp->updateUser($user);
    $taskApp->listen();
} catch (Exception $e) {
    echo $e->getMessage();
}
