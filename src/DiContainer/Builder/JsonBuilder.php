<?php

namespace GSoares\DiContainer\Builder;

use GSoares\DiContainer\Cache\Creator;
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
        $validator = new JsonValidator();
        $creator = new Creator($methodCreator, $validator);

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