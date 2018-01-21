<?php

namespace GSoares\Test\Unit\DiContainer\Dto\Decoder;

use GSoares\DiContainer\Cache\FileCreator;
use PHPUnit\Framework\TestCase;
use GSoares\DiContainer\Cache\CacheInterface;

class FileCreatorTest extends TestCase
{

    /**
     * @var FileCreator
     */
    private $fileCreator;


    public function setUp()
    {
        $this->fileCreator = new FileCreator();
    }

    public function tearDown()
    {
        $this->fileCreator = null;
    }

    /**
     * @test
     */
    public function testIfDoesNoCreateCacheFileMustBeNull()
    {
        $this->assertNull($this->fileCreator->getCacheFile());
    }

    /**
     * @test
     */
    public function testCreate()
    {
        $containerCache = $this->fileCreator->create($this->getTestCachePath(), '#does not matter');

        $this->assertInstanceOf(CacheInterface::class, $containerCache);
        $this->assertFalse($containerCache->isCompiled());
    }

    /**
     * @test
     *
     * @expectedException \GSoares\DiContainer\Exception\InvalidFileException
     * @expectedExceptionMessage Container cache path [invalid] does not exists
     */
    public function testCreateWithNonExistentCachePathMustThrowException()
    {
        $this->fileCreator->create('invalid', '');
    }

    /**
     * @test
     *
     * @expectedException \GSoares\DiContainer\Exception\InvalidFileException
     * @expectedExceptionMessage Container cache path [test.txt] is not a directory
     */
    public function testCreateWithInvalidCachePathMustThrowException()
    {
        $this->fileCreator->create($this->getTestCachePath() . DIRECTORY_SEPARATOR . 'test.txt', '');
    }

    /**
     * @test
     *
     * @expectedException \GSoares\DiContainer\Exception\InvalidFileException
     * @expectedExceptionMessage Container cache path [is-not-writable] is not writable
     */
    public function testCreateWithNonWritableCachePathMustThrowException()
    {
        $cachePath = $this->getTestCachePath() . DIRECTORY_SEPARATOR . 'is-not-writable';

        if (!file_exists($cachePath)) {
            mkdir($cachePath, '0600');
        }

        $this->fileCreator->create($cachePath, '');
    }

    /**
     * @return string
     */
    private function getTestCachePath()
    {
        return __DIR__ .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'cache/test';
    }
}
