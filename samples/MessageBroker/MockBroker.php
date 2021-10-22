<?php

namespace Samples\MessageBroker;

use Micronative\FileCache\CacheItem;
use Micronative\FileCache\CachePool;

class MockBroker
{
    /** @var string */
    private $storageDir;

    /** @var string */
    private $storageName = 'broker.messages.storage';

    /** @var \Micronative\FileCache\CachePool */
    private $cachePool;

    /** @var string[] */
    private $messages = [];

    /**
     * MockBroker constructor.
     * @throws \Micronative\FileCache\Exceptions\CachePoolException
     */
    public function __construct(){
        $this->storageDir = dirname(__FILE__).'/storage';
        $this->cachePool = new CachePool($this->storageDir);
        $this->loadMessages();
    }

    /**
     * @throws \Micronative\FileCache\Exceptions\CachePoolException
     */
    public function __destruct()
    {
        $this->saveMessages();
    }

    /**
     * @throws \Micronative\FileCache\Exceptions\CachePoolException
     */
    private function loadMessages(){
        $item = $this->cachePool->getItem($this->storageName);
        if(!empty($item->get())) {
            $this->messages = $item->get();
        }
    }

    /**
     * @throws \Micronative\FileCache\Exceptions\CachePoolException
     */
    private function saveMessages(){
        $data = ['key' => $this->storageName, 'value' => $this->messages];
        $item = new CacheItem($data);
        $this->cachePool->save($item)->commit();
    }

    /**
     * @param string $message
     */
    public function push(string $message)
    {
        array_push($this->messages, $message);
    }

    /**
     * @return string
     */
    public function shift()
    {
        return array_shift($this->messages);
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param string[] $messages
     * @return \Samples\MessageBroker\MockBroker
     */
    public function setMessages(array $messages): MockBroker
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * @return string
     */
    public function getStorageDir(): string
    {
        return $this->storageDir;
    }

    /**
     * @param string $storageDir
     * @return \Samples\MessageBroker\MockBroker
     */
    public function setStorageDir(string $storageDir): MockBroker
    {
        $this->storageDir = $storageDir;

        return $this;
    }
}
