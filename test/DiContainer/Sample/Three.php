<?php

namespace GSoares\Test\DiContainer\Sample;

class Three
{
    /**
     * @var One
     */
    private $one;

    /**
     * @var Two
     */
    private $two;

    /**
     * @param One $one
     */
    public function __construct(One $one)
    {
        $this->one = $one;
    }

    /**
     * @param Two $two
     */
    public function setTwo(Two $two)
    {
        $this->two = $two;
    }

    /**
     * @return Two
     */
    public function getTwo()
    {
        return $this->two;
    }

    /**
     * @return One
     */
    public function getOne()
    {
        return $this->one;
    }
}