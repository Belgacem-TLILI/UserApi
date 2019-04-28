<?php

namespace Belga\Exception;

class InvalidHeaderParamException extends ValidationException
{
    const ERROR_MSG = 'Header param validation failed.';
    const EDITOR_FAIL = 'Missing or invalid header "x-editor".';
}
