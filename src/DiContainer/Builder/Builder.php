<?php

namespace GSoares\DiContainer\Builder;

use GSoares\DiContainer\Container;
use GSoares\DiContainer\Dto\Decoder\DecoderInterface;
use GSoares\DiContainer\Dto\ParameterDto;
use GSoares\DiContainer\Dto\ServiceDto;
use GSoares\DiContainer\File\Validator\ValidatorInterface;

class Builder implements BuilderInterface
{

    /**
     * @var array
     */
    private $registries;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @var bool
     */
    private $enableCache;

    /**
     * @var string
     */
    private $containerCachePath;


    /**
     * @var string
     */
    private $containerTemplatePath;

    public function __construct($containerCachePath, ValidatorInterface $validator, DecoderInterface $decoder)
    {
        $this->validator = $validator;
        $this->decoder = $decoder;
        $this->enableCache = true;
        $this->registries = [];
        $this->containerTemplatePath = realpath(__DIR__ . '/../../../template/ContainerCache.php');
        $this->setContainerCachePath($containerCachePath);
    }

    /**
     * @inheritdoc
     */
    public function disableCache()
    {
        $this->enableCache = false;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function compile(array $files)
    {
        $cacheClass = $this->createCacheClass($files);

        $container = new Container($cacheClass);

        if (!$cacheClass->isCompiled()) {
            foreach ($this->registries as $id) {
                $container->get($id);
            }

            $class = file_get_contents($this->getContainerCacheFile());
            $class = str_replace('#isCompiled#', 'return true;', $class);

            file_put_contents($this->getContainerCacheFile(), $class);
        }

        return $container;
    }

    /**
     * @inheritdoc
     */
    public function build(array $files)
    {
        return new Container($this->createCacheClass($files));
    }

    /**
     * @return string
     */
    private function getContainerCacheFile()
    {
        return $this->containerCachePath . '/ContainerCache.php';
    }

    /**
     * @param array $servicesMap
     *
     * @return array
     */
    private function mapServices(array $servicesMap)
    {
        $services = [];

        array_walk(
            $servicesMap,
            function ($serviceMap) use (&$services)
            {
                $serviceDto = $this->decoder->decodeService($serviceMap);

                $services[$serviceDto->id] = $this->createMethodByService($serviceDto);
            }
        );

        $this->registries = array_keys($services);

        return $services;
    }

    /**
     * @param array $parametersMap
     *
     * @return array
     */
    private function mapParameters(array $parametersMap)
    {
        $parameters = [];

        array_walk(
            $parametersMap,
            function ($parameterMap) use (&$parameters)
            {
                $parameterDto = $this->decoder->decodeParameter($parameterMap);

                $parameters[$parameterDto->id] = $this->createMethodByParameter($parameterDto);
            }
        );

        $this->registries = array_keys($parameters);

        return $parameters;
    }

    /**
     * @param ParameterDto $parameterDto
     *
     * @return ParameterDto
     */
    private function createMethodByParameter(ParameterDto $parameterDto)
    {
        $method = $this->clearId($parameterDto->id);
        $parameter = var_export($parameterDto->value, true);
        $parameter = str_replace("stdClass::__set_state", "(object)", $parameter);

        $method = "
        /**
         * @return " . gettype($parameter) . "
         */
        public function get_$method()
        {
            return $parameter;
        }";

        return $method;
    }

    /**
     * @param ServiceDto $serviceDto
     *
     * @return string
     */
    private function createMethodByService(ServiceDto $serviceDto)
    {
        $method = $this->clearId($serviceDto->id);
        $className = $serviceDto->class;
        $arguments = [];

        foreach ($serviceDto->arguments as $argument) {
            if (strpos($argument, '%') === 0) {
                $arguments[] = "\$this->get_" . $this->clearId(str_replace('%', '', $argument)) . "()";

                continue;
            }

            $arguments[] = $argument;
        }

        $argumentsString = implode(', ', $arguments);

        $method = "
        /**
         * @return $className
         */
        public function get_$method()
        {
            \$service = new $className($argumentsString);

            return \$service;
        }";

        return $method;
    }

    /**
     * @param string $id
     *
     * @return string
     */
    private function clearId($id)
    {
        return preg_replace('/[^A-Za-z0-9_]/', '_', $id);
    }

    /**
     * @param array $files
     *
     * @return \ContainerCache
     */
    private function createCacheClass(array $files)
    {
        if (class_exists('ContainerCache')) {
            return new \ContainerCache();
        }

        if (!file_exists($this->getContainerCacheFile()) || !$this->enableCache) {
            if (file_exists($this->getContainerCacheFile())) {
                unlink($this->getContainerCacheFile());
            }

            $methods = '';

            array_walk(
                $files,
                function ($file) use (&$methods) {
                    $this->validator
                        ->validate($file);

                    $methods .= implode(PHP_EOL, $this->mapServices($this->validator->getServicesMap()));
                    $methods .= implode(PHP_EOL, $this->mapParameters($this->validator->getParametersMap()));
                }
            );

            $class = file_get_contents($this->containerTemplatePath);
            $class = str_replace('#methods#', $methods, $class);

            file_put_contents($this->getContainerCacheFile(), $class);
        }

        include $this->getContainerCacheFile();

        return new \ContainerCache();
    }

    /**
     * @param $containerCachePath
     *
     * @return $this
     */
    private function setContainerCachePath($containerCachePath)
    {
        if (!file_exists($containerCachePath) ||
            !is_dir($containerCachePath) ||
            !is_writable($containerCachePath)) {
            throw new \InvalidArgumentException("Invalid container cache path [$containerCachePath]");
        }

        $this->containerCachePath = $containerCachePath;

        return $this;
    }
}