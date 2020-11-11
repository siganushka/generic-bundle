<?php

namespace Siganushka\GenericBundle\Serializer\Encoder;

use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class UnicodeJsonEncoder extends JsonEncoder
{
    public function __construct(int $jsonEncodeOptions)
    {
        parent::__construct(new JsonEncode([JsonEncode::OPTIONS => $jsonEncodeOptions]));
    }
}
