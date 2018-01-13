<?php

namespace GSoares\DiContainer\File\Validator;

use GSoares\DiContainer\Exception\InvalidFileException;

abstract class AbstractValidator implements ValidatorInterface
{

    /**
     * @var array
     */
    protected $parametersMap;

    /**
     * @var array
     */
    protected $servicesMap;

    /**
     * @inheritdoc
     */
    public function validate($file)
    {
        $error = null;

        if (!is_file($file)) {
            $error = "File [$file] is not a regular file";
        }

        if (!is_readable($file)) {
            $error = "File [$file] is not readable";
        }

        if ($error) {
            throw new InvalidFileException($error);
        }

        $this->parseFile($file);
    }

    /**
     * @return array
     */
    public function getServicesMap()
    {
        return $this->servicesMap;
    }

    /**
     * @return array
     */
    public function getParametersMap()
    {
        return $this->parametersMap;
    }

    /**
     * @param string $file
     *
     * @return void
     *
     * @throws \GSoares\DiContainer\Exception\InvalidFileException
     */
    abstract protected function parseFile($file);
}
