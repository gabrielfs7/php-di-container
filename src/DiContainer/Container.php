<?php

namespace GSoares\DiContainer;

use GSoares\DiContainer\Cache\CacheInterface;
use GSoares\DiContainer\Exception\NotFountException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->cache->offsetGet($id);
        }

        throw new NotFountException("Container registry [$id] not found");
    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {
        return $this->cache->offsetExists($id);
    }
}
