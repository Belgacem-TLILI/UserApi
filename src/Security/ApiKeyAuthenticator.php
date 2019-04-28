<?php

namespace Belga\Security;

use Belga\Error\ErrorCode;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ApiKeyAuthenticator
{
    /**
     * Array of api keys where key is consumer name and value is the secret
     * [
     *  'consumer1' => 'sdf6s54df654sd65f4sdfs84',
     *  'consumer2' => 'ljsljsd987sdkjhfjksd98f6'
     * ]
     * @var array
     */
    private $apiKeys;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AuthHandler constructor.
     *
     * @param array $apiKeys @see $this->apiKeys doc
     */
    public function __construct(array $apiKeys, LoggerInterface $logger)
    {
        $this->apiKeys = $apiKeys;
        $this->logger = $logger;
    }

    /**
     * Authenticate consumer via X-API-KEY header
     *
     * @param Request $request
     *
     * @return string Name of the consumer (for successful authentication)
     */
    public function authenticate(Request $request)
    {
        $apiKey = $request->headers->get('X-API-KEY');

        foreach ($this->apiKeys as $name => $allowedKey) {
            if ($apiKey === $allowedKey) {
                $this->logger->info(sprintf('Consumer name: %s', $name));
                return $name;
            }
        }
        $this->logger->info(sprintf('Wrong Given X-API-KEY: %s', $apiKey));
        throw new AuthenticationException('Incorrect API key', ErrorCode::INCORRECT_API_KEY);
    }
}