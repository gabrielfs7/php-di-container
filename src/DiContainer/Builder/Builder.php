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
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    public function __construct(ValidatorInterface $validator, DecoderInterface $decoder)
    {
        $this->validator = $validator;
        $this->decoder = $decoder;
    }

    /**
     * @inheritdoc
     */
    public function build(array $files)
    {
        if (!class_exists('ContainerCache')) {
            $this->createCacheClass($files);
        }

        return new Container(new \ContainerCache());
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
     */
    private function createCacheClass(array $files)
    {
        $cachePath = realpath(__DIR__ . '/../../../cache');
        $classPath = $cachePath . '/ContainerCache.php';

        if (file_exists($classPath)) {
            unlink($classPath);
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

        $class = file_get_contents(__DIR__ . '/../../../template/ContainerCache.php');
        $class = str_replace('#methods#', $methods, $class);

        file_put_contents($classPath, $class);

        include $classPath;
    }
}