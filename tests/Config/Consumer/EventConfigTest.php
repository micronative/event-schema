<?php

namespace Tests\Config\Consumer;

use Micronative\EventSchema\Config\Consumer\EventConfig;
use PHPUnit\Framework\TestCase;

class EventConfigTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Config\Consumer\EventConfig */
    private $eventConfig;

    public function testSettersAndGetters()
    {
        $this->eventConfig = new EventConfig('Event.Name');
        $this->eventConfig
            ->setName('SomeName')
            ->setVersion("1.0.0")
            ->setServiceClasses(['ServiceClass']);

        $this->assertEquals('SomeName', $this->eventConfig->getName());
        $this->assertEquals('1.0.0', $this->eventConfig->getVersion());
        $this->assertEquals(['ServiceClass'], $this->eventConfig->getServiceClasses());
    }
}
