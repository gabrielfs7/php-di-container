<?php

namespace GSoares\Test\Unit\DiContainer\Dto\Decoder;

use GSoares\DiContainer\Dto\CallDto;
use GSoares\DiContainer\Dto\Decoder\JsonDecoder;
use GSoares\DiContainer\Dto\ParameterDto;
use GSoares\DiContainer\Dto\ServiceDto;
use PHPUnit\Framework\TestCase;

class JsonDecoderTest extends TestCase
{

    /**
     * @var JsonDecoder
     */
    private $decoder;

    public function setUp()
    {
        $this->decoder = new JsonDecoder();
    }

    public function tearDown()
    {
        $this->decoder = null;
    }

    /**
     * @param ParameterDto $expected
     * @param \stdClass $map
     *
     * @test
     *
     * @dataProvider decodeParameterProvider
     */
    public function testDecodeParameter(ParameterDto $expected, \stdClass $map)
    {
        $this->assertEquals($expected, $this->decoder->decodeParameter($map));
    }

    /**
     * @return array
     */
    public function decodeParameterProvider()
    {
        return [
            [
                $this->createParameterDto(
                    'id1',
                    'simple value'
                ),
                $this->createParameterMap(
                    'id1',
                    'simple value'
                )
            ],
            [
                $this->createParameterDto(
                    'id2',
                    [
                        '127.0.0.1',
                        '127.0.0.2',
                        '127.0.0.2'
                    ]
                ),
                $this->createParameterMap(
                    'id2',
                    [
                        '127.0.0.1',
                        '127.0.0.2',
                        '127.0.0.2'
                    ]
                )
            ],
            [
                $this->createParameterDto(
                    'id3',
                    $this->createParameterMap(
                        'parameter',
                        'value'
                    )
                ),
                $this->createParameterMap(
                    'id3',
                    $this->createParameterMap(
                        'parameter',
                        'value'
                    )
                )
            ]
        ];
    }

    /**
     * @param ServiceDto $expected
     * @param \stdClass $map
     *
     * @test
     *
     * @dataProvider decodeServiceProvider
     */
    public function testDecodeService(ServiceDto $expected, \stdClass $map)
    {
        $this->assertEquals($expected, $this->decoder->decodeService($map));
    }

    /**
     * @return array
     */
    public function decodeServiceProvider()
    {
        return [
            [
                $this->createServiceDto(
                    'sample.one',
                    "GSoares\\Test\\Sample\\One",
                    [],
                    []
                ),
                $this->createServiceMap(
                    'sample.one',
                    "GSoares\\Test\\Sample\\One",
                    [],
                    []
                )
            ],
            [
                $this->createServiceDto(
                    'sample.two',
                    "GSoares\\Test\\Sample\\Two",
                    [
                        "%sample.one%",
                        "%database%"
                    ],
                    []
                ),
                $this->createServiceMap(
                    'sample.two',
                    "GSoares\\Test\\Sample\\Two",
                    [
                        "%sample.one%",
                        "%database%"
                    ],
                    []
                )
            ]
        ];
    }

    /**
     * @param string $id
     * @param string $class
     * @param array $arguments
     * @param CallDto[] $calls
     *
     * @return ParameterDto
     */
    private function createServiceDto($id, $class, array $arguments, array $calls)
    {
        $dto = new ServiceDto();
        $dto->id = $id;
        $dto->class = $class;
        $dto->arguments = $arguments;
        $dto->call = $calls;

        return $dto;
    }

    /**
     * @param string $id
     * @param string $class
     * @param array $arguments
     * @param array $calls
     *
     * @return \stdClass
     */
    private function createServiceMap($id, $class, array $arguments, array $calls)
    {
        $map = new \stdClass();
        $map->id = $id;
        $map->class = $class;
        $map->arguments = $arguments;
        $map->call = $calls;

        return $map;
    }

    /**
     * @param string $id
     * @param mixed $value
     *
     * @return ParameterDto
     */
    private function createParameterDto($id, $value)
    {
        $dto = new ParameterDto();
        $dto->id = $id;
        $dto->value = $value;

        return $dto;
    }

    /**
     * @param string $id
     * @param mixed $value
     *
     * @return \stdClass
     */
    private function createParameterMap($id, $value)
    {
        $map = new \stdClass();
        $map->$id = $value;

        return $map;
    }
}
