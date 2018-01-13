<?php

namespace GSoares\Test\DiContainer\Integration;

use GSoares\DiContainer\Builder\BuilderInterface;
use GSoares\DiContainer\Builder\Builder;
use GSoares\DiContainer\Builder\JsonBuilder;
use GSoares\DiContainer\Cache\Creator;
use GSoares\DiContainer\Cache\MethodCreator;
use GSoares\DiContainer\Container;
use GSoares\DiContainer\Dto\Decoder\JsonDecoder;
use GSoares\DiContainer\File\Validator\JsonValidator;
use PHPUnit\Framework\TestCase;
use stdClass;

class ContainerTest extends TestCase
{

    /**
     * @var BuilderInterface
     */
    private $builder;

    /**
     * @var Container
     */
    private $container;

    public function setUp()
    {
        $cachePath = __DIR__ . '/../../../cache';
        $configPath = __DIR__ . '/../../resources';

        $this->builder = new JsonBuilder($cachePath);
        $this->container = $this->builder
            ->enableCompile()
            ->build(
                [
                    "$configPath/sample-container1.json",
                    "$configPath/sample-container2.json"
                ]
            );
    }

    public function tearDown()
    {
        $this->builder = null;
        $this->container = null;
    }

    /**
     * @test
     */
    public function testGetSimpleService()
    {
        $this->assertInstanceOf('GSoares\Test\DiContainer\Sample\One', $this->container->get('sample.one'));
    }

    /**
     * @test
     */
    public function testGetComplexService()
    {
        $two = $this->container->get('sample.two');

        $this->assertInstanceOf('GSoares\Test\DiContainer\Sample\Two', $two);
        $this->assertInstanceOf('GSoares\Test\DiContainer\Sample\One', $two->getOne());
        $this->assertEquals($this->getDatabaseConfig(), $two->getDatabaseConf());
    }

    /**
     * @test
     */
    public function testCallServiceMethod()
    {
        $three = $this->container->get('sample.three');

        $this->assertInstanceOf('GSoares\Test\DiContainer\Sample\Two', $three->getTwo());
        $this->assertInstanceOf('GSoares\Test\DiContainer\Sample\One', $three->getOne());
    }

    /**
     * @test
     */
    public function testGetParameter()
    {
        $this->assertEquals('prod', $this->container->get('environment'));
    }

    /**
     * @test
     *
     * @expectedException \GSoares\DiContainer\Exception\NotFountException
     * @expectedExceptionMessage Container registry [not.existent] not found
     */
    public function testNotExistentServiceMustThrowException()
    {
        $this->container->get('not.existent');
    }

    /**
     * @test
     */
    public function testHasRegistry()
    {
        $this->assertTrue($this->container->has('database'));
        $this->assertFalse($this->container->has('not.existent'));
    }

    /**
     * @test
     */
    public function testGetComplexParameter()
    {
        $this->assertEquals($this->getDatabaseConfig(), $this->container->get('database'));
    }

    /**
     * @return \stdClass
     */
    private function getDatabaseConfig()
    {
        $databaseConf = new \stdClass();
        $databaseConf->username = "user";
        $databaseConf->password = "secret";
        $databaseConf->host = "localhost";
        $databaseConf->port = "3306";

        return $databaseConf;
    }
}