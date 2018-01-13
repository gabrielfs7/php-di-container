<?php

namespace GSoares\DiContainer\Cache;

interface MethodCreatorInterface
{

    /**
     * @param array $services
     *
     * @return array
     */
    public function createByServices(array $services);

    /**
     * @param array $parameters
     *
     * @return array
     */
    public function createByParameters(array $parameters);
}
