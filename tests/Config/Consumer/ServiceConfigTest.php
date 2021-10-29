<?php

namespace Tests\Config;

use Micronative\EventSchema\Config\Consumer\ServiceConfig;
use PHPUnit\Framework\TestCase;

class ServiceConfigTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Config\Consumer\ServiceConfig */
    private ServiceConfig $serviceConfig;

    public function testSettersAndGetters()
    {
        $this->serviceConfig = new ServiceConfig('Service.Class');
        $this->serviceConfig
            ->setClass('SomeClass')
            ->setAlias('SomeAlias')
            ->setCallbacks(['SomeCallbacks']);

        $this->assertEquals('SomeClass', $this->serviceConfig->getClass());
        $this->assertEquals('SomeAlias', $this->serviceConfig->getAlias());
        $this->assertEquals(['SomeCallbacks'], $this->serviceConfig->getCallbacks());
    }
}
