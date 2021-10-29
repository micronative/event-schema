<?php

namespace Tests\Command;

use Micronative\EventSchema\Command\EventValidateCommand;
use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Event\EventValidator;
use Micronative\EventSchema\Exceptions\ValidatorException;
use Micronative\EventSchema\Service\ServiceInterface;
use PHPUnit\Framework\TestCase;
use Tests\Service\Samples\CreateTask;
use Tests\Service\Samples\SampleEvent;

class EventValidateCommandTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Command\EventValidateCommand */
    private EventValidateCommand $command;
    private EventValidator $validator;
    private ServiceInterface $service;
    private AbstractEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        $testDir = dirname(dirname(__FILE__));
        $this->validator = new EventValidator($testDir);
        $this->event = new SampleEvent('Test.Event.Name', 1, ['name' => 'Ken']);
        $this->service = new CreateTask();
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testExecute()
    {
        $this->event->setSchemaFile("/assets/consumer/schemas/CreateTask.json");
        $this->command = new EventValidateCommand(
            $this->validator,
            $this->event,
            true
        );
        $result = $this->command->execute();
        $this->assertTrue($result);
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testExecuteThrowsException()
    {
        $this->event->setSchemaFile("/assets/consumer/schemas/CreateContact.json");
        $this->command = new EventValidateCommand($this->validator, $this->event, true);
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches('%' . ValidatorException::INVALIDATED_EVENT . '%');
        $this->command->execute();
    }
}
