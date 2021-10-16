<?php

namespace Tests;

use Micronative\EventSchema\Exceptions\ValidatorException;
use Micronative\EventSchema\Json\JsonReader;
use Micronative\EventSchema\Producer;
use PHPUnit\Framework\TestCase;
use Tests\Event\SampleEvent;

class ProducerTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Producer */
    protected $producer;

    /** @var string */
    protected $testDir;


    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(__FILE__);
        $this->producer = new Producer(
            [$this->testDir . "/assets/producer/configs/events.yml"],
            $this->testDir
        );
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testValidate()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json")
        );
        $event = new SampleEvent($data->name, null, $data->id, (array)$data->payload);
        $event->setSchemaFile("/assets/producer/schemas/Task.json");
        $validated = $this->producer->validate($event, true);
        $this->assertTrue($validated);
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testValidateThrowsException()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.afterSaveCommit.Create.json")
        );
        $event = new SampleEvent($data->name, null, $data->id, (array)$data->payload);
        $event->setSchemaFile("/assets/producer/schemas/TaskMore.json");
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches('%' . ValidatorException::INVALIDATED_EVENT . '%');
        $this->producer->validate($event, true);
    }

    public function testSettersAndGetters()
    {
        $schemaDir = "/app";
        $this->producer->setSchemaDir($schemaDir);
        $this->assertEquals($schemaDir, $this->producer->getSchemaDir());
    }
}
