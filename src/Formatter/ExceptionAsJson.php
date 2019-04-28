<?php

namespace Belga\Formatter;

use Belga\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ExceptionAsJson formats exceptions as json with predefined structure (swagger).
 *
 */
class ExceptionAsJson
{
    const KEY_CODE = 'code';
    const KEY_FIELD = 'field';
    const KEY_MESSAGE = 'message';
    const KEY_ERRORS = 'errors';

    /**
     * Method format Exception into the response object.
     *
     * @param JsonResponse $response response object, which will be modified (exception will be formatted in it)
     * @param int $httpStatus status number from Symfony\Component\HttpFoundation\JsonResponse
     * @param \Exception $exception exception, which will be parsed in result
     *
     * @return void
     */
    public function format(JsonResponse $response, $httpStatus, \Exception $exception)
    {
        $content[self::KEY_CODE] = $exception->getCode();
        $content[self::KEY_MESSAGE] = $exception->getMessage();
        $content[self::KEY_ERRORS] = $this->getInnerErrorsFromException($exception);

        $jsonContent = json_encode($content);

        $response->setStatusCode($httpStatus);
        $response->setContent($jsonContent);
    }

    /**
     * Create structure for self::KEY_ERRORS part, for ValidationException load all inner errors.
     *
     * @param \Exception $exception
     *
     * @return array result created from ValidationException inner errors
     */
    private function getInnerErrorsFromException(\Exception $exception)
    {
        $result = [];

        if ($exception instanceof ValidationException) {
            $errors = $exception->getInnerErrors();

            foreach ($errors as $error) {
                $item[self::KEY_FIELD] = $error->getField();
                $item[self::KEY_MESSAGE] = $error->getMessage();
                $result[] = $item;
            }
        }

        return $result;
    }
}
