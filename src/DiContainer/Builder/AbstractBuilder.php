<?php

namespace GSoares\DiContainer\Builder;

use GSoares\DiContainer\Container;
use GSoares\DiContainer\Dto\ParameterDto;
use GSoares\DiContainer\Dto\ServiceDto;
use GSoares\DiContainer\File\Validator\ValidatorInterface;

abstract class AbstractBuilder implements BuilderInterface
{

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @inheritdoc
     */
    public function build(array $files)
    {
        $parameters = [];
        $services = [];

        array_walk(
            $files,
            function ($file) use (&$parameters, &$services) {
                $this->validator
                    ->validate($file);

                $parametersMap = $this->validator->getParametersMap();
                $servicesMap = $this->validator->getServicesMap();

                array_walk(
                    $parametersMap,
                    function ($parameterMap) use (&$parameters)
                    {
                        $parameter = $this->decodeParameter($parameterMap);

                        $parameters[$parameter->id] = $parameter->value;
                    }
                );

                array_walk(
                    $servicesMap,
                    function ($serviceMap) use (&$services)
                    {
                        $service = $this->decodeService($serviceMap);

                        $services[$service->id] = $service;
                    }
                );
            }
        );

        return new Container($parameters, $services);
    }

    /**
     * @param mixed $parameterMap
     *
     * @return ParameterDto
     */
    abstract protected function decodeParameter($parameterMap);

    /**
     * @param mixed $serviceMap
     *
     * @return ServiceDto
     */
    abstract protected function decodeService($serviceMap);
}