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
use function Mcustiel\Phiremock\Client\isEqualTo;
use Mcustiel\Phiremock\Client\Phiremock;
use function Mcustiel\Phiremock\Client\respond;

class ScenarioStateCest
{
    use PhiremockApiTestHelper;

    public function setsScenarioStateCorrectly(ApiTester $I)
    {
        $this->_getPhiremockClient()->createExpectation(
            Phiremock::on(
                getRequest()
                    ->andUrl(isEqualTo('/banana'))
                    ->andScenarioState('tomatoScenario', 'potato')
            )->then(
                respond(418)
                    ->andBody('Is the answer to the Ultimate Question of Life, The Universe, and Everything')
            )
        );

        $I->sendGET('/banana');
        $I->seeResponseCodeIs(404);
        $this->_getPhiremockClient()->setScenarioState('tomatoScenario', 'potato');
        $I->sendGET('/banana');
        $I->seeResponseCodeIs(418);
        $I->seeResponseEquals('Is the answer to the Ultimate Question of Life, The Universe, and Everything');
    }
}
