<?php
/**
 * This file is part of Phiremock.
 *
 * Phiremock is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Phiremock is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Phiremock.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Mcustiel\Phiremock\Client;

use Mcustiel\Phiremock\Client\Connection\Host;
use Mcustiel\Phiremock\Client\Connection\Port;
use Mcustiel\Phiremock\Client\Utils\Http\GuzzlePsr18Client;
use Mcustiel\Phiremock\Client\Utils\Http\Scheme;
use Mcustiel\Phiremock\Factory as PhiremockFactory;
use Psr\Http\Client\ClientInterface;

class Factory
{
    const CLIENT_CONFIG = [
        'http_errors' => false,
    ];

    /** @var PhiremockFactory */
    private $phiremockFactory;

    public function __construct(PhiremockFactory $factory)
    {
        $this->phiremockFactory = $factory;
    }

    public static function createDefault(): self
    {
        return new static(new PhiremockFactory());
    }

    public function createPhiremockClient(Host $host, Port $port, ?Scheme $scheme = null): Phiremock
    {
        return new Phiremock(
            $host,
            $port,
            $this->createRemoteConnection(),
            $this->phiremockFactory->createV2UtilsFactory()->createExpectationToArrayConverter(),
            $this->phiremockFactory->createV2UtilsFactory()->createArrayToExpectationConverter(),
            $this->phiremockFactory->createV2UtilsFactory()->createScenarioStateInfoToArrayConverter(),
            $scheme
        );
    }

    public function createSecurePhiremockClient(Host $host, Port $port): Phiremock
    {
        return new Phiremock(
            $host,
            $port,
            $this->createRemoteConnection(),
            $this->phiremockFactory->createV2UtilsFactory()->createExpectationToArrayConverter(),
            $this->phiremockFactory->createV2UtilsFactory()->createArrayToExpectationConverter(),
            $this->phiremockFactory->createV2UtilsFactory()->createScenarioStateInfoToArrayConverter(),
            Scheme::createHttps()
        );
    }

    public function createRemoteConnection(): ClientInterface
    {
        if (!class_exists('\GuzzleHttp\Client', true)) {
            throw new \Exception('A default http client implementation is needed. ' . 'Please extend the factory to return a PSR18-compatible HttpClient or install Guzzle Http Client v6');
        }
        return new GuzzlePsr18Client();
    }

    protected function getPhiremockFactory(): PhiremockFactory
    {
        return $this->phiremockFactory;
    }
}
