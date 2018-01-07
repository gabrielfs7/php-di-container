<?php

namespace GSoares\DiContainer\Builder;

use Psr\Container\ContainerInterface;

interface BuilderInterface
{

    /**
     * @return $this
     */
    public function disableCache();

    /**
     * @param array $files
     *
     * @return ContainerInterface
     */
    public function compile(array $files);

    /**
     * @param array $files
     *
     * @return ContainerInterface
     */
    public function build(array $files);
}