<?php

namespace GSoares\DiContainer\Builder;

use GSoares\DiContainer\Dto\ParameterDto;
use GSoares\DiContainer\Dto\ServiceDto;

class JsonBuilder extends AbstractBuilder
{
    /**
     * @inheritdoc
     */
    protected function decodeParameter($parameterMap)
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
    protected function decodeService($serviceMap)
    {
        $serviceDto = new ServiceDto();

        foreach ($serviceMap as $attribute => $value) {
            $serviceDto->$attribute = $value;
        }

        return $serviceDto;
    }
}