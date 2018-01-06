<?php

namespace GSoares\DiContainer\Builder;

use GSoares\DiContainer\Container;
use GSoares\DiContainer\File\Validator\ValidatorInterface;

class JsonBuilder implements BuilderInterface
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
                        foreach ($parameterMap as $parameter => $value) {
                            $parameters[$parameter] = $value;
                        }
                    }
                );

                array_walk(
                    $servicesMap,
                    function ($serviceMap) use (&$services)
                    {
                        $services[$serviceMap->id] = $serviceMap;
                    }
                );
            }
        );

        return new Container($parameters, $services);
    }
}