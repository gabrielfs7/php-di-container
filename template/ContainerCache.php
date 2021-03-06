<?php

class ContainerCache implements \GSoares\DiContainer\Cache\CacheInterface
{
    #methods#

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return method_exists($this, $this->getMethodNameByOffset($offset));
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->{$this->getMethodNameByOffset($offset)}();
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value) {}

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset) {}

    /**
     * @return bool
     */
    public function isCompiled()
    {
        return false;#isCompiled#
    }

    /**
     * @param string $offset
     *
     * @return string
     */
    private function getMethodNameByOffset($offset)
    {
        return 'get_' . preg_replace('/[^A-Za-z0-9_]/', '_', $offset);
    }
}
