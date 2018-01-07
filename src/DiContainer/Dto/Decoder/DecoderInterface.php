<?php

namespace GSoares\DiContainer\Dto\Decoder;

use GSoares\DiContainer\Dto\ParameterDto;
use GSoares\DiContainer\Dto\ServiceDto;

interface DecoderInterface
{

    /**
     * @param mixed $parameterMap
     *
     * @return ParameterDto
     */
    public function decodeParameter($parameterMap);

    /**
     * @param mixed $serviceMap
     *
     * @return ServiceDto
     */
    public function decodeService($serviceMap);
}