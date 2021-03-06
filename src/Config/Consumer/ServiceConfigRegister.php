<?php

namespace Micronative\EventSchema\Config\Consumer;

use Micronative\EventSchema\Exceptions\ConfigException;
use Micronative\EventSchema\Json\JsonReader;
use Symfony\Component\Yaml\Yaml;

class ServiceConfigRegister
{
    protected ?string $assetDir;
    protected ?array $configFiles = [];

    /** @var \Micronative\EventSchema\Config\Consumer\ServiceConfig[] $serviceConfigs */
    protected array $serviceConfigs = [];

    /** @var \Micronative\EventSchema\Config\Consumer\ServiceConfig[] $aliasConfigs */
    protected array $aliasConfigs = [];

    /**
     * ServiceConfigRegister constructor.
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
     * @return \Micronative\EventSchema\Config\Consumer\ServiceConfigRegister
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function loadServiceConfigs()
    {
        if (empty($this->configFiles)) {
            return $this;
        }
        foreach ($this->configFiles as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            switch ($ext) {
                case 'json':
                    $this->loadServiceConfigFromJson($file);
                    break;
                case 'yml':
                    $this->loadServiceConfigFromYaml($file);
                    break;
                default:
                    throw new ConfigException(ConfigException::UNSUPPORTED_FILE_FORMAT . $ext);
            }
        }

        return $this;
    }

    /**
     * @param string $serviceClass
     * @return \Micronative\EventSchema\Config\Consumer\ServiceConfig|null
     */
    public function retrieveServiceConfig(string $serviceClass)
    {
        if (isset($this->serviceConfigs[$serviceClass])) {
            return $this->serviceConfigs[$serviceClass];
        }

        if (isset($this->aliasConfigs[$serviceClass])) {
            return $this->aliasConfigs[$serviceClass];
        }

        return null;
    }

    /**
     * @param \Micronative\EventSchema\Config\Consumer\ServiceConfig $serviceConfig
     * @return \Micronative\EventSchema\Config\Consumer\ServiceConfigRegister
     */
    public function registerServiceConfig(ServiceConfig $serviceConfig)
    {
        $serviceClass = $serviceConfig->getClass();
        $this->serviceConfigs[$serviceClass] = $serviceConfig;

        if (!empty($serviceAlias = $serviceConfig->getAlias())) {
            $this->aliasConfigs[$serviceAlias] = $serviceConfig;
        }

        return $this;
    }

    /**
     * @param string|null $file
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    private function loadServiceConfigFromJson(string $file = null)
    {
        if (!empty($this->assetDir)) {
            $file = $this->assetDir . $file;
        }
        $services = JsonReader::decode(JsonReader::read($file), true);
        $this->loadFromArray($services);
    }

    /**
     * @param string|null $file
     */
    private function loadServiceConfigFromYaml(string $file = null)
    {
        if (!empty($this->assetDir)) {
            $file = $this->assetDir . $file;
        }
        $services = Yaml::parseFile($file);
        $this->loadFromArray($services);
    }

    /**
     * @param array|null $services
     */
    private function loadFromArray(array $services = null)
    {
        foreach ($services as $service) {
            if (isset($service['service'])) {
                $class = $service['service'];
                $alias = isset($service['alias']) ? $service['alias'] : null;
                $callbacks = isset($service['callbacks']) ? $service['callbacks'] : null;
                $serviceConfig = new ServiceConfig($class, $alias, $callbacks);
                $this->registerServiceConfig($serviceConfig);
            }
        }
    }

    /**
     * @return string|null
     */
    public function getAssetDir()
    {
        return $this->assetDir;
    }

    /**
     * @param string|null $assetDir
     * @return \Micronative\EventSchema\Config\Consumer\ServiceConfigRegister
     */
    public function setAssetDir(?string $assetDir): ServiceConfigRegister
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
     * @param string[] $configFiles
     * @return \Micronative\EventSchema\Config\Consumer\ServiceConfigRegister
     */
    public function setConfigFiles(array $configFiles = null)
    {
        $this->configFiles = $configFiles;

        return $this;
    }

    /**
     * @return array
     */
    public function getServiceConfigs()
    {
        return $this->serviceConfigs;
    }

    /**
     * @param array|null $serviceConfigs
     * @return \Micronative\EventSchema\Config\Consumer\ServiceConfigRegister
     */
    public function setServiceConfigs(array $serviceConfigs = null)
    {
        $this->serviceConfigs = $serviceConfigs;

        return $this;
    }
}
