<?php

namespace Mcustiel\Phiremock\Client\Utils\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzlePsr18Client implements ClientInterface
{
    /** @var GuzzleClient */
    private $client;

    public function __construct(GuzzleClient $client = null)
    {
        $this->client = $client ?? new GuzzleClient(
            [
                'http_errors' => false,
                'allow_redirects' => true,
                'verify'          => false,
            ]
        );
    }

    /** @throws GuzzleException */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->send($request);
    }
}
