<?php

namespace Tests\Event;

use PHPUnit\Framework\TestCase;

class SampleEventTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Event\AbstractEvent */

    /** @var string */
    protected $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
    }

    public function testToJson()
    {
        $event = new SampleEvent("SomeName");
        $event->setId('1')
            ->setName("Test.Event.Name")
            ->setPayload((object)["name" => "Ken"]);
        $this->assertSame($event->getId(), '1');
        $this->assertEquals((object)["name" => "Ken"], $event->getPayload());

        $json = $event->jsonSerialize();
        $this->assertTrue(is_string($json));
        $this->assertEquals('{"name":"Test.Event.Name","id":"1","payload":{"name":"Ken"}}', $json);

        $event = new SampleEvent("SomeName");
        $event->setName("Users.afterSaveCommit.Create");
        $event->setPayload(["user" => ["data" => ["name" => "Ken"]], "account" => ["data" => ["name" => "Brighte"]]]);
        $json = $event->jsonSerialize();
        $this->assertTrue(is_string($json));
        $this->assertEquals(
            '{"name":"Users.afterSaveCommit.Create","id":null,"payload":{"user":{"data":{"name":"Ken"}},"account":{"data":{"name":"Brighte"}}}}',
            $json
        );

        $event = new SampleEvent('SomeEvent');
        $event->unserialize('{"name":"Test.Event.Name","id":"1","payload":{"name":"Ken"}}');
        $this->assertEquals("Test.Event.Name", $event->getName());
        $this->assertEquals(1, $event->getId());
        $this->assertEquals(['name' => 'Ken'], $event->getPayload());

        $event = new SampleEvent('Sample.Event', null,'111');
        $event->setVersion('1.0.0');
        $this->assertSame( '111', $event->getId());
        $this->assertSame('Sample.Event', $event->getName());
        $this->assertSame('1.0.0', $event->getVersion());
    }
}
