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

        $fileName = basename($file);

        $object = json_decode($fileContent);

        if (!$object instanceof \stdClass) {
            throw new InvalidFileException(
                sprintf(
                    'Invalid Json file [%s]. Json last error[%s] %s',
                    $fileName, json_last_error(), json_last_error_msg()
                )
            );
        }

        $hasServices = property_exists($object, 'services');
        $hasParameters = property_exists($object, 'parameters');

        if (!$hasServices && !$hasParameters) {
            throw new InvalidFileException(
                sprintf(
                    'Json file [%s] must have either "services" or "parameters"',
                    $fileName
                )
            );
        }

        $this->servicesMap = $hasServices ? $object->services : [];
        $this->parametersMap = $hasParameters ? $object->parameters : [];

        if (!is_array($this->servicesMap)) {
            throw new InvalidFileException(
                sprintf(
                    'Json file [%s] "services" must be a valid array',
                    $fileName
                )
            );
        }

        if (!is_array($this->parametersMap)) {
            throw new InvalidFileException(
                sprintf(
                    'Json file [%s] "parameters" must be a valid array',
                    $fileName
                )
            );
        }
    }
}
