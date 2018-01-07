<?php

namespace GSoares\DiContainer\Dto\Decoder;

use GSoares\DiContainer\Dto\ParameterDto;
use GSoares\DiContainer\Dto\ServiceDto;

class JsonDecoder implements DecoderInterface
{

    /**
     * @inheritdoc
     */
    public function decodeParameter($parameterMap)
    {
        $parameterDto = new ParameterDto();

        foreach ($parameterMap as $parameter => $value) {
            $parameterDto->id = $parameter;
            $parameterDto->value = $value;
        }

        return $parameterDto;
    }

    /**
     * @inheritdoc
     */
    public function decodeService($serviceMap)
    {
        $serviceDto = new ServiceDto();

        foreach ($serviceMap as $attribute => $value) {
            $serviceDto->$attribute = $value;
        }

        return $serviceDto;
    }
}