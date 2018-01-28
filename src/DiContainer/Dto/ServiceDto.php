<?php

namespace GSoares\DiContainer\Dto;

class ServiceDto
{

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $class;

    /**
     * @var bool
     */
    public $abstract = false;

    /**
     * @var string
     */
    public $parent;

    /**
     * @var array
     */
    public $arguments = [];

    /**
     * @var CallDto[]
     */
    public $call = [];

    /**
     * @param ServiceDto $serviceDto
     *
     * @return ServiceDto
     */
    public function merge(ServiceDto $serviceDto)
    {
        if (empty($this->call)) {
            $this->call = $serviceDto->call;
        }

        if (empty($this->arguments)) {
            $this->arguments = $serviceDto->arguments;
        }

        return $serviceDto;
    }
}
