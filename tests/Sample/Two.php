<?php

namespace GSoares\Test\Sample;

class Two
{
    /**
     * @var One
     */
    private $one;

    /**
     * @var \stdClass
     */
    private $databaseConf;

    public function __construct(One $one, \stdClass $databaseConf)
    {
        $this->one = $one;
        $this->databaseConf = $databaseConf;
    }

    /**
     * @return One
     */
    public function getOne()
    {
        return $this->one;
    }

    /**
     * @return \stdClass
     */
    public function getDatabaseConf()
    {
        return $this->databaseConf;
    }
}
