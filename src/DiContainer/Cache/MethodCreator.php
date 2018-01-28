<?php

namespace GSoares\DiContainer\Cache;

use GSoares\DiContainer\Dto\Decoder\DecoderInterface;
use GSoares\DiContainer\Dto\ParameterDto;
use GSoares\DiContainer\Dto\ServiceDto;
use GSoares\DiContainer\Exception\NotFountException;

class MethodCreator implements MethodCreatorInterface
{

    /**
     * @var DecoderInterface
     */
    private $decoder;

    public function __construct(DecoderInterface $decoder)
    {
        $this->decoder = $decoder;
    }

    /**
     * @param array $servicesMap
     *
     * @return array
     */
    public function createByServices(array $servicesMap)
    {
        $services = [];
        $abstractions = [];
        $inheritors = [];

        array_walk(
            $servicesMap,
            function ($serviceMap) use (&$services, &$abstractions, &$inheritors) {
                $serviceDto = $this->decoder->decodeService($serviceMap);

                if ($serviceDto->parent) {
                    $inheritors[$serviceDto->id] = $serviceDto;

                    return;
                }

                if ($serviceDto->abstract) {
                    $abstractions[$serviceDto->id] = $serviceDto;

                    return;
                }

                $services[$serviceDto->id] = $this->createMethodByService($serviceDto);
            }
        );

        array_walk(
            $inheritors,
            function ($inheritor) use ($abstractions, &$services) {
                /** @var ServiceDto $inheritor */
                if (!isset($abstractions[$inheritor->parent])) {
                    throw new NotFountException(
                        sprintf(
                            'Abstract service [%s] not found',
                            $inheritor->parent
                        )
                    );
                }

                /** @var ServiceDto $abstraction */
                $abstraction = $abstractions[$inheritor->parent];

                $inheritor->merge($abstraction);

                $services[$inheritor->id] = $this->createMethodByService($inheritor);
            }
        );

        return $services;
    }

    /**
     * @param array $parametersMap
     *
     * @return array
     */
    public function createByParameters(array $parametersMap)
    {
        $parameters = [];

        array_walk(
            $parametersMap,
            function ($parameterMap) use (&$parameters) {
                $parameterDto = $this->decoder->decodeParameter($parameterMap);

                $parameters[$parameterDto->id] = $this->createMethodByParameter($parameterDto);
            }
        );

        return $parameters;
    }

    /**
     * @param ParameterDto $parameterDto
     *
     * @return ParameterDto
     */
    private function createMethodByParameter(ParameterDto $parameterDto)
    {
        $parameter = var_export($parameterDto->value, true);
        $parameter = str_replace("stdClass::__set_state", "(object)", $parameter);

        return sprintf(
            $this->getParameterMethodBody(),
            gettype($parameter),
            $this->createMethodName($parameterDto->id),
            $parameter
        );
    }

    /**
     * @param ServiceDto $serviceDto
     *
     * @return string
     */
    private function createMethodByService(ServiceDto $serviceDto)
    {
        $calls = [];

        foreach ($serviceDto->call as $callDto) {
            $calls[] = sprintf(
                '$service->%s(%s);',
                $callDto->method,
                $this->buildMethodArguments($callDto->arguments)
            );
        }


        return sprintf(
            $this->getServiceMethodBody($serviceDto),
            $serviceDto->class,
            $this->createMethodName($serviceDto->id),
            $serviceDto->class,
            $this->buildMethodArguments($serviceDto->arguments),
            implode("\n", $calls)
        );
    }

    /**
     * @param ServiceDto $serviceDto
     *
     * @return string
     */
    private function getServiceMethodBody(ServiceDto $serviceDto)
    {
        $cache = $serviceDto->unique ?
            '' :
            '
            static $service;

            if ($service) {
                return $service;
            }
        ';

        return '
        /**
         * @return %s
         */
        public function %s
        {
            ' . $cache . '
            $service = new %s(%s);
            %s

            return $service;
        }';
    }

    /**
     * @return string
     */
    private function getParameterMethodBody()
    {
        return '
        /**
         * @return %s
         */
        public function %s
        {
            return %s;
        }
        ';
    }

    /**
     * @param array $methodArguments
     * @return string
     */
    private function buildMethodArguments(array $methodArguments)
    {
        $arguments = [];

        foreach ($methodArguments as $argument) {
            if (strpos($argument, '%') === 0) {
                $arguments[] = sprintf('$this->%s', $this->createMethodName($argument));

                continue;
            }

            $arguments[] = $argument;
        }

        return implode(', ', $arguments);
    }

    /**
     * @param string $id
     *
     * @return string
     */
    private function createMethodName($id)
    {
        return 'get_' . preg_replace('/[^A-Za-z0-9_]/', '_', str_replace('%', '', $id)) . '()';
    }
}
