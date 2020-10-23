<?php

namespace Mcustiel\Phiremock\Client\Utils\Http;

class Scheme
{
    public const HTTP = 'http';
    public const HTTPS = 'https';

    /** @var string */
    private $scheme;

    public function __construct(string $scheme)
    {
        $this->ensureIsValidScheme($scheme);
        $this->scheme = $scheme;
    }

    public static function createHttp(): self
    {
        return new self(self::HTTP);
    }

    public static function createHttps(): self
    {
        return new self(self::HTTPS);
    }

    public function asString(): string
    {
        return $this->scheme;
    }

    private function ensureIsValidScheme($scheme)
    {
        if (preg_match('/^https?$/', $scheme) !== 1) {
            throw new \InvalidArgumentException(sprintf('Invalid scheme %s', $scheme));
        }
    }
}
