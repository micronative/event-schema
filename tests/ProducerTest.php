<?php

namespace Tests;

use Micronative\EventSchema\Exceptions\ValidatorException;
use Micronative\EventSchema\Json\JsonReader;
use Micronative\EventSchema\Validator;
use PHPUnit\Framework\TestCase;
use Tests\Event\SampleEvent;

class ProducerTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Validator */
    protected Validator $producer;
    protected string $testDir;


    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(__FILE__);
        $this->producer = new Validator(
            $this->testDir,
            ["/assets/producer/configs/out_events.yml"]
        );
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testValidate()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.Created.event.json")
        );
        $event = new SampleEvent($data->name, null, $data->id, (array)$data->payload);
        $event->setSchemaFile("/assets/producer/schemas/User.Created.schema.json");
        $validated = $this->producer->validate($event, true);
        $this->assertTrue($validated);
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testValidateEventWithNoSchema()
    {
        $data = JsonReader::decode(
            JsonReader::read($this->testDir . "/assets/events/Users.Created.event.json")
        );
        $event = new SampleEvent($data->name, null, $data->id, (array)$data->payload);
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
            JsonReader::read($this->testDir . "/assets/events/Users.Created.event.json")
        );
        $event = new SampleEvent($data->name, null, $data->id, (array)$data->payload);
        $event->setSchemaFile("/assets/producer/schemas/Task.Created.schema.json");
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches('%' . ValidatorException::INVALIDATED_EVENT . '%');
        $this->producer->validate($event, true);
    }

    public function testSettersAndGetters()
    {
        $schemaDir = "/app";
        $this->producer->setAssetDir($schemaDir);
        $this->assertEquals($schemaDir, $this->producer->getAssetDir());
    }
}
