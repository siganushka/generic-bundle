<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResourceNotFoundException extends NotFoundHttpException
{
    /**
     * @param int|string $identifier
     */
    public function __construct($identifier, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct(sprintf('Resource #%s not found.', $identifier), $previous, $code, $headers);
    }
}
