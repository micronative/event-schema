<?php

namespace Samples\MessageBroker;

use Micronative\FileCache\CacheItem;
use Micronative\FileCache\CachePool;

class Broker
{
    private string $storageDir;
    private string $storageName = 'broker.messages.storage';
    private CachePool $cachePool;
    private array $messages = [];

    /**
     * Broker constructor.
     * @param string $storageDir
     * @throws \Micronative\FileCache\Exceptions\CachePoolException
     */
    public function __construct(string $storageDir)
    {
        $this->storageDir = $storageDir;
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
    private function loadMessages()
    {
        $item = $this->cachePool->getItem($this->storageName);
        if (!empty($item->get())) {
            $this->messages = $item->get();
        }
    }

    /**
     * @throws \Micronative\FileCache\Exceptions\CachePoolException
     */
    private function saveMessages()
    {
        $data = ['key' => $this->storageName, 'value' => $this->messages];
        $item = new CacheItem($data);
        $this->cachePool->save($item)->commit();
    }

    /**
     * @param string $message
     * @param string $topic
     * @return bool
     */
    public function push(string $message, string $topic)
    {
        $this->messages[$topic][] = $message;

        return true;
    }

    /**
     * @param string $topic
     * @return null
     */
    public function pull(string $topic)
    {
        if (!isset($this->messages[$topic])) {
            return null;
        }

        $messages = $this->messages[$topic];
        $message = array_shift($messages);
        $this->messages[$topic] = $messages;

        return $message;
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
     * @return \Samples\MessageBroker\Broker
     */
    public function setMessages(array $messages): Broker
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
     * @return \Samples\MessageBroker\Broker
     */
    public function setStorageDir(string $storageDir): Broker
    {
        $this->storageDir = $storageDir;

        return $this;
    }
}
