# Event-Schema
[![Software license][ico-license]](README.md)
[![Build][ico-build-7.4]][link-build]
[![Build][ico-build-8.0]][link-build]
[![Coverage][ico-codecov]][link-codecov]

[ico-license]: https://img.shields.io/github/license/nrk/predis.svg?style=flat-square
[ico-build-7.4]: https://github.com/micronative/event-schema/actions/workflows/php-7.4.yml/badge.svg
[ico-build-8.0]: https://github.com/micronative/event-schema/actions/workflows/php-8.0.yml/badge.svg
[ico-codecov]: https://codecov.io/gh/micronative/event-schema/branch/master/graph/badge.svg

[link-build]: https://github.com/micronative/event-schema/actions
[link-codecov]: https://codecov.io/gh/micronative/mock-server

Event-schema is a tool to validate and process messages from a broker or between microservices.

## Configuration
<pre>
"require": {
        "micronative/event-schema": "^1.0.1"
},
"repositories": [
    { "type": "vcs", "url": "https://github.com/micronative/event-schema" }
],
</pre>

Run
<pre>
composer require micronative/event-schema:1.0.1
</pre>

## Sample code

The code under [samples](./samples) is a mock microservice architecture:
- a [MessageBroker](./samples/MessageBroker)
- two microservices: [UserService](./samples/UserService) and [TaskService](./samples/TaskService)

In this sample, UserService is a producer and TaskService is a consumer. When a User created or updated on UserService, 
it will use Micronative\EventSchema\Validator to validate the event then publish it to MessageBroker. TaskService is 
listening to these events and use Micronative\EventSchema\Consumer to process the incoming events.

### Producer configs:
```yaml
- event: User.Created
  version: 1.0.0
  schema: /assets/schemas/User.Created.schema.json
- event: User.Updated
  version: 2.0.0
  schema: /assets/schemas/User.Updated.schema.json
```
@see: [samples/UserService/assets/configs/events.yml](samples/UserService/assets/configs/events.yml)

```php
<?php

namespace Samples\UserService;

use Micronative\EventSchema\Validator;
use Micronative\MockBroker\Broker;
use Micronative\MockBroker\Publisher as MockPublisher;
use Samples\UserService\Messaging\Publisher;
use Samples\UserService\Entities\User;
use Samples\UserService\Events\UserEventSubscriber;
use Samples\UserService\Repositories\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;

class UserApp
{
    private UserRepository $userRepository;

    /**
     * UserApp constructor.
     * @param \Micronative\MockBroker\Broker|null $broker
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function __construct(Broker $broker = null)
    {
        $eventSubscriber = new UserEventSubscriber(
            new Publisher(
                new MockPublisher($broker),
                new Validator(dirname(__FILE__), ["/assets/configs/out_events.yml"]))
        );
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($eventSubscriber);
        $this->userRepository = new UserRepository($eventDispatcher);
    }

    /**
     * @param string $name
     * @param string|null $email
     * @throws \Exception
     */
    public function createUser(string $name, ?string $email = null)
    {
        $this->userRepository->save(new User($name, $email));
    }

    /**
     * @param \Samples\UserService\Entities\User $user
     * @throws \Exception
     */
    public function updateUser(User $user, string $name, string $email)
    {
        $user->setName($name)->setEmail($email);
        $this->userRepository->update($user);
    }
}

```
@see: [Samples\UserService\UserApp](samples/UserService/UserApp.php)

```php
<?php

namespace Samples\UserService\Repositories;

use Ramsey\Uuid\Uuid;
use Samples\UserService\Entities\User;
use Samples\UserService\Events\UserEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserRepository
{
    private EventDispatcherInterface $eventDispatcher;

    /**
     * UserRepository constructor.
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \Samples\UserService\Entities\User $user
     * @throws \Exception
     */
    public function save(User $user)
    {
        // save user then dispatch event
        $this->eventDispatcher->dispatch(
            new UserEvent(UserEvent::USER_CREATED, UserEvent::VERSION, Uuid::uuid4()->toString(), $user->toArray()),
            UserEvent::USER_CREATED
        );
    }

    /**
     * @param \Samples\UserService\Entities\User $user
     * @throws \Exception
     */
    public function update(User $user)
    {
        // update user then dispatch event
        $this->eventDispatcher->dispatch(
            new UserEvent(UserEvent::USER_UPDATED, UserEvent::VERSION, Uuid::uuid4()->toString(), $user->toArray()),
            UserEvent::USER_UPDATED
        );
    }
}
```
@see: [Samples\UserService\Repositories\UserRepository](samples/UserService/Repositories/UserRepository.php)

```php
<?php

namespace Samples\UserService\Events;

use Samples\UserService\Messaging\Publisher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventSubscriber implements EventSubscriberInterface
{
    private Publisher $publisher;

    public function __construct(Publisher $publisher)
    {
        $this->publisher = $publisher;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserEvent::USER_CREATED => 'onUserCreated',
            UserEvent::USER_UPDATED => 'onUserUpdated',
        ];
    }

    public function onUserCreated(UserEvent $userEvent)
    {
        $this->publisher->publishEvent($userEvent);
    }

    public function onUserUpdated(UserEvent $userEvent)
    {
        $this->publisher->publishEvent($userEvent);
    }
}
```
@see: [Samples\UserService\Events\UserEventSubscriber](samples/UserService/Events/UserEventSubscriber.php)

```php
<?php

namespace Samples\UserService\Messaging;
use Micronative\EventSchema\ValidatorInterface;
use Samples\UserService\Events\UserEvent;
use Micronative\MockBroker\PublisherInterface as MockPublisherInterface;

class Publisher implements PublisherInterface
{
    private ValidatorInterface $validator;
    private MockPublisherInterface $publisher;

    /**
     * @param MockPublisherInterface $publisher
     * @param ValidatorInterface|null $validator
     */
    public function __construct(MockPublisherInterface $publisher, ValidatorInterface $validator = null)
    {
        $this->validator = $validator;
        $this->publisher = $publisher;
    }

    public function publishEvent(UserEvent $userEvent)
    {
        echo "-- Validating outgoing event message: {$userEvent->getName()}" . PHP_EOL;
        if ($this->validator->validate($userEvent, true)) {
            echo "-- Start publishing event message to broker: {$userEvent->getName()}" . PHP_EOL;
            $this->publisher->publish($userEvent->toJson(), UserEvent::USER_EVENT_TOPIC);
            echo "-- Finish publishing event message to broker: {$userEvent->getName()}" . PHP_EOL;
        }
    }
}
```
@see: [Samples\UserService\Events\Messaging\Publisher](samples/UserService/Messaging/Publisher.php)

### Consumer configs
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
@see: [samples/TaskService/assets/configs/events.yml](samples/TaskService/assets/configs/events.yml)

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
@see: [samples/TaskService/assets/configs/services.yml](samples/TaskService/assets/configs/services.yml)

```php
<?php

namespace Samples\TaskService;

use Micronative\EventSchema\Processor;
use Micronative\MockBroker\Broker;
use Micronative\MockBroker\Consumer;
use Micronative\MockBroker\ConsumerInterface;
use Samples\TaskService\Events\TaskEvent;

class TaskApp
{
    const USER_EVENT_TOPIC = 'User.Events';
    private ConsumerInterface $consumer;
    private Processor $processor;

    /**
     * App constructor.
     * @param \Micronative\MockBroker\Broker|null $broker
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function __construct(Broker $broker = null)
    {
        $this->consumer = new Consumer($broker);
        $container = new Container();
        $this->processor = new Processor(
            dirname(__FILE__),
            ["/assets/configs/events.yml"],
            ["/assets/configs/services.yml"],
            $container
        );
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function listen()
    {
        $message = $this->consumer->consume(self::USER_EVENT_TOPIC);
        if (!empty($message)) {
            $taskEvent = (new TaskEvent())->fromJson($message);
            echo "-- Start processing event: {$taskEvent->getName()}" . PHP_EOL;
            $this->processor->process($taskEvent);
            echo "-- Finish processing event: {$taskEvent->getName()}" . PHP_EOL;
        }
    }
}
```
@see: [Samples\TaskService\TaskApp](samples/TaskService/TaskApp.php)
