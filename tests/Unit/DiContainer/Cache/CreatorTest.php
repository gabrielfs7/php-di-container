<?php

namespace GSoares\Test\Unit\DiContainer\Dto\Decoder;

use GSoares\DiContainer\Cache\CacheInterface;
use GSoares\DiContainer\Cache\CompilerInterface;
use GSoares\DiContainer\Cache\Creator;
use GSoares\DiContainer\Cache\FileCreatorInterface;
use GSoares\DiContainer\Cache\MethodCreatorInterface;
use GSoares\DiContainer\File\Validator\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class CreatorTest extends TestCase
{

    /**
     * @var Creator
     */
    private $creator;

    /**
     * @var MethodCreatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $methodCreator;

    /**
     * @var FileCreatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileCreator;

    /**
     * @var ValidatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $validator;

    /**
     * @var CompilerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $compiler;

    /**
     * @var CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    public function setUp()
    {
        $this->methodCreator = $this->createMock('\GSoares\DiContainer\Cache\MethodCreatorInterface');
        $this->fileCreator = $this->createMock('\GSoares\DiContainer\Cache\FileCreatorInterface');
        $this->validator = $this->createMock('\GSoares\DiContainer\File\Validator\ValidatorInterface');
        $this->compiler = $this->createMock('\GSoares\DiContainer\Cache\CompilerInterface');
        $this->cache = $this->createMock('\GSoares\DiContainer\Cache\CacheInterface');

        $this->creator = new Creator(
            $this->methodCreator,
            $this->fileCreator,
            $this->validator,
            $this->compiler
        );
    }

    public function tearDown()
    {
        $this->creator = null;
        $this->methodCreator = null;
        $this->fileCreator = null;
        $this->validator = null;
        $this->compiler = null;
    }

    /**
     * @test
     */
    public function testCreate()
    {
        $this->fileCreator
            ->expects($this->once())
            ->method('create')
            ->with('path', 'services_parameters')
            ->willReturn($this->cache);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with('file')
            ->willReturn(null);

        $this->validator
            ->expects($this->once())
            ->method('getServicesMap')
            ->willReturn([]);

        $this->validator
            ->expects($this->once())
            ->method('getParametersMap')
            ->willReturn([]);

        $this->methodCreator
            ->expects($this->once())
            ->method('createByServices')
            ->with([])
            ->willReturn(['services_']);

        $this->methodCreator
            ->expects($this->once())
            ->method('createByParameters')
            ->with([])
            ->willReturn(['parameters']);

        $this->fileCreator
            ->expects($this->once())
            ->method('getCacheFile')
            ->willReturn('cache_file');

        $this->compiler
            ->expects($this->once())
            ->method('compile')
            ->with($this->cache, 'cache_file')
            ->willReturn(null);

        $this->cache
            ->expects($this->once())
            ->method('isCompiled')
            ->willReturn(false);

        $this->assertEquals($this->cache, $this->creator->enableCompile()->create('path', ['file']));
    }
}
