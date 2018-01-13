<?php

namespace GSoares\DiContainer\Builder;

use GSoares\DiContainer\Cache\CreatorInterface;
use GSoares\DiContainer\Container;

class Builder implements BuilderInterface
{

    /**
     * @var CreatorInterface
     */
    private $creator;

    /**
     * @var bool
     */
    private $enableCache;

    /**
     * @var string
     */
    private $cachePath;

    public function __construct(CreatorInterface $creator, $cachePath)
    {
        $this->creator = $creator;
        $this->cachePath = $cachePath;
        $this->enableCache = false;
    }

    /**
     * @inheritdoc
     */
    public function enableCache()
    {
        $this->enableCache = true;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function enableCompile()
    {
        $this->creator->enableCompile();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function build($containerFiles)
    {
        $containerCache = $this->getContainerCacheClass($containerFiles);

        return new Container($containerCache);
    }

    /**
     * @param array $containerFiles
     *
     * @return \GSoares\DiContainer\Cache\CacheInterface
     */
    private function getContainerCacheClass(array $containerFiles)
    {
        $classExists = class_exists('\ContainerCache');

        if ($classExists && $this->enableCache()) {
            return new \ContainerCache();
        }

        return $this->creator->create($this->cachePath, $containerFiles);
    }
}
