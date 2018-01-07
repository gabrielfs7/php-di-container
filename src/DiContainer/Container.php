<?php

namespace GSoares\DiContainer;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{

    /**
     * @var \ArrayAccess
     */
    private $registries;

    public function __construct(\ArrayAccess $registries)
    {
       $this->registries = $registries;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->registries->offsetGet($id);
        }
    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {
        return $this->registries->offsetExists($id);
    }
}