# Event-Schema
[![Software license][ico-license]](README.md)
[![Build status][ico-travis]][link-travis]
[![Coverage][ico-codecov]][link-codecov]


[ico-license]: https://img.shields.io/github/license/nrk/predis.svg?style=flat-square
[ico-travis]: https://travis-ci.com/micronative/event-schema.svg?branch=master
[ico-codecov]: https://codecov.io/gh/micronative/event-schema/branch/master/graph/badge.svg

[link-codecov]: https://codecov.io/gh/micronative/event-schema
[link-travis]: https://travis-ci.com/github/micronative/event-schema

Event-schema is a tool to validate and process messages from a broker or between microservices.

## Configuration
<pre>
"require": {
        "micronative/event-schema": "^1.0.0"
},
"repositories": [
    { "type": "vcs", "url": "https://github.com/micronative/event-schema" }
],
</pre>

Run
<pre>
composer require micronative/event-schema:1.0.0
</pre>

## Sample code
The codes under [samples](./samples) is a mock microservice architecture:
- a [MessageBroker](./samples/MessageBroker)
- two microservices: [UserService](./samples/UserService) and [TaskService](./samples/TaskService)

When a User created or updated on UserService, it will use Micronative\EventSchema\Producer to validate the event then publish it to MessageBroker. TaskService is listening to these events and use Micronative\EventSchema\Consumer to process the incoming events
```php
try {
    $broker = new MockBroker();
    $userApp = new UserApp($broker);
    $userApp->createUser('Ken', 'ken@bc.com');
} catch (Exception $e) {
    echo $e->getMessage();
}
```
@see: [user_service_create_user.php](samples/user_service_create_user.php)

```php
try {
    $broker = new MockBroker();
    $userApp = new UserApp($broker);
    $user = new User('John', 'John@bc.com');
    $userApp->updateUser($user);
} catch (Exception $e) {
    echo $e->getMessage();
}
```
@see: [user_service_update_user.php](samples/user_service_update_user.php)

```php
try {
    $broker = new MockBroker();
    $taskApp = new TaskApp($broker);
    $taskApp->listen();
} catch (Exception $e) {
    echo $e->getMessage();
}
```
@see: [task_service.php](samples/task_service.php)
