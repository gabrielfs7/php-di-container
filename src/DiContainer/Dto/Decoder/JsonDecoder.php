<?php

namespace GSoares\DiContainer\Dto\Decoder;

use GSoares\DiContainer\Dto\CallDto;
use GSoares\DiContainer\Dto\ParameterDto;
use GSoares\DiContainer\Dto\ServiceDto;
use GSoares\DiContainer\Exception\InvalidMapException;

class JsonDecoder implements DecoderInterface
{

    /**
     * @inheritdoc
     */
    public function decodeParameter($map)
    {
        $this->validateMap($map);

        $parameterDto = new ParameterDto();

        foreach ($map as $parameter => $value) {
            $parameterDto->id = $parameter;
            $parameterDto->value = $value;
        }

        return $parameterDto;
    }

    /**
     * @inheritdoc
     */
    public function decodeService($map)
    {
        $this->validateMap($map);

        $serviceDto = new ServiceDto();

        foreach ($map as $attribute => $value) {
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

    /**
     * @param mixed $map
     *
     * @throws InvalidMapException
     */
    private function validateMap($map)
    {
        if (!$map instanceof \stdClass) {
            throw new InvalidMapException("Map is not instance of stdClass");
        }

        $arrayMap = (array) $map;

        if (empty($arrayMap)) {
            throw new InvalidMapException("Map is empty");
        }
    }
}
