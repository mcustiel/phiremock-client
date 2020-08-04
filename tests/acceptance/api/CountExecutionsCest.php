<?php

namespace Mcustiel\Phiremock\Client\Tests\Acceptance;

use ApiTester;
use function Mcustiel\Phiremock\Client\getRequest;
use function Mcustiel\Phiremock\Client\postRequest;

class CountExecutionsCest
{
    use PhiremockApiTestHelper;

    public function countsRequestsBasedInDefinition(ApiTester $I)
    {
        $I->assertSame(0, $this->_getPhiremockClient()->countExecutions(getRequest()));
        $I->sendGet('/tomato');
        $I->assertSame(1, $this->_getPhiremockClient()->countExecutions(getRequest()));
        $I->assertSame(0, $this->_getPhiremockClient()->countExecutions(postRequest()));
    }

    public function countsAllRequests(ApiTester $I)
    {
        $I->assertSame(0, $this->_getPhiremockClient()->countExecutions());
        $I->sendGet('/tomato');
        $I->sendPost('/potato', ['banana' => 'coconut']);
        $I->assertSame(1, $this->_getPhiremockClient()->countExecutions(getRequest()));
        $I->assertSame(1, $this->_getPhiremockClient()->countExecutions(postRequest()));
        $I->assertSame(2, $this->_getPhiremockClient()->countExecutions());
    }
}
