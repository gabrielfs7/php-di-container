<?php

namespace GSoares\Test\DiContainer\Sample;

abstract class AbstractSample
{

    /**
     * @var SampleOne
     */
    protected $sampleOne;

    /**
     * @var SampleTwo
     */
    protected $sampleTwo;

    public function __construct(SampleOne $sampleOne, SampleTwo $sampleTwo)
    {
        $this->sampleOne = $sampleOne;
        $this->sampleTwo = $sampleTwo;
    }
}
