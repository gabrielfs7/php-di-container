<?php

namespace GSoares\DiContainer;

use GSoares\DiContainer\Exception\InvalidFileException;
use Psr\Container\ContainerInterface;

class JsonBuilder
{

    /**
     * @var array
     */
    private $files;

    /**
     * @var array
     */
    private $services;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(array $files)
    {
        $this->files = $files;
        $this->services = [];
        $this->parameters = [];
    }

    /**
     * @return ContainerInterface
     */
    public function build()
    {
        $this->mapServicesAndParameters();

        return new Container($this->parameters, $this->services);
    }

    private function mapServicesAndParameters()
    {
        foreach ($this->files as $file) {
            $configuration = $this->getConfiguration($file);

            foreach ($configuration->parameters as $values) {
                foreach ($values as $parameter => $value) {
                    $this->parameters[$parameter] = $value;
                }
            }

            foreach ($configuration->services as $service) {
                $this->services[$service->id] = $service;
            }
        }
    }

    /**
     * @param string $file
     * @throws InvalidFileException
     * @return \stdClass
     */
    private function getConfiguration($file)
    {
        $this->validateFile($file);

        $fileContent = file_get_contents($file);

        $object = json_decode($fileContent);

        if (!$object instanceof \stdClass) {
            throw new InvalidFileException(
                "Invalid Json file [$file]. Json last error[" . json_last_error() . "] " . json_last_error_msg()
            );
        }

        $hasServices = property_exists($object, 'services');
        $hasParameters = property_exists($object, 'parameters');

        if (!$hasServices && $hasParameters) {
            throw new InvalidFileException("Json file [$file] must have either 'services' or 'parameters'");
        }

        $newObject = new \stdClass();
        $newObject->services = $hasServices ? $object->services : [];
        $newObject->parameters = $hasParameters ? $object->parameters : [];

        if (!is_array($newObject->services)) {
            throw new InvalidFileException("Json file [$file] 'services' must be a valid array");
        }

        if (!is_array($newObject->parameters)) {
            throw new InvalidFileException("Json file [$file] 'parameters' must be a valid array");
        }

        return $object;
    }

    /**
     * @param string $file
     * @throws InvalidFileException
     */
    private function validateFile($file)
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
    }
}