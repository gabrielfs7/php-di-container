<?php

namespace GSoares\Test\Sample;

abstract class AbstractSample
{

    /**
     * @var One
     */
    protected $one;

    /**
     * @var Two
     */
    protected $two;

    /**
     * @var Three
     */
    protected $three;

    /**
     * @var string
     */
    private $changeable;

    public function __construct(One $one, Two $two)
    {
        $this->one = $one;
        $this->two = $two;
    }

    /**
     * @return One
     */
    public function getOne()
    {
        return $this->one;
    }

    /**
     * @return Two
     */
    public function getTwo()
    {
        return $this->two;
    }

    /**
     * @return Three
     */
    public function getThree()
    {
        return $this->three;
    }

    /**
     * @param Three $three
     *
     * @return $this
     */
    public function setThree($three)
    {
        $this->three = $three;

        return $this;
    }

    /**
     * @return string
     */
    public function getChangeable()
    {
        return $this->changeable;
    }

    /**
     * @param string $changeable
     *
     * @return $this
     */
    public function setChangeable($changeable)
    {
        $this->changeable = $changeable;

        return $this;
    }
}
