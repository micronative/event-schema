<?php

namespace Tests;

use Micronative\EventSchema\Config\Consumer\EventConfigRegister;
use Micronative\EventSchema\Config\Consumer\ServiceConfigRegister;
use Micronative\EventSchema\Event\EventValidator;
use Micronative\EventSchema\Exceptions\ProcessorException;
use Micronative\EventSchema\Exceptions\ValidatorException;
use Micronative\EventSchema\Json\JsonReader;
use Micronative\EventSchema\Processor;
use Micronative\EventSchema\Service\ServiceFactory;
use PHPUnit\Framework\TestCase;
use Tests\Event\SampleEvent;

class ConsumerTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Processor */
    protected Processor $consumer;
    protected string $testDir;

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(__FILE__);
        $this->consumer = new Processor(
            $this->testDir,
            ["/assets/consumer/configs/in_events.yml", "/assets/consumer/configs/in_events.json"],
            ["/assets/consumer/configs/services.yml", "/assets/consumer/configs/services.json"],
        );
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testProcess()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.Created.event.json"),
            false
        );
        $event = new SampleEvent('User.Created', null, $data->id, (array)$data->payload);
        $result = $this->consumer->process($event);
        $this->assertTrue(is_bool($result));
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     */
    public function testProcessFailed()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.Created.Failed.event.json"),
            false
        );
        $event = new SampleEvent('User.Created', null, $data->id ?? null, (array)$data->payload);
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches('%' . sprintf(ValidatorException::INVALIDATED_EVENT, $event->getName()) . '%');
        $this->consumer->process($event);
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testProcessWithFilteredEvent()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.Created.event.json"),
            false
        );
        $event = new SampleEvent($data->name, null, $data->id, (array)$data->payload);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessageMatches('%' . ProcessorException::FILTERED_EVENT_ONLY . '%');
        $this->consumer->process($event, ['EventOne', 'EventTwo']);
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testProcessWithNoneRegisteredEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/None.Registered.Event.json"), false);
        $event = new SampleEvent($data->name, null, $data->id ?? null, (array)$data->payload);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(
            sprintf(ProcessorException::NO_REGISTER_EVENTS, $event->getName(), $event->getVersion())
        );
        $this->consumer->process($event);
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testProcessWithEmptyServiceEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Empty.Service.Event.json"), false);
        $event = new SampleEvent('Empty.Service.Event', null, $data->id ?? null, (array)$data->payload);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(
            sprintf(ProcessorException::NO_REGISTER_SERVICES, $event->getName(), $event->getVersion())
        );
        $this->consumer->process($event);
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testProcessWithNoneExistingServiceEvent()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/None.Existing.Service.Event.json"),
            false
        );
        $event = new SampleEvent($data->name, null, $data->id ?? null, (array)$data->payload);
        $this->assertTrue($this->consumer->process($event));
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testProcessWithInvalidServiceClass()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Invalid.Service.Class.Event.json"),
            false
        );
        $event = new SampleEvent('Invalid.Service.Class.Event', null, $data->id ?? null, (array)$data->payload);
        $this->assertTrue($this->consumer->process($event));
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testRollback()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.Created.event.json"),
            false
        );
        $event = new SampleEvent('User.Created', null, $data->id ?? null, (array)$data->payload);
        $result = $this->consumer->rollback($event);
        $this->assertTrue($result);
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     */
    public function testRollbackWithInvalidValidation()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.Created.Failed.event.json"),
            false
        );
        $event = new SampleEvent('User.Created', null, $data->id ?? null, (array)$data->payload);
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches('%' . sprintf(ValidatorException::INVALIDATED_EVENT, $event->getName()). '%');
        $this->consumer->rollback($event);
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testRollbackWithInvalidServiceClass()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Invalid.Service.Class.Event.json"),
            false
        );
        $event = new SampleEvent($data->name, null, $data->id ?? null, (array)$data->payload);
        $this->assertTrue($this->consumer->rollback($event));
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testRollbackWithNoneExistingServiceEvent()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/None.Existing.Service.Event.json"),
            false
        );
        $event = new SampleEvent($data->name, null, $data->id ?? null, (array)$data->payload);
        $this->assertTrue($this->consumer->rollback($event));
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testRollbackWithEmptyServiceEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/Empty.Service.Event.json"), false);
        $event = new SampleEvent($data->name, null, $data->id ?? null, (array)$data->payload);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(
            sprintf(ProcessorException::NO_REGISTER_SERVICES, $event->getName(), $event->getVersion())
        );
        $this->consumer->rollback($event);
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testRollbackWithNoneRegisteredEvent()
    {
        $data = JsonReader::decode(JsonReader::read($this->testDir . "/assets/events/None.Registered.Event.json"), false);
        $event = new SampleEvent($data->name, null, $data->id, (array)$data->payload);
        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage(
            sprintf(ProcessorException::NO_REGISTER_EVENTS, $event->getName(), $event->getVersion())
        );
        $this->consumer->rollback($event);
    }

    public function testSettersAndGetters()
    {
        $eventRegister = new EventConfigRegister();
        $this->consumer->setEventConfigRegister($eventRegister);
        $this->assertSame($eventRegister, $this->consumer->getEventConfigRegister());

        $serviceRegister = new ServiceConfigRegister();
        $this->consumer->setServiceConfigRegister($serviceRegister);
        $this->assertSame($serviceRegister, $this->consumer->getServiceConfigRegister());

        $serviceFactory = new ServiceFactory();
        $this->consumer->setServiceFactory($serviceFactory);
        $this->assertSame($serviceFactory, $this->consumer->getServiceFactory());

        $validator = new EventValidator();
        $this->consumer->setEventValidator($validator);
        $this->assertSame($validator, $this->consumer->getEventValidator());

        $schemaDir = "/app";
        $this->consumer->setAssetDir($schemaDir);
        $this->assertEquals($schemaDir, $this->consumer->getAssetDir());
        $container = new SampleContainer();
        $this->consumer->setContainer($container);
        $this->assertEquals($container, $this->consumer->getContainer());
    }
}
