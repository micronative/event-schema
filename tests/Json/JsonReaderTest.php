<?php

namespace Tests\Json;

use Micronative\EventSchema\Exceptions\JsonException;
use Micronative\EventSchema\Json\JsonReader;
use PHPUnit\Framework\TestCase;

class JsonReaderTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Json\JsonReader */

    /** @var string */
    protected $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
    }

    /**
     * @covers \Micronative\EventSchema\Json\JsonReader::read
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function testReadEmptyFile()
    {
        $this->expectException(JsonException::class);
        JsonReader::read(null);
    }

    /**
     * @covers \Micronative\EventSchema\Json\JsonReader::read
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function testReadInvalidFile()
    {
        $this->expectException(JsonException::class);
        JsonReader::read("someinvalidfile");
    }

    /**
     * @covers \Micronative\EventSchema\Json\JsonReader::decode
     * @covers \Micronative\EventSchema\Json\JsonReader::read
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function testDecodeFailed()
    {
        $this->expectException(JsonException::class);
        JsonReader::decode(null);
    }

    /**
     * @covers \Micronative\EventSchema\Json\JsonReader::read
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function testRead()
    {
        $file = $this->testDir . "/assets/files/read.json";
        $json = JsonReader::read($file);
        $this->assertTrue(is_string($json));
    }

    /**
     * @covers \Micronative\EventSchema\Json\JsonReader::read
     * @covers \Micronative\EventSchema\Json\JsonReader::decode
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function testDecode()
    {
        $file = $this->testDir . "/assets/files/read.json";
        $json = JsonReader::read($file);
        $object = JsonReader::decode($json);

        $this->assertTrue(is_object($object));
        $this->assertEquals("Users.afterSaveCommit.Create", $object->event);
        $this->assertEquals("20190726032212", $object->time);
        $this->assertTrue(isset($object->payload));
        $this->assertEquals("Ken", $object->payload->user->data->name);
        $this->assertTrue(isset($object->payload->account->data->name));
        $this->assertEquals("Brighte", $object->payload->account->data->name);
    }

    /**
     * @covers \Micronative\EventSchema\Json\JsonReader::encode
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function testEncodeFailed()
    {
        $this->expectException(JsonException::class);
        JsonReader::encode(null);
    }

    /**
     * @covers \Micronative\EventSchema\Json\JsonReader::encode
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function testEncode()
    {
        $array = ["name" => "Ken"];
        $json = JsonReader::encode($array);
        $this->assertTrue(is_string($json));
    }

    /**
     * @covers \Micronative\EventSchema\Json\JsonReader::save
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function testSaveEmptyFile()
    {
        $this->expectException(JsonException::class);
        $this->expectExceptionMessage(JsonException::MISSING_JSON_FILE);
        JsonReader::save(null);
    }

    /**
     * @covers \Micronative\EventSchema\Json\JsonReader::save
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function testSaveEmptyContent()
    {
        $file = $this->testDir . "/assets/files/save.json";
        $this->expectException(JsonException::class);
        $this->expectExceptionMessage(JsonException::MISSING_JSON_CONTENT);
        JsonReader::save($file, null);
    }

    /**
     * @covers \Micronative\EventSchema\Json\JsonReader::save
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function testSave()
    {
        $file = $this->testDir . "/assets/files/save.json";
        $array = ["name" => "Ken"];
        $json = JsonReader::encode($array);
        JsonReader::save($file, $json);
        $contents = file_get_contents($file);
        $this->assertSame('{"name":"Ken"}', $contents);
    }
}
