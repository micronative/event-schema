<?php

namespace Tests\Config\Consumer;

use Micronative\EventSchema\Config\Consumer\EventConfig;
use Micronative\EventSchema\Config\Consumer\EventConfigRegister;
use Micronative\EventSchema\Exceptions\ConfigException;
use PHPUnit\Framework\TestCase;

class EventConfigRegisterTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Config\Consumer\EventConfigRegister */
    protected EventConfigRegister $eventConfigRegister;
    protected string $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(dirname(__FILE__)));
        $this->eventConfigRegister = new EventConfigRegister($this->testDir,
                                                             [
                                                                 "/assets/consumer/configs/events.yml",
                                                                 "/assets/consumer/configs/events.json"
                                                             ]
        );
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function testLoadEventsWithEmptyConfigs()
    {
        $this->eventConfigRegister->setConfigFiles(null);
        $this->eventConfigRegister->loadEventConfigs();
        $this->assertEquals([], $this->eventConfigRegister->getEventConfigs());
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function testLoadEventsWithUnsupportedFiles()
    {
        $this->eventConfigRegister->setConfigFiles([$this->testDir . "/assets/configs/consumer/events.csv"]);
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(ConfigException::UNSUPPORTED_FILE_FORMAT . 'csv');
        $this->eventConfigRegister->loadEventConfigs();
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function testLoadEventConfigs()
    {
        $this->eventConfigRegister->loadEventConfigs();
        $eventConfigs = $this->eventConfigRegister->getEventConfigs();

        $this->assertTrue(is_array($eventConfigs));
        $this->assertTrue(isset($eventConfigs["User.Created"]));
        $this->assertTrue(isset($eventConfigs["User.Updated"]));
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function testRegisterEvent()
    {
        $this->eventConfigRegister->loadEventConfigs();
        $config1 = new EventConfig("Event.Name", null, null, ["FirstServiceClass"]);
        $config2 = new EventConfig("Event.Name", null, null, ["SecondServiceClass"]);
        $this->eventConfigRegister->registerEventConfig($config1);
        $this->eventConfigRegister->registerEventConfig($config2);
        $eventConfigs = $this->eventConfigRegister->getEventConfigs();
        $config = current($eventConfigs["Event.Name"]);

        $this->assertIsArray($eventConfigs);
        $this->assertArrayHasKey("Event.Name", $eventConfigs);
        $this->assertInstanceOf(EventConfig::class, $config);
        $this->assertEquals(["FirstServiceClass"], $config->getServiceClasses());
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function testRetrieveEvent()
    {
        $this->eventConfigRegister->loadEventConfigs();
        $config = new EventConfig("Event.Name", ['1.0.0', '2.0.0'], null, ["SomeServiceClass"]);
        $this->eventConfigRegister->registerEventConfig($config);
        /** @var EventConfig $eventConfig */
        $eventConfig = $this->eventConfigRegister->retrieveEventConfig("Event.Name", '1.0.0');
        $noneExistingEvent1 = $this->eventConfigRegister->retrieveEventConfig("Not.Existing.Name");
        $noneExistingEvent2 = $this->eventConfigRegister->retrieveEventConfig("Event.Name", "3.0.0");

        $this->assertInstanceOf(EventConfig::class, $eventConfig);
        $this->assertEquals("Event.Name", $eventConfig->getName());
        $this->assertEquals(["SomeServiceClass"], $eventConfig->getServiceClasses());
        $this->assertNull($noneExistingEvent1);
        $this->assertNull($noneExistingEvent2);
    }

    public function testGetterAndSetters()
    {
        $configs = [];
        $this->eventConfigRegister->setConfigFiles($configs);
        $this->assertSame($configs, $this->eventConfigRegister->getConfigFiles());

        $events = [];
        $this->eventConfigRegister->setEventConfigs($events);
        $this->assertSame($events, $this->eventConfigRegister->getEventConfigs());
    }
}
