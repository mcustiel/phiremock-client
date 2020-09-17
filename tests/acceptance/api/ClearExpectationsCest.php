<?php

namespace Mcustiel\Phiremock\Client\Tests\Acceptance;

use ApiTester;
use function Mcustiel\Phiremock\Client\getRequest;
use Mcustiel\Phiremock\Client\Phiremock;
use function Mcustiel\Phiremock\Client\respond;

class ClearExpectationsCest
{
    use PhiremockApiTestHelper;

    public function clearsExpectations(ApiTester $I)
    {
        $expectations = $this->_getPhiremockClient()->listExpectations();
        $I->assertEmpty($expectations);
        $this->_getPhiremockClient()->createExpectation(
            Phiremock::on(
                getRequest()
            )->then(
                respond(418)
            )
        );

        $expectations = $this->_getPhiremockClient()->listExpectations();
        $I->assertCount(1, $expectations);
        $this->_getPhiremockClient()->clearExpectations();
        $expectations = $this->_getPhiremockClient()->listExpectations();
        $I->assertEmpty($expectations);
    }
}
