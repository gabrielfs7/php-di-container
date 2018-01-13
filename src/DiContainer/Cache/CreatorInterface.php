<?php

namespace GSoares\DiContainer\Cache;

interface CreatorInterface
{

    /**
     * @return $this
     */
    public function enableCompile();

    /**
     * @param string $cachePath
     * @param array $containerFiles
     *
     * @return CacheInterface
     */
    public function create($cachePath, array $containerFiles);
}
