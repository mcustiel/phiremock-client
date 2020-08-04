<?php

namespace Mcustiel\Phiremock\Client\Tests\Acceptance;

use ApiTester;
use function Mcustiel\Phiremock\Client\getRequest;
use Mcustiel\Phiremock\Client\Phiremock;
use function Mcustiel\Phiremock\Client\respond;

class ResetCest
{
    use PhiremockApiTestHelper;

    public function callingResetRestoresExpectations(ApiTester $I)
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
        $this->_getPhiremockClient()->reset();
        $expectations = $this->_getPhiremockClient()->listExpectations();
        $I->assertEmpty($expectations);
    }

    public function callingResetRestoresRequestsCounter(ApiTester $I)
    {
        $I->assertSame(0, $this->_getPhiremockClient()->countExecutions(getRequest()));
        $I->sendGet('/tomato');
        $I->assertSame(1, $this->_getPhiremockClient()->countExecutions(getRequest()));
        $this->_getPhiremockClient()->reset();
        $I->assertSame(0, $this->_getPhiremockClient()->countExecutions(getRequest()));
    }

    public function callingResetRestoresRequestsList(ApiTester $I)
    {
        $I->assertEmpty($this->_getPhiremockClient()->listExecutions(getRequest()));
        $I->sendGet('/tomato');
        $I->assertCount(1, $this->_getPhiremockClient()->listExecutions(getRequest()));
        $this->_getPhiremockClient()->reset();
        $I->assertEmpty($this->_getPhiremockClient()->listExecutions(getRequest()));
    }
}
