<?php

namespace Tests\Command;

use Micronative\EventSchema\Command\ServiceRollbackCommand;
use Micronative\EventSchema\Validators\ServiceValidator;
use PHPUnit\Framework\TestCase;
use Tests\Service\Samples\CreateContact;
use Tests\Service\Samples\SampleEvent;

class ServiceRollbackCommandTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Command\ServiceRollbackCommand */
    private $command;

    public function setUp(): void
    {
        parent::setUp();
        $testDir = dirname(dirname(__FILE__));
        $validator = new ServiceValidator($testDir);
        $event = new SampleEvent('Test.Event.Name', 1, ['name' => 'Ken']);
        $service = new CreateContact();
        $this->command = new ServiceRollbackCommand($validator, $service, $event);
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function testExecute()
    {
        $result = $this->command->execute();
        $this->assertEquals('Contact creation has been rollback.', $result);
    }
}
