<?php

namespace Mcustiel\Phiremock\Client\Tests\Acceptance;

use ApiTester;
use Mcustiel\Phiremock\Client\Phiremock;

trait PhiremockApiTestHelper
{
    /** @var Phiremock */
    private $phiremock;

    public function _before(ApiTester $I)
    {
        if ($this->phiremock === null) {
            $this->phiremock = $I->getPhiremockClient();
        }
        $this->phiremock->reset();
    }

    public function _getPhiremockClient(): Phiremock
    {
        return $this->phiremock;
    }
}
