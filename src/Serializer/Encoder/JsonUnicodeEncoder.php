<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Encoder;

use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class JsonUnicodeEncoder extends JsonEncoder
{
    public function __construct(int $jsonEncodeOptions)
    {
        parent::__construct(new JsonEncode([JsonEncode::OPTIONS => $jsonEncodeOptions]));
    }
}
