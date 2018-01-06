<?php

namespace GSoares\DiContainer;

use GSoares\DiContainer\Dto\ServiceDto;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array
     */
    private $services;

    public function __construct(array $parameters, array $services)
    {
       $this->parameters = $parameters;
       $this->services = $services;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if ($this->hasService($id)) {
            /** @var ServiceDto $serviceDto */
            $serviceDto = $this->services[$id];

            $arguments = [];

            foreach ($serviceDto->arguments as $argument) {
                if (strpos($argument, '%') === 0) {
                    $arguments[] = $this->get(str_replace('%', '', $argument));

                    continue;
                }

                $arguments[] = $argument;
            }

            $reflectionClass = new \ReflectionClass($serviceDto->class);

            return $reflectionClass->newInstanceArgs($arguments);
        }

        if ($this->hasParameter($id)) {
            return $this->parameters[$id];
        }
    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {
        return $this->hasService($id) || $this->hasParameter($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    private function hasParameter($id)
    {
        return array_key_exists($id, $this->parameters);
    }

    /**
     * @param string $id
     * @return bool
     */
    private function hasService($id)
    {
        return array_key_exists($id, $this->services);
    }
}