<?php

namespace Belga\Tests\Unit\EventListener;

use Mockery;
use PHPUnit\Framework\TestCase;
use Belga\EventListener\RequestListener;
use Belga\Security\ApiKeyAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @coversDefaultClass Belga\EventListener\RequestListener
 */
class RequestListenerTest extends TestCase
{
    protected $apiKeyAuthenticator;
    protected $event;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->apiKeyAuthenticator = Mockery::mock(ApiKeyAuthenticator::class);
        $this->event               = Mockery::mock(GetResponseEvent::class);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @covers ::onKernelRequest
     * @covers ::__construct
     */
    public function testOnKernelRequestMaster()
    {
        $request = new Request();
        
        $this->event->shouldReceive('isMasterRequest')->andReturn(true);
        
        $this->event->shouldReceive('getRequest')->andReturn($request);
        
        $this->apiKeyAuthenticator->shouldReceive('authenticate')->with($request)->andReturn(null);
        
        $requestListener = new RequestListener($this->apiKeyAuthenticator);
        
        $requestListener->onKernelRequest($this->event);
    }

    /**
     * @covers ::onKernelRequest
     * @covers ::__construct
     */
    public function testOnKernelRequestNotMaster()
    {
        $this->event->shouldReceive('isMasterRequest')->andReturn(false);

        $requestListener = new RequestListener($this->apiKeyAuthenticator);

        $requestListener->onKernelRequest($this->event);
    }
}