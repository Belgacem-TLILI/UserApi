<?php

namespace Belga\Tests\Unit\Exception;

use Belga\Error\ErrorCode;
use Belga\Exception\EntityValidationException;
use Belga\Exception\ExceptionEntry;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Test for class Validation exception.
 */
class ValidationExceptionTest extends TestCase
{

    public function testGetForResponseResultAsJsonEmptySuccess()
    {
        $exception = new EntityValidationException(EntityValidationException::ERROR_MSG, ErrorCode::VALIDATION_OF_DATA_ERROR);

        $errorList = $exception->getErrorList();

        $this->assertInternalType('array', $errorList);
        $this->assertEmpty($errorList);

        $this->assertEquals([], $exception->getInnerErrors());
        $this->assertEquals(ErrorCode::VALIDATION_OF_DATA_ERROR, $exception->getCode());
        $this->assertEquals(EntityValidationException::ERROR_MSG, $exception->getMessage());
    }

    public function testGetForResponseResultSuccess()
    {
        $exception = new EntityValidationException(EntityValidationException::ERROR_MSG, ErrorCode::VALIDATION_OF_DATA_ERROR);

        $violationa = new ConstraintViolation('Stringaaa', 'Templateaaa', [], 'aaaa', 'testValuea', 'invalid_valuea');
        $violationb = new ConstraintViolation('Stringbbb', 'Templatebbb', [], 'bbbb', 'testValueb', 'invalid_valueb');

        $validationsErrors = [$violationa, $violationb];

        $errorList = new ConstraintViolationList($validationsErrors);

        $exception->setErrorList($errorList);

        $exceptionErrorList = $exception->getErrorList();

        $this->assertCount(2, $exceptionErrorList);

        $this->assertEquals(ErrorCode::VALIDATION_OF_DATA_ERROR, $exception->getCode());
        $this->assertEquals(EntityValidationException::ERROR_MSG, $exception->getMessage());

        $this->assertInstanceOf(ConstraintViolationList::class, $exceptionErrorList);

        $violationaResult = $exceptionErrorList[0];
        $this->assertEquals('Stringaaa', $violationaResult->getMessage());
        $this->assertEquals('testValuea', $violationaResult->getPropertyPath());

        $violationbResult = $exceptionErrorList[1];
        $this->assertEquals('Stringbbb', $violationbResult->getMessage());
        $this->assertEquals('testValueb', $violationbResult->getPropertyPath());

        $formatedErrors = $exception->getInnerErrors();

        $this->assertInternalType('array', $formatedErrors);
        $this->assertCount(2, $formatedErrors);

        foreach ($formatedErrors as $error) {
            $this->assertInstanceOf(ExceptionEntry::class, $error);

            if ($error->getField() == 'testValuea') {
                $this->assertEquals("Stringaaa", $error->getMessage());
                $this->assertEquals(EntityValidationException::ERROR_CONSTRAINT_CODE, $error->getCode());
            } else if ($error->getField() == 'testValueb') {
                $this->assertEquals("Stringbbb", $error->getMessage());
                $this->assertEquals(EntityValidationException::ERROR_CONSTRAINT_CODE, $error->getCode());
            } else {
                $this->assertFalse(true);
            }
        }
    }
}