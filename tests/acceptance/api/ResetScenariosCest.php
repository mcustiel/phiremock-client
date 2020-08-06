<?php

namespace Mcustiel\Phiremock\Client\Tests\Acceptance;

use ApiTester;
use function Mcustiel\Phiremock\Client\getRequest;
use function Mcustiel\Phiremock\Client\isEqualTo;
use Mcustiel\Phiremock\Client\Phiremock;
use function Mcustiel\Phiremock\Client\respond;

class ResetScenariosCest
{
    use PhiremockApiTestHelper;

    public function setsScenarioStateCorrectly(ApiTester $I)
    {
        $this->_getPhiremockClient()->createExpectation(
            Phiremock::on(
                getRequest()
                    ->andUrl(isEqualTo('/banana'))
                    ->andScenarioState('tomatoScenario', 'Scenario.START')
            )->then(
                respond(204)->andSetScenarioStateTo('potato')
            )
        );

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
        $I->seeResponseCodeIs(204);
        $I->sendGET('/banana');
        $I->seeResponseCodeIs(418);
        $I->seeResponseEquals('Is the answer to the Ultimate Question of Life, The Universe, and Everything');
        $this->_getPhiremockClient()->resetScenarios();
        $I->sendGET('/banana');
        $I->seeResponseCodeIs(204);
    }
}
