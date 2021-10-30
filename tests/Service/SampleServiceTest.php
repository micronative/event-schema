<?php

namespace Tests\Service;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tests\Service\Samples\CreateContact;
use Tests\Service\Samples\SampleContainer;

class SampleServiceTest extends TestCase
{
    /** @coversDefaultClass \Tests\Service\Samples\CreateContact */
    protected CreateContact $sampleService;

    public function setUp(): void
    {
        parent::setUp();
        $this->sampleService = new CreateContact();
    }

    public function testSettersAndGetters()
    {
        $this->sampleService->setContainer(new SampleContainer());
        $this->assertInstanceOf(ContainerInterface::class, $this->sampleService->getContainer());
    }
}
