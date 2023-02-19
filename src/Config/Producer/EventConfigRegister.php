<?php

namespace Micronative\EventSchema\Config\Producer;

use Micronative\EventSchema\Config\AbstractEventConfigRegister;

class EventConfigRegister extends AbstractEventConfigRegister
{
    /**
     * @param array|null $events
     */
    protected function loadFromArray(array $events = null)
    {
        foreach ($events as $event) {
            if (!isset($event['event'])) {
                continue;
            }
            $name = $event['event'];
            $version = $event['version'] ?? null;
            $schemaFile = $event['schema'] ?? null;
            $eventConfig = new EventConfig($name, $version, $schemaFile);
            $this->registerEventConfig($eventConfig);
        }
    }

    /**
     * @param string $eventName
     * @param string|null $version
     * @return \Micronative\EventSchema\Config\Producer\EventConfig|null
     */
    public function retrieveEventConfig(string $eventName, string $version = null)
    {
        if (isset($this->eventConfigs[$eventName])) {
            /** @var \Micronative\EventSchema\Config\Producer\EventConfig $eventConfig */
            foreach ($this->eventConfigs[$eventName] as $eventConfig) {
                if ($version == $eventConfig->getVersion()) {
                    return $eventConfig;
                }
            }
        }

        return null;
    }
}
