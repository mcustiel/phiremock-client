<?php

namespace Mcustiel\Phiremock\Client\Connection;

class Host
{
    /** @var string */
    private $host;

    /**
     * @param string $host
     */
    public function __construct($host)
    {
        $this->ensureIsValidHost($host);
        $this->host = $host;
    }

    /** @return \Mcustiel\Phiremock\Client\Connection\Host */
    public static function createLocalhost()
    {
        return new self('localhost');
    }

    /**
     * @return string
     */
    public function asString()
    {
        return $this->host;
    }

    private function ensureIsValidHost($host)
    {
        if (!\is_string($host)) {
            throw new \InvalidArgumentException('Host must be a string value');
        }
        if (false === filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) &&
            false === filter_var($host, FILTER_VALIDATE_IP)) {
            throw new \InvalidArgumentException(sprintf('Invalid host number: %d', $host));
        }
    }
}
