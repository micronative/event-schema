<?php

namespace Micronative\EventSchema\Config;

use Micronative\EventSchema\Exceptions\ConfigException;
use Micronative\EventSchema\Json\JsonReader;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractEventConfigRegister
{
    /** @var string|null */
    protected $assetDir;

    /** @var string[] $configFiles */
    protected $configFiles = [];

    /** @var \Micronative\EventSchema\Config\AbstractEventConfig $eventConfigs */
    protected $eventConfigs = [];

    /**
     * AbstractEventRegister constructor.
     *
     * @param string|null $assetDir
     * @param array|null $files
     */
    public function __construct(string $assetDir = null, array $files = null)
    {
        $this->assetDir = $assetDir;
        $this->configFiles = $files;
    }

    /**
     * @param string $eventName
     * @param string|null $version
     * @return \Micronative\EventSchema\Config\AbstractEventConfig|null
     */
    abstract public function retrieveEventConfig(string $eventName, string $version = null);

    /**
     * @param array|null $events
     */
    abstract protected function loadFromArray(array $events = null);

    /**
     * @return \Micronative\EventSchema\Config\AbstractEventConfigRegister
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function loadEventConfigs()
    {
        if (empty($this->configFiles)) {
            return $this;
        }

        foreach ($this->configFiles as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            switch ($ext) {
                case 'json':
                    $this->loadEventConfigFromJson($file);
                    break;
                case 'yml':
                    $this->loadEventConfigFromYaml($file);
                    break;
                default:
                    throw new ConfigException(ConfigException::UNSUPPORTED_FILE_FORMAT . $ext);
            }
        }

        return $this;
    }

    /**
     * @param \Micronative\EventSchema\Config\AbstractEventConfig $eventConfig
     * @return \Micronative\EventSchema\Config\AbstractEventConfigRegister
     */
    public function registerEventConfig(AbstractEventConfig $eventConfig)
    {
        $eventName = $eventConfig->getName();
        if (!isset($this->eventConfigs[$eventName])) {
            $this->eventConfigs[$eventName] = [$eventConfig];
        } else {
            $this->eventConfigs[$eventName][] = $eventConfig;
        }

        return $this;
    }

    /**
     * @param string|null $file
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    protected function loadEventConfigFromJson(string $file = null)
    {
        if (!empty($this->assetDir)) {
            $file = $this->assetDir . $file;
        }
        $events = JsonReader::decode(JsonReader::read($file), true);
        $this->loadFromArray($events);
    }

    /**
     * @param string|null $file
     */
    protected function loadEventConfigFromYaml(string $file = null)
    {
        if (!empty($this->assetDir)) {
            $file = $this->assetDir . $file;
        }
        $events = Yaml::parseFile($file);
        $this->loadFromArray($events);
    }

    /**
     * @return string|null
     */
    public function getAssetDir(): ?string
    {
        return $this->assetDir;
    }

    /**
     * @param string|null $assetDir
     * @return \Micronative\EventSchema\Config\AbstractEventConfigRegister
     */
    public function setAssetDir(?string $assetDir): AbstractEventConfigRegister
    {
        $this->assetDir = $assetDir;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfigFiles()
    {
        return $this->configFiles;
    }

    /**
     * @param array|null $configFiles
     * @return \Micronative\EventSchema\Config\AbstractEventConfigRegister
     */
    public function setConfigFiles(array $configFiles = null)
    {
        $this->configFiles = $configFiles;

        return $this;
    }

    /**
     * @return \Micronative\EventSchema\Config\AbstractEventConfig[]
     */
    public function getEventConfigs()
    {
        return $this->eventConfigs;
    }

    /**
     * @param \Micronative\EventSchema\Config\AbstractEventConfig[]|null $eventConfigs
     * @return \Micronative\EventSchema\Config\AbstractEventConfigRegister
     */
    public function setEventConfigs(array $eventConfigs = null)
    {
        $this->eventConfigs = $eventConfigs;

        return $this;
    }
}
