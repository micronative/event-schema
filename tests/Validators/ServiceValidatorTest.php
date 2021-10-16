<?php

namespace Tests\Validators;

use Micronative\EventSchema\Json\JsonReader;
use Micronative\EventSchema\Validators\ServiceValidator;
use PHPUnit\Framework\TestCase;
use Tests\Event\SampleEvent;
use Tests\Service\Samples\CreateContact;

use function Webmozart\Assert\Tests\StaticAnalysis\null;

class ServiceValidatorTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Validators\ServiceValidator */
    private $serviceValidator;

    /** @var string */
    private $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->serviceValidator = new ServiceValidator($this->testDir);
    }

    /**
     * @covers \Micronative\EventSchema\Validators\ServiceValidator::validateService
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testValidateWithEmptyJsonSchema()
    {
        $file = $this->testDir . "/assets/events/Users.afterSaveCommit.Create.json";
        $jsonObject = JsonReader::decode(JsonReader::read($file));
        $event = new SampleEvent('Users.afterSaveCommit.Create', null, null, (array)$jsonObject->payload);
        $service = new CreateContact();
        $validated = $this->serviceValidator->validateService($event, $service);
        $this->assertTrue($validated);
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testValidate()
    {
        $file = $this->testDir . "/assets/events/Users.afterSaveCommit.Create.json";
        $jsonObject = JsonReader::decode(JsonReader::read($file));
        $event = new SampleEvent('Users.afterSaveCommit.Create', null, null, (array)$jsonObject->payload);
        $service = new CreateContact();
        $service->setSchema("/assets/consumer/schemas/CreateContact.json");
        $validated = $this->serviceValidator->validateService($event, $service, true);
        $this->assertTrue($validated);

        $validator = $this->serviceValidator->getValidator();
        $this->serviceValidator->setValidator($validator);
        $this->assertSame($validator, $this->serviceValidator->getValidator());

        $this->serviceValidator->setSchemaDir($this->testDir);
        $schemaDir = $this->serviceValidator->getSchemaDir();
        $this->assertSame($schemaDir, $this->serviceValidator->getSchemaDir());
    }
}
