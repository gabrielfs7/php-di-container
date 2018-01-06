<?php

namespace GSoares\DiContainer\Builder;

use Psr\Container\ContainerInterface;

interface BuilderInterface
{

    /**
     * @param array $files
     *
     * @return ContainerInterface
     */
    public function build(array $files);
}