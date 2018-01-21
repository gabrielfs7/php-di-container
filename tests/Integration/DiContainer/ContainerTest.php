<?php

namespace GSoares\Test\Integration\DiContainer;

use GSoares\DiContainer\Builder\BuilderInterface;
use GSoares\DiContainer\Builder\JsonBuilder;
use GSoares\DiContainer\Container;
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
        $cachePath = __DIR__ .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'cache';

        $containerCacheFile = $cachePath . DIRECTORY_SEPARATOR . 'ContainerCache.php';

        if (file_exists($containerCacheFile)) {
            unlink($containerCacheFile);
        }

        $configPath = __DIR__ .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'resources';

        $this->builder = new JsonBuilder($cachePath);
        $this->container = $this->builder
            ->enableCache()
            ->enableCompile()
            ->build(
                [
                    $configPath . DIRECTORY_SEPARATOR . 'sample-container1.json',
                    $configPath . DIRECTORY_SEPARATOR . 'sample-container2.json'
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
        $this->assertInstanceOf('GSoares\Test\Sample\One', $this->container->get('sample.one'));
    }

    /**
     * @test
     */
    public function testGetComplexService()
    {
        $two = $this->container->get('sample.two');

        $this->assertInstanceOf('GSoares\Test\Sample\Two', $two);
        $this->assertInstanceOf('GSoares\Test\Sample\One', $two->getOne());
        $this->assertEquals($this->getDatabaseConfig(), $two->getDatabaseConf());
    }

    /**
     * @test
     */
    public function testCallServiceMethod()
    {
        $three = $this->container->get('sample.three');

        $this->assertInstanceOf('GSoares\Test\Sample\Two', $three->getTwo());
        $this->assertInstanceOf('GSoares\Test\Sample\One', $three->getOne());
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
