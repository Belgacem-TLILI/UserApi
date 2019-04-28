<?php

namespace Belga\Exception;

use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Validation exception which is able carry ConstraintViolationList.
 */
class EntityValidationException extends ValidationException
{
    const ERROR_MSG = 'Validation failed.';
    const ERROR_CONSTRAINT_CODE = 'CONSTRAINT_ERROR';

    /**
     * @var ConstraintViolationList
     */
    private $errorList = [];

    /**
     * Set constraint violation list.
     *
     * @param ConstraintViolationList $errors constraint violation list
     *
     * @return void
     */
    public function setErrorList(ConstraintViolationList $errors)
    {
        $this->errorList = $errors;
    }

    /**
     * Get constraint violation list.
     *
     * @return ConstraintViolationList list with constraint violations
     */
    public function getErrorList()
    {
        return $this->errorList;
    }

    /**
     * Get inner errors.
     *
     * @return ExceptionEntry[] array of inner errors.
     */
    public function getInnerErrors()
    {
        $errors = [];

        foreach ($this->getErrorList() as $constraintViolation) {
            $code = $constraintViolation->getCode() ?: self::ERROR_CONSTRAINT_CODE;

            $constraintResult = new ExceptionEntry(
                $constraintViolation->getMessage(),
                $code,
                $constraintViolation->getPropertyPath()
            );

            $errors[] = $constraintResult;
        }

        return $errors;
    }
}
