<?php

namespace GSoares\Test\Unit\DiContainer\Dto\Decoder;

use GSoares\DiContainer\Dto\Decoder\JsonDecoder;
use GSoares\DiContainer\Dto\ParameterDto;
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
     * @param \stdClass $parameterMap
     *
     * @test
     *
     * @dataProvider decoderParameterProvider
     */
    public function testDecoderParameter(ParameterDto $expected, \stdClass $parameterMap)
    {
        $this->assertEquals($expected, $this->decoder->decodeParameter($parameterMap));
    }

    /**
     * @return array
     */
    public function decoderParameterProvider()
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
     * @param string $id
     * @param mixed $value
     *
     * @return ParameterDto
     */
    private function createParameterDto($id, $value)
    {
        $parameterDto = new ParameterDto();
        $parameterDto->id = $id;
        $parameterDto->value = $value;

        return $parameterDto;
    }

    /**
     * @param string $id
     * @param mixed $value
     *
     * @return \stdClass
     */
    private function createParameterMap($id, $value)
    {
        $parameterMap = new \stdClass();
        $parameterMap->$id = $value;

        return $parameterMap;
    }
}
