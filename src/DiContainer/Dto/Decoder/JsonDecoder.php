<?php

namespace GSoares\DiContainer\Dto\Decoder;

use GSoares\DiContainer\Dto\CallDto;
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
            if ($attribute == 'call') {
                $serviceDto->call = $this->calls($value);

                continue;
            }

            $serviceDto->$attribute = $value;
        }

        return $serviceDto;
    }

    /**
     * @param array $calls
     * @return array
     */
    private function calls(array $calls)
    {
        $callsDto = [];

        foreach ($calls as $call) {
            $callDto = new CallDto();
            $callDto->method = $call->method;
            $callDto->arguments = $call->arguments;

            $callsDto[] = $callDto;
        }

        return $callsDto;
    }
}
