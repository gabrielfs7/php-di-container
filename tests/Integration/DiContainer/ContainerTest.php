<?php

namespace GSoares\Test\Integration\DiContainer;

use GSoares\DiContainer\Builder\BuilderInterface;
use GSoares\DiContainer\Builder\JsonBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

class ContainerTest extends TestCase
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var BuilderInterface
     */
    private $builder;

    /**
     * @var string
     */
    private $cachePath;

    /**
     * @var string
     */
    private $configPath;

    /**
     * @var array
     */
    private $defaultPaths;

    public function setUp()
    {
        $this->cachePath = __DIR__ .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'cache';

        $this->configPath = __DIR__ .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'resources';

        $this->defaultPaths = [
            $this->configPath . DIRECTORY_SEPARATOR . 'sample-container1.json',
            $this->configPath . DIRECTORY_SEPARATOR . 'sample-container2.json'
        ];
    }

    public function tearDown()
    {
        $this->defaultPaths = null;
        $this->cachePath = null;
        $this->configPath = null;
        $this->builder = null;
        $this->container = null;
    }

    /**
     * @test
     *
     * @expectedException \GSoares\DiContainer\Exception\NotFountException
     * @expectedExceptionMessage Abstract service [invalid] not found
     */
    public function testGetInheritorWithInvalidAbstractionMustThrowException()
    {
        $this->createContainer(
            [
                $this->configPath . DIRECTORY_SEPARATOR . 'invalid-abstraction.json'
            ]
        );
    }

    /**
     * @test
     */
    public function testGetSimpleService()
    {
        $this->assertInstanceOf(
            'GSoares\Test\Sample\One',
            $this->createContainer($this->defaultPaths)->get('sample.one')
        );
    }

    /**
     * @test
     */
    public function testGetComplexService()
    {
        $two = $this->createContainer($this->defaultPaths)->get('sample.two');

        $this->assertInstanceOf('GSoares\Test\Sample\Two', $two);
        $this->assertInstanceOf('GSoares\Test\Sample\One', $two->getOne());
        $this->assertEquals($this->getDatabaseConfig(), $two->getDatabaseConf());
    }

    /**
     * @test
     */
    public function testCallServiceMethod()
    {
        $this->createContainer($this->defaultPaths);

        $three = $this->container->get('sample.three');

        $this->assertInstanceOf('GSoares\Test\Sample\Two', $three->getTwo());
        $this->assertInstanceOf('GSoares\Test\Sample\One', $three->getOne());
    }

    /**
     * @test
     */
    public function testGetInheritorService()
    {
        $inheritanceOne = $this->createContainer($this->defaultPaths)->get('sample.inheritance.one');

        $this->assertInstanceOf('GSoares\Test\Sample\InheritanceOne', $inheritanceOne);
        $this->assertInstanceOf('GSoares\Test\Sample\One', $inheritanceOne->getOne());
        $this->assertInstanceOf('GSoares\Test\Sample\Two', $inheritanceOne->getTwo());
        $this->assertInstanceOf('GSoares\Test\Sample\Three', $inheritanceOne->getThree());
    }

    /**
     * @test
     */
    public function testUniqueService()
    {
        $container = $this->createContainer($this->defaultPaths);

        $inheritanceOne = $container->get('sample.inheritance.one');
        $inheritanceOne->setChangeable('test');

        $inheritanceTwo = $container->get('sample.inheritance.two');
        $inheritanceTwo->setChangeable('test');

        $this->assertEquals('test', $container->get('sample.inheritance.one')->getChangeable());
        $this->assertEquals(null, $container->get('sample.inheritance.two')->getChangeable());
    }

    /**
     * @test
     */
    public function testGetParameter()
    {
        $this->assertEquals('prod', $this->createContainer($this->defaultPaths)->get('environment'));
    }

    /**
     * @test
     *
     * @expectedException \GSoares\DiContainer\Exception\NotFountException
     * @expectedExceptionMessage Container registry [not.existent] not found
     */
    public function testNotExistentServiceMustThrowException()
    {
        $this->createContainer($this->defaultPaths)->get('not.existent');
    }

    /**
     * @test
     */
    public function testHasRegistry()
    {
        $container = $this->createContainer($this->defaultPaths);

        $this->assertTrue($container->has('database'));
        $this->assertFalse($container->has('not.existent'));
    }

    /**
     * @test
     */
    public function testGetComplexParameter()
    {
        $this->assertEquals($this->getDatabaseConfig(), $this->createContainer($this->defaultPaths)->get('database'));
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

    /**
     * @param array $paths
     *
     * @return \Psr\Container\ContainerInterface
     */
    private function createContainer(array $paths)
    {
        $containerCacheFile = $this->cachePath . DIRECTORY_SEPARATOR . 'ContainerCache.php';

        if (file_exists($containerCacheFile)) {
            unlink($containerCacheFile);
        }

        $this->builder = new JsonBuilder($this->cachePath);

        return $this->container = $this->builder
            ->enableCache()
            ->enableCompile()
            ->build($paths);
    }
}
