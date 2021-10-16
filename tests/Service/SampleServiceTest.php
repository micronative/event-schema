<?php

namespace Tests\Service;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tests\Service\Samples\CreateContact;
use Tests\Service\Samples\SampleContainer;

class SampleServiceTest extends TestCase
{
    /** @coversDefaultClass \Tests\Service\Samples\CreateContact */
    protected $sampleService;

    public function setUp(): void
    {
        parent::setUp();
        $this->sampleService = new CreateContact();
    }

    public function testSettersAndGetters()
    {
        $this->sampleService
            ->setName('Create.Contact')
            ->setSchema('json_schema_file')
            ->setContainer(new SampleContainer());

        $this->assertEquals('Create.Contact', $this->sampleService->getName());
        $this->assertEquals('json_schema_file', $this->sampleService->getSchema());
        $this->assertInstanceOf(ContainerInterface::class, $this->sampleService->getContainer());
    }
}
