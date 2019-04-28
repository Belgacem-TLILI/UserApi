<?php

namespace Belga\EventListener;

use Belga\Error\ErrorCode;
use Belga\Exception\ApplicationErrorException;
use Belga\Exception\InvalidUrlParamException;
use Belga\Exception\ValidationException;
use Belga\Formatter\ExceptionAsJson;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ExceptionListener
{
    /*
     * @var LoggerInterface
     */
    private $logger;

    /*
     * @var ExceptionAsJson formatter
     */
    private $formatter;

    /**
     * ExceptionListener constructor.
     *
     * @param ExceptionAsJson $formatter formatter used for create json from exception
     * @param LoggerInterface $logger
     */
    public function __construct(ExceptionAsJson $formatter, LoggerInterface $logger)
    {
        $this->formatter = $formatter;
        $this->logger = $logger;
    }

    /**
     * When an exception is thrown (regardless of type) this listener will catch it
     * and here we can handle the response object and update the status, header and content
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $response = new JsonResponse();

        $exception = $event->getException();

        if ($this->isLoggableException($exception)) {
            $this->logger->error($exception);
        }

        $httpStatus = $this->getHttpStatus($exception);

        if ($httpStatus == JsonResponse::HTTP_INTERNAL_SERVER_ERROR) {
            // hide internal exceptions to some general
            $exception = $this->getApplicationErrorException();
        }

        $this->formatter->format($response, $httpStatus, $exception);

        $event->setResponse($response);
    }

    /**
     * Get status for response based on kind of Exception.
     *
     * @param \Exception $exception
     *
     * @return int number from JsonResponse constants
     */
    private function getHttpStatus(\Exception $exception)
    {

        $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        if ($exception instanceof EntityNotFoundException || $exception instanceof NotFoundHttpException) {
            $status = JsonResponse::HTTP_NOT_FOUND;
        } elseif ($exception instanceof AuthenticationException) {
            $status = JsonResponse::HTTP_UNAUTHORIZED;
        } elseif ($exception instanceof ValidationException) {
            $status = JsonResponse::HTTP_BAD_REQUEST;
        }

        return $status;
    }

    /**
     * Get general application error exception.
     *
     * @return ApplicationErrorException
     */
    private function getApplicationErrorException()
    {
        return new ApplicationErrorException(
            ApplicationErrorException::ERROR_MSG,
            ErrorCode::APPLICATION_GENERAL_ERROR
        );
    }

    /**
     * As this Listener catch any exception,
     */
    private function isLoggableException($exception)
    {
        if ($exception instanceof InvalidUrlParamException) {
            return false;
        }

        return true;
    }
}