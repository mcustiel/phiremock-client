<?php

namespace Mcustiel\Phiremock\Client\Tests\Acceptance;

use ApiTester;
use Mcustiel\Phiremock\Client\Connection\Host;
use Mcustiel\Phiremock\Client\Connection\Port;
use Mcustiel\Phiremock\Client\Factory as ClientFactory;
use Mcustiel\Phiremock\Client\Phiremock;

trait PhiremockApiTestHelper
{
    /** @var Phiremock */
    private $phiremock;

    public function _before(ApiTester $I)
    {
        if ($this->phiremock === null) {
            $factory = ClientFactory::createDefault();
            $this->phiremock = $factory->createPhiremockClient(new Host('localhost'), new Port(8086));
        }
        $this->phiremock->reset();
    }

    public function _getPhiremockClient(): Phiremock
    {
        return $this->phiremock;
    }
}
