<?php

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
