<?php

namespace Tests\Config\Producer;

use Micronative\EventSchema\Config\Producer\EventConfig;
use PHPUnit\Framework\TestCase;

class EventConfigTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Config\Producer\EventConfig */
    private EventConfig $eventConfig;

    public function testSettersAndGetters()
    {
        $this->eventConfig = new EventConfig('Event.Name');
        $this->eventConfig
            ->setName('SomeName')
            ->setVersion("1.0.0")
            ->setSchemaFile('locationToSchemaFile');

        $this->assertEquals('SomeName', $this->eventConfig->getName());
        $this->assertEquals('1.0.0', $this->eventConfig->getVersion());
        $this->assertEquals('locationToSchemaFile', $this->eventConfig->getSchemaFile());
    }
}
