<?php

namespace Belga\Tests\Unit\EventListener;

use Doctrine\ORM\EntityNotFoundException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Belga\EventListener\ExceptionListener;
use Belga\Exception\ApplicationErrorException;
use Belga\Formatter\ExceptionAsJson;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * @coversDefaultClass Belga\EventListener\ExceptionListener
 */
class ExceptionListenerTest extends TestCase
{
    protected $logger;
    protected $formatter;

    public function setUp()
    {
        parent::setUp();

        $this->logger = Mockery::mock(Logger::class);
        $this->formatter = Mockery::mock(ExceptionAsJson::class);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @covers ::onKernelException
     * @covers ::getHttpStatus
     * @covers ::__construct
     */
    public function testOnKernelException()
    {
        $event = Mockery::mock(GetResponseForExceptionEvent::class);
        $exception = new EntityNotFoundException('Entity Not Found');

        $event->shouldReceive('getException')->andReturn($exception);

        $this->logger->shouldReceive('error')->with($exception)->andReturnSelf();

        $this->formatter->shouldReceive('format')
            ->with(Mockery::type(JsonResponse::class), JsonResponse::HTTP_NOT_FOUND, $exception)
            ->andReturnSelf();

        $event->shouldReceive('setResponse')->with(Mockery::type(JsonResponse::class))->andReturnSelf();

        $listener = new ExceptionListener($this->formatter, $this->logger);

        $listener->onKernelException($event);
    }

    /**
     * @covers ::onKernelException
     * @covers ::getHttpStatus
     * @covers ::getApplicationErrorException
     * @covers ::__construct
     */
    public function testOnKernelExceptionWithInternalServerError()
    {
        $event = Mockery::mock(GetResponseForExceptionEvent::class);
        $exception = new ApplicationErrorException('Error');

        $event->shouldReceive('getException')->andReturn($exception);

        $this->logger->shouldReceive('error')->with($exception)->andReturnSelf();

        $this->formatter->shouldReceive('format')
            ->with(Mockery::type(JsonResponse::class), JsonResponse::HTTP_INTERNAL_SERVER_ERROR, Mockery::type(ApplicationErrorException::class))
            ->andReturnSelf();

        $event->shouldReceive('setResponse')->with(Mockery::type(JsonResponse::class))->andReturnSelf();

        $listener = new ExceptionListener($this->formatter, $this->logger);

        $listener->onKernelException($event);
    }
}