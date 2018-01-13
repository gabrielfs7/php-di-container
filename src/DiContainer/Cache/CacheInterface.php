<?php

namespace GSoares\DiContainer\Cache;

interface CacheInterface extends \ArrayAccess
{

    /**
     * @return bool
     */
    public function isCompiled();
}
