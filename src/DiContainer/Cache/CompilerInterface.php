<?php

namespace GSoares\DiContainer\Cache;


interface CompilerInterface
{

    /**
     * @param CacheInterface $containerCache
     * @param string $cacheFile
     */
    public function compile(CacheInterface $containerCache, $cacheFile);
}
