<?php

namespace Belga\Validator;

use Belga\Error\ErrorCode;
use Belga\Exception\InvalidUrlParamException;

class ValueValidator
{
    /**
     * Validate a given $id it should be a valid integer
     *
     * @param int $id
     */
    public static function validateId($id)
    {
        $id = trim($id);
        if (!ctype_digit($id)) {
            throw new InvalidUrlParamException(InvalidUrlParamException::INVALID_USER_ID, ErrorCode::URL_PARAM);
        }
    }
}