<?php

namespace Belga\Exception;

class InvalidUrlParamException extends ValidationException
{
    const ERROR_MSG = 'URL param validation failed.';
    const INVALID_USER_ID = 'User Id value is not a valid integer.';
}
