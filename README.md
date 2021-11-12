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
### Configs
The codes under [samples](./samples) is a mock microservice architecture:
- a [MessageBroker](./samples/MessageBroker)
- two microservices: [UserService](./samples/UserService) and [TaskService](./samples/TaskService)

In this sample, UserService is a producer and TaskService is a consumer. When a User created or updated on UserService, 
it will use Micronative\EventSchema\Producer to validate the event then publish it to MessageBroker. TaskService is 
listening to these events and use Micronative\EventSchema\Consumer to process the incoming events.

- Producer configs:
```yaml
- event: User.Created
  version: 1.0.0
  schema: /assets/schemas/User.Created.schema.json
- event: User.Updated
  version: 2.0.0
  schema: /assets/schemas/User.Updated.schema.json
```
@see: [UserService/assets/configs/events.yml](samples/UserService/assets/configs/events.yml)

```php
class UserApp
{
    private MockPublisher $publisher;
    private UserRepository $userRepository;
    private Producer $producer;

    /**
     * UserApp constructor.
     * @param \Samples\MessageBroker\MockBroker|null $broker
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function __construct(MockBroker $broker = null)
    {
        $this->publisher = new MockPublisher($broker);
        $this->userRepository = new UserRepository();
        $assetDir = dirname(__FILE__);
        $this->producer = new Producer($assetDir, ["/assets/configs/events.yml"]);
    }

    /**
     * @param string $name
     * @param string $email
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function createUser(string $name, string $email)
    {
        $user = new User($name, $email);
        if ($this->userRepository->save($user)) {
            $userEvent = new UserEvent(UserRepository::USER_CREATED, null, Uuid::uuid4()->toString(), $user->toArray());
            if ($this->producer->validate($userEvent, true)) {
                $this->publisher->publish($userEvent->jsonSerialize());
            }
        }
    }

    /**
     * @param \Samples\UserService\Entities\User $user
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function updateUser(User $user)
    {
        if ($this->userRepository->update($user)) {
            $userEvent = new UserEvent(UserRepository::USER_UPDATED, null, Uuid::uuid4()->toString(), $user->toArray());
            if ($this->producer->validate($userEvent, true)) {
                $this->publisher->publish($userEvent->jsonSerialize());
            }
        }
    }
}
```
@see: [UserService/UserApp.php](samples/UserService/UserApp.php)

- Consumer configs
```yaml
- event: User.Created
  version: 1.0.0
  schema: /assets/schemas/User.Created.schema.json
  services:
    - CreateTaskForNewUser
    - SendNotificationToNewUser
- event: User.Created
  version: 2.0.0
  schema: /assets/schemas/User.Created.schema.json
  services:
    - CreateTaskForNewUser
    - SendNotificationToNewUser
- event: User.Updated
  schema: /assets/schemas/User.Updated.schema.json
  services:
    - CreateTaskForUpdatedUser
    - SendNotificationToUpdatedUser
```
@see: [TaskService/assets/configs/events.yml](samples/TaskService/assets/configs/events.yml)

```yaml
- service: Samples\TaskService\Services\CreateTaskForNewUser
  alias: CreateTaskForNewUser
  callbacks:
    - LogTask
- service: Samples\TaskService\Services\SendNotificationToNewUser
  alias: SendNotificationToNewUser
  schema: /assets/schemas/services/SendNotifcation.json
  callbacks:
    - LogNotification
- service: Samples\TaskService\Services\SendNotificationToUpdatedUser
  alias: SendNotificationToUpdatedUser
  callbacks:
    - LogNotification
- service: Samples\TaskService\Services\CreateTaskForUpdatedUser
  alias: CreateTaskForUpdatedUser
  callbacks:
    - LogTask
- service: Samples\TaskService\Services\LogTask
  alias: LogTask
- service: Samples\TaskService\Services\LogNotification
  alias: LogNotification
```
@see: [TaskService/assets/configs/services.yml](samples/TaskService/assets/configs/services.yml)

```php
class TaskApp
{
    private MockReceiver $receiver;
    private Consumer $consumer;

    /**
     * App constructor.
     * @param \Samples\MessageBroker\MockBroker|null $broker
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function __construct(MockBroker $broker = null)
    {
        $this->receiver = new MockReceiver($broker);
        $assetDir = dirname(__FILE__);
        $container = new Container();
        $this->consumer = new Consumer(
            $assetDir,
            ["/assets/configs/events.yml"],
            ["/assets/configs/services.yml"],
            $container
        );
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ConsumerException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function listen()
    {
        $message = $this->receiver->get();
        if (!empty($message)) {
            $taskEvent = (new TaskEvent())->unserialize($message);
            $this->consumer->process($taskEvent);
        }
    }
}
```
@see: [TaskService/TaskApp.php](samples/TaskService/TaskApp.php)

- UserService create new user
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

- UserService update user
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
