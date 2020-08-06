<?php

namespace Mcustiel\Phiremock\Client\Tests\Acceptance;

use ApiTester;
use function Mcustiel\Phiremock\Client\getRequest;
use function Mcustiel\Phiremock\Client\postRequest;

class ResetRequestCounterCest
{
    use PhiremockApiTestHelper;

    public function countsRequestsBasedInDefinition(ApiTester $I)
    {
        $I->assertSame(0, $this->_getPhiremockClient()->countExecutions(getRequest()));
        $I->sendGet('/tomato');
        $I->assertSame(1, $this->_getPhiremockClient()->countExecutions(getRequest()));
        $I->assertSame(0, $this->_getPhiremockClient()->countExecutions(postRequest()));
        $this->_getPhiremockClient()->resetRequestsCounter();
        $I->assertSame(0, $this->_getPhiremockClient()->countExecutions(getRequest()));
    }
}
