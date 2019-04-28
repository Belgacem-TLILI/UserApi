<?php

namespace Belga\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class ObjectSerializer
{
    const API_OUTPUT_FORMAT = 'json';

    const  DATETIME_FORMAT = 'Y-m-d H:i:s';

    private $serializer;

    /**
     * ObjectSerializer constructor.
     * @param JsonEncoder|null $encoder
     * @param GetSetMethodNormalizer|null $normalizer
     * @param GetSetMethodNormalizer|null $normalizer
     */
    public function __construct(
        JsonEncoder $encoder = null,
        GetSetMethodNormalizer $normalizer = null,
        Serializer $serializer = null
    )
    {
        $encoder = $encoder ?: new JsonEncoder();
        $normalizer = $normalizer ?: new GetSetMethodNormalizer();
        $callback = function ($innerObject = null) {
            return $innerObject instanceof \DateTime ? $innerObject->format(self::DATETIME_FORMAT) : '';
        };
        $normalizer->setCallbacks(['createdAt' => $callback]);
        $this->serializer = $serializer ?: new Serializer([$normalizer], [$encoder]);
    }

    public function serialize($data)
    {
        return $this->serializer->serialize($data, self::API_OUTPUT_FORMAT);
    }
}