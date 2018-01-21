<?php

namespace GSoares\DiContainer\Cache;

class Compiler implements CompilerInterface
{

    /**
     * @param CacheInterface $containerCache
     * @param string $cacheFile
     */
    public function compile(CacheInterface $containerCache, $cacheFile)
    {
        $reflectionClass = new \ReflectionClass($containerCache);

        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (strpos($method->getName(), 'get_') === 0) {
                $method->invoke($containerCache);
            }
        }

        $class = file_get_contents($cacheFile);
        $class = str_replace('#isCompiled#', 'return true;', $class);

        file_put_contents($cacheFile, $class);
    }
}
