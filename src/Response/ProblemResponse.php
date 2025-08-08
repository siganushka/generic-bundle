<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @see https://datatracker.ietf.org/doc/html/rfc7807
 */
class ProblemResponse extends JsonResponse
{
    /**
     * Creates an API Problem Response.
     *
     * @param string      $detail  a human-readable explanation specific to this occurrence of the problem
     * @param int         $status  the HTTP status code
     * @param string|null $title   a short, human-readable summary of the problem type
     * @param string|null $type    a URI reference [RFC3986] that identifies the problem type
     * @param array       $headers the response headers
     */
    public function __construct(string $detail, int $status, ?string $title = null, ?string $type = null, array $headers = [])
    {
        self::assertStatus($status);

        parent::__construct(
            self::createAsArray($detail, $status, $title, $type), $status, $headers,
        );
    }

    /**
     * Creates an API Problem Details as array.
     *
     * @param string      $detail a human-readable explanation specific to this occurrence of the problem
     * @param int         $status the HTTP status code
     * @param string|null $title  a short, human-readable summary of the problem type
     * @param string|null $type   a URI reference [RFC3986] that identifies the problem type
     *
     * @return array{ type: string, title: string, status: int, detail: string }
     */
    public static function createAsArray(string $detail, int $status, ?string $title = null, ?string $type = null): array
    {
        self::assertStatus($status);

        $title ??= self::$statusTexts[$status];
        $type ??= 'about:blank';

        return compact('type', 'title', 'status', 'detail');
    }

    /**
     * Checks HTTP status code is invalid.
     */
    public static function assertStatus(int $status): void
    {
        if ($status < 400 || !\array_key_exists($status, self::$statusTexts)) {
            throw new \InvalidArgumentException('The status code must be a 4xx or 5xx.');
        }
    }
}
