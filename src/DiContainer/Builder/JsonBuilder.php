<?php

namespace GSoares\DiContainer\Builder;

use GSoares\DiContainer\Cache\Compiler;
use GSoares\DiContainer\Cache\Creator;
use GSoares\DiContainer\Cache\FileCreator;
use GSoares\DiContainer\Cache\MethodCreator;
use GSoares\DiContainer\Dto\Decoder\JsonDecoder;
use GSoares\DiContainer\File\Validator\JsonValidator;

class JsonBuilder implements BuilderInterface
{

    /**
     * @var Builder
     */
    private $builder;

    public function __construct($cachePath)
    {
        $decoder = new JsonDecoder();
        $methodCreator = new MethodCreator($decoder);
        $fileCreator = new FileCreator();
        $validator = new JsonValidator();
        $compiler = new Compiler();
        $creator = new Creator($methodCreator, $fileCreator, $validator, $compiler);

        $this->builder = new Builder($creator, $cachePath);
    }

    /**
     * @inheritdoc
     */
    public function enableCache()
    {
        $this->builder->enableCache();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function enableCompile()
    {
        $this->builder->enableCompile();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function build($containerFiles)
    {
        return $this->builder->build($containerFiles);
    }
}
