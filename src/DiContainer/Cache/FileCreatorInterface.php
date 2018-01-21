<?php

namespace GSoares\DiContainer\Cache;

interface FileCreatorInterface
{

    /**
     * @param string $cachePath
     * @param string $methodsBody
     *
     * @return CacheInterface
     *
     * @throws \GSoares\DiContainer\Exception\InvalidFileException
     */
    public function create($cachePath, $methodsBody);

    /**
     * @return string
     */
    public function getCacheFile();
}
