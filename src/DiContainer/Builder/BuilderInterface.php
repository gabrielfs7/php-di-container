<?php

namespace GSoares\DiContainer\Builder;

use Psr\Container\ContainerInterface;

interface BuilderInterface
{

    /**
     * @return $this
     */
    public function enableCache();

    /**
     * @return $this
     */
    public function enableCompile();

    /**
     * @param array $containerFiles
     *
     * @return ContainerInterface
     */
    public function build($containerFiles);
}
