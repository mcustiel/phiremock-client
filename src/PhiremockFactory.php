<?php

declare(strict_types=1);

namespace Mcustiel\Phiremock\Client;

use Http\Discovery\Psr17FactoryDiscovery;
use Mcustiel\Phiremock\Factory as Base;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class PhiremockFactory extends Base
{
    public function findRequestFactoryInterface(): RequestFactoryInterface
    {
        if (!class_exists('\Http\Discovery\Psr17FactoryDiscovery', true)) {
            throw new \Exception('A psr-17 RequestFactory is needed. '
                 . 'Please extend the factory to return a PSR-17 compatible RequestFactoryInterface or install suggested package php-http/discovery');
        }

        return Psr17FactoryDiscovery::findRequestFactory();
    }

    public function findStreamFactoryInterface(): StreamFactoryInterface
    {
        if (!class_exists('\Http\Discovery\Psr17FactoryDiscovery', true)) {
            throw new \Exception('A psr-17 StreamFactory is needed. '
                 . 'Please extend the factory to return a PSR-17 compatible StreamFactoryInterface or install suggested package php-http/discovery');
        }

        return Psr17FactoryDiscovery::findStreamFactory();
    }
}
