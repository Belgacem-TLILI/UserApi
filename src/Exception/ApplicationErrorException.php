<?php

namespace Belga\Exception;

/**
 * General application error exception.
 */
class ApplicationErrorException extends \Exception
{
    const ERROR_MSG = 'Application error, try again later or contact us.';
}
