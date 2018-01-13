<?php

namespace GSoares\DiContainer\Cache;

use GSoares\DiContainer\Exception\InvalidFileException;
use GSoares\DiContainer\File\Validator\ValidatorInterface;

class Creator implements CreatorInterface
{

    /**
     * @var MethodCreatorInterface
     */
    private $methodCreator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var bool
     */
    private $enableCompile;

    public function __construct(MethodCreatorInterface $methodCreator, ValidatorInterface $validator)
    {
        $this->methodCreator = $methodCreator;
        $this->validator = $validator;
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
        $this->validateCachePath($cachePath);
        $this->createContainerCacheClass($cachePath, $containerFiles);

        $containerCache = new \ContainerCache();

        if ($this->enableCompile) {
            $this->compileClass($containerCache, $cachePath);
        }

        return $containerCache;
    }

    /**
     * @param string $cachePath
     * @param array $containerFiles
     */
    private function createContainerCacheClass($cachePath, array $containerFiles)
    {
        $containerCacheTemplate = realpath(
            __DIR__ . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            'template' . DIRECTORY_SEPARATOR .
            'ContainerCache.php'
        );
        $containerCacheFile = $this->getContainerCacheFile($cachePath);

        if (file_exists($containerCacheFile)) {
            unlink($containerCacheFile);
        }

        $class = file_get_contents($containerCacheTemplate);
        $class = str_replace('#methods#', $this->getStringMethods($containerFiles), $class);

        file_put_contents($containerCacheFile, $class);

        if (!class_exists('\ContainerCache')) {
            include $containerCacheFile;
        }
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

    /**
     * @param string $cachePath
     * @throws InvalidFileException
     */
    private function validateCachePath($cachePath)
    {
        if (!file_exists($cachePath)) {
            throw new InvalidFileException("Container cache path [$cachePath] does not exists");
        }

        if (!is_dir($cachePath)) {
            throw new InvalidFileException("Container cache path [$cachePath] is not a directory");
        }

        if (!is_writable($cachePath)) {
            throw new InvalidFileException("Container cache path [$cachePath] is not writable");
        }
    }

    /**
     * @param CacheInterface $containerCache
     * @param string $cachePath
     */
    private function compileClass(CacheInterface $containerCache, $cachePath)
    {
        if ($containerCache->isCompiled()) {
            return;
        }

        $reflectionClass = new \ReflectionClass($containerCache);

        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (strpos($method->getName(), 'get_') === 0) {
                $method->invoke($containerCache);
            }
        }

        $class = file_get_contents($this->getContainerCacheFile($cachePath));
        $class = str_replace('#isCompiled#', 'return true;', $class);

        file_put_contents($this->getContainerCacheFile($cachePath), $class);
    }

    /**
     * @param string $cachePath
     * @return string
     */
    private function getContainerCacheFile($cachePath)
    {
        return $cachePath . DIRECTORY_SEPARATOR . 'ContainerCache.php';
    }
}
