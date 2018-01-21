<?php

namespace GSoares\Test\Unit\DiContainer\Dto\Decoder;

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
     * @var MethodCreatorInterface
     */
    private $methodCreator;

    /**
     * @var FileCreatorInterface
     */
    private $fileCreator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var CompilerInterface
     */
    private $compiler;

    public function setUp()
    {
        $this->methodCreator = $this->createMock('\GSoares\DiContainer\Cache\MethodCreatorInterface');
        $this->fileCreator = $this->createMock('\GSoares\DiContainer\Cache\FileCreatorInterface');
        $this->validator = $this->createMock('\GSoares\DiContainer\File\Validator\ValidatorInterface');
        $this->compiler = $this->createMock('\GSoares\DiContainer\Cache\CompilerInterface');

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
        $this->markTestIncomplete();
    }
}
