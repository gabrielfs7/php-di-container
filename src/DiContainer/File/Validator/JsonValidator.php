<?php

namespace GSoares\DiContainer\File\Validator;

use GSoares\DiContainer\Exception\InvalidFileException;

class JsonValidator extends AbstractValidator
{

    /**
     * @inheritdoc
     */
    protected function parseFile($file)
    {
        $fileContent = file_get_contents($file);

        $object = json_decode($fileContent);

        if (!$object instanceof \stdClass) {
            throw new InvalidFileException(
                "Invalid Json file [$file]. Json last error[" . json_last_error() . "] " . json_last_error_msg()
            );
        }

        $hasServices = property_exists($object, 'services');
        $hasParameters = property_exists($object, 'parameters');

        if (!$hasServices && !$hasParameters) {
            throw new InvalidFileException("Json file [$file] must have either 'services' or 'parameters'");
        }

        $this->servicesMap = $hasServices ? $object->services : [];
        $this->parametersMap = $hasParameters ? $object->parameters : [];

        if (!is_array($this->servicesMap)) {
            throw new InvalidFileException("Json file [$file] 'services' must be a valid array");
        }

        if (!is_array($this->parametersMap)) {
            throw new InvalidFileException("Json file [$file] 'parameters' must be a valid array");
        }
    }
}
