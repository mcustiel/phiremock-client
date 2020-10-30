<?php

/**
 * This file is part of phiremock-codeception-extension.
 *
 * phiremock-codeception-extension is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * phiremock-codeception-extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with phiremock-codeception-extension.  If not, see <http://www.gnu.org/licenses/>.
 */

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
