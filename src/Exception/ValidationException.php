<?php

namespace Belga\Exception;

use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * Validation exception for different kind of validations.
 */
abstract class ValidationException extends ValidatorException
{
    const ERROR_MSG = 'Validation general error.';

    /**
     * Get inner errors, if some are available.
     *
     * @return ExceptionEntry[] array of inner errors.
     */
    public function getInnerErrors()
    {
        return [];
    }
}
