<?php

namespace GSoares\DiContainer\File\Validator;

interface ValidatorInterface
{

    /**
     * @param string $file
     *
     * @return void
     *
     * @throws \GSoares\DiContainer\Exception\InvalidFileException
     */
    public function validate($file);

    /**
     * @return array
     */
    public function getServicesMap();

    /**
     * @return array
     */
    public function getParametersMap();
}
