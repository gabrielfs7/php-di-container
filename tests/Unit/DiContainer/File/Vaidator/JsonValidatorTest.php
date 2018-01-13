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
    public function testValidateValidJsonFile()
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
}
