<?php

namespace Tests\Event;

use JsonSchema\Validator;
use Micronative\EventSchema\Event\EventValidator;
use Micronative\EventSchema\Exceptions\JsonException;
use Micronative\EventSchema\Exceptions\ValidatorException;
use PHPUnit\Framework\TestCase;

class EventValidatorTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Event\EventValidator */
    protected EventValidator $validator;
    protected string $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->validator = new EventValidator($this->testDir, new Validator());
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testValidateWithInvalidJsonEvent()
    {
        $event = new SampleInvalidEvent('SomeName');
        $event->setSchemaFile("/assets/schemas/events/User.json");
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage(ValidatorException::INVALID_JSON);
        $this->validator->validateEvent($event);
    }

    /**
     * @covers \Micronative\EventSchema\Event\EventValidator::validateEvent
     */
    public function testValidateWithInvalidSchema()
    {
        $event = new SampleEvent("SomeName");
        $event->setSchemaFile("/assets/producer/schemas/InvalidSchema.json");
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage(ValidatorException::INVALID_SCHEMA);
        $this->validator->validateEvent($event);
    }

    /**
     * @covers \Micronative\EventSchema\Event\EventValidator::validateEvent
     */
    public function testValidateFailed()
    {
        $event = new SampleEvent("SomeName");
        $event->setSchemaFile("/assets/producer/schemas/User.Created.schema.json");
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches("%" . ValidatorException::INVALIDATED_EVENT . "%");
        $this->validator->validateEvent($event);
    }

    /**
     * @covers \Micronative\EventSchema\Event\EventValidator::validateEvent
     */
    public function testValidateWithNoneExistingSchema()
    {
        $event = new SampleEvent("SomeName");
        $event->setSchemaFile("/assets/producer/schemas/NoneExistingSchema.json");
        $this->expectException(JsonException::class);
        $this->expectExceptionMessageMatches("%" . JsonException::INVALID_JSON_FILE . "%");
        $this->validator->validateEvent($event);
    }

    /**
     * @covers \Micronative\EventSchema\Event\EventValidator::validateEvent
     */
    public function testValidateSuccessfulWithEmptySchema()
    {
        $event = new SampleEvent("SomeName");
        $event->setName('User.Created')->setPayload(["name" => "Ken"]);
        $validated = $this->validator->validateEvent($event, true);
        $this->assertTrue($validated);
    }

    /**
     * @covers \Micronative\EventSchema\Event\EventValidator::validateEvent
     */
    public function testValidateSuccessful()
    {
        $event = new SampleEvent("SomeName");
        $event->setName('User.Created')->setPayload(["name" => "Ken"]);
        $event->setSchemaFile("/assets/producer/schemas/User.Created.schema.json");
        $validated = $this->validator->validateEvent($event, true);
        $this->assertTrue($validated);
    }
}
