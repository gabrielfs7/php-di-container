<?php

namespace GSoares\DiContainer\Cache;

use GSoares\DiContainer\Exception\InvalidFileException;

class FileCreator implements FileCreatorInterface
{

    /**
     * @var string
     */
    private $cacheFile;

    /**
     * @inheritdoc
     */
    public function create($cachePath, $methodsBody)
    {
        $this->cacheFile = $cachePath . DIRECTORY_SEPARATOR . 'ContainerCache.php';

        $this->validateCachePath($cachePath);

        $containerCacheTemplate = realpath(
            __DIR__ . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            'template' . DIRECTORY_SEPARATOR .
            'ContainerCache.php'
        );

        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }

        $class = file_get_contents($containerCacheTemplate);
        $class = str_replace('#methods#', $methodsBody, $class);

        file_put_contents($this->cacheFile, $class);

        if (!class_exists('\ContainerCache')) {
            include $this->cacheFile;
        }

        return new \ContainerCache();
    }

    /**
     * @inheritdoc
     */
    public function getCacheFile()
    {
        return $this->cacheFile;
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
}
