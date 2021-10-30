<?php

namespace Micronative\EventSchema\Config\Consumer;

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
            $version = isset($event['version']) ? $event['version'] : null;
            $schemaFile = isset($event['schema']) ? $event['schema'] : null;
            $services = isset($event['services']) ? $event['services'] : null;
            $eventConfig = new EventConfig($name, $version, $schemaFile, $services);
            $this->registerEventConfig($eventConfig);
        }
    }

    /**
     * @param string $eventName
     * @param string|null $version
     * @return \Micronative\EventSchema\Config\AbstractEventConfig|null
     */
    public function retrieveEventConfig(string $eventName, ?string $version = null)
    {
        if (isset($this->eventConfigs[$eventName])) {
            /** @var \Micronative\EventSchema\Config\AbstractEventConfig $eventConfig */
            foreach ($this->eventConfigs[$eventName] as $eventConfig) {
                if ($version === $eventConfig->getVersion()) {
                    return $eventConfig;
                }
            }
        }

        return null;
    }
}
