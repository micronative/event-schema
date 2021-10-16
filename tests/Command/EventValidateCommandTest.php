<?php

namespace Tests\Command;

use Micronative\EventSchema\Command\EventValidateCommand;
use Micronative\EventSchema\Exceptions\ValidatorException;
use Micronative\EventSchema\Validators\ServiceValidator;
use PHPUnit\Framework\TestCase;
use Tests\Service\Samples\CreateTask;
use Tests\Service\Samples\SampleEvent;

class EventValidateCommandTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Command\EventValidateCommand */
    private $command;

    /** @var \Micronative\EventSchema\Validators\ServiceValidator */
    private $validator;

    /** @var \Micronative\EventSchema\Service\ServiceInterface */
    private $service;

    /** @var \Micronative\EventSchema\Event\AbstractEvent */
    private $event;

    public function setUp(): void
    {
        parent::setUp();
        $testDir = dirname(dirname(__FILE__));
        $this->validator = new ServiceValidator($testDir);
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
