<?php

namespace GSoares\DiContainer\Dto\Decoder;

use GSoares\DiContainer\Dto\ParameterDto;
use GSoares\DiContainer\Dto\ServiceDto;

interface DecoderInterface
{

    /**
     * @param mixed $map
     *
     * @return ParameterDto
     */
    public function decodeParameter($map);

    /**
     * @param mixed $map
     *
     * @return ServiceDto
     */
    public function decodeService($map);
}
