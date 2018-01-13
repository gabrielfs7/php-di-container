<?php

namespace GSoares\Test\Unit\DiContainer\File\Validator;

use GSoares\DiContainer\File\Validator\JsonValidator;
use PHPUnit\Framework\TestCase;

class JsonValidatorTest extends TestCase
{

    /**
     * @var JsonValidator
     */
    private $validator;

    /**
     * @var string
     */
    private $containerPath;

    public function setUp()
    {
        $this->validator = new JsonValidator();
        $this->containerPath = __DIR__ . '/../../../../resources/';
    }

    public function tearDown()
    {
        $this->validator = null;
    }

    /**
     * @test
     */
    public function testValidJsonFile()
    {
        $this->assertNull($this->validator->validate($this->containerPath . 'sample-container1.json'));
    }

    /**
     * @test
     * @expectedException \GSoares\DiContainer\Exception\InvalidFileException
     * @expectedExceptionMessage File [fake.json] must be an existent and readable file
     */
    public function testNonExistentFile()
    {
        $this->validator->validate('fake.json');
    }

    /**
     * @test
     * @expectedException \GSoares\DiContainer\Exception\InvalidFileException
     * @expectedExceptionMessage Invalid Json file [invalid.json]. Json last error[4] Syntax error
     */
    public function testInvalidJsonFile()
    {
        $this->validator->validate($this->containerPath . 'invalid.json');
    }

    /**
     * @test
     * @expectedException \GSoares\DiContainer\Exception\InvalidFileException
     * @expectedExceptionMessage Json file [invalid-mandatory.json] must have either "services" or "parameters"
     */
    public function testMissingParametersOrServices()
    {
        $this->validator->validate($this->containerPath . 'invalid-mandatory.json');
    }

    /**
     * @test
     * @expectedException \GSoares\DiContainer\Exception\InvalidFileException
     * @expectedExceptionMessage Json file [parameters-not-array.json] "parameters" must be a valid array
     */
    public function testParametersIsNotArray()
    {
        $this->validator->validate($this->containerPath . 'parameters-not-array.json');
    }

    /**
     * @test
     * @expectedException \GSoares\DiContainer\Exception\InvalidFileException
     * @expectedExceptionMessage Json file [services-not-array.json] "services" must be a valid array
     */
    public function testServicesIsNotArray()
    {
        $this->validator->validate($this->containerPath . 'services-not-array.json');
    }
}
