<?php

namespace GSoares\DiContainer\Cache;

use GSoares\DiContainer\File\Validator\ValidatorInterface;

class Creator implements CreatorInterface
{

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

    /**
     * @var bool
     */
    private $enableCompile;

    public function __construct(
        MethodCreatorInterface $methodCreator,
        FileCreatorInterface $fileCreator,
        ValidatorInterface $validator,
        CompilerInterface $compiler
    ) {
        $this->methodCreator = $methodCreator;
        $this->fileCreator = $fileCreator;
        $this->validator = $validator;
        $this->compiler = $compiler;
        $this->enableCompile = false;
    }

    /**
     * @inheritdoc
     */
    public function enableCompile()
    {
        $this->enableCompile = true;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function create($cachePath, array $containerFiles)
    {
        $containerCache = $this->fileCreator
            ->create($cachePath, $this->getStringMethods($containerFiles));

        if ($this->enableCompile && !$containerCache->isCompiled()) {
            $this->compiler
                ->compile($containerCache, $this->fileCreator->getCacheFile());
        }

        return $containerCache;
    }

    /**
     * @param array $containerFiles
     *
     * @return string
     */
    private function getStringMethods(array $containerFiles)
    {
        $methods = '';

        array_walk(
            $containerFiles,
            function ($containerFile) use (&$methods) {
                $this->validator
                    ->validate($containerFile);

                $methods .= implode(
                    PHP_EOL,
                    $this->methodCreator->createByServices($this->validator->getServicesMap())
                );
                $methods .= implode(
                    PHP_EOL,
                    $this->methodCreator->createByParameters($this->validator->getParametersMap())
                );
            }
        );

        return $methods;
    }
}
