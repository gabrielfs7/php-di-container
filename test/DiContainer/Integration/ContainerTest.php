<?php

namespace GSoares\Test\DiContainer\Integration;

use GSoares\DiContainer\Container;
use GSoares\DiContainer\JsonBuilder;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{

    /**
     * @var JsonBuilder
     */
    private $builder;

    /**
     * @var Container
     */
    private $container;

    public function setUp()
    {
        $this->builder = new JsonBuilder([__DIR__ . '/../../resources/sample-container.json']);
        $this->container = $this->builder
            ->build()
            ->getContainer();
    }

    public function tearDown()
    {
        $this->builder = null;
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
    public function testGetParameter()
    {
        $this->assertEquals('prod', $this->container->get('environment'));
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