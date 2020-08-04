<?php

namespace Mcustiel\Phiremock\Client\Tests\Acceptance;

use ApiTester;
use function Mcustiel\Phiremock\Client\getRequest;
use Mcustiel\Phiremock\Client\Phiremock;
use function Mcustiel\Phiremock\Client\respond;
use Mcustiel\Phiremock\Domain\Http\MethodsEnum;

class ListExpectationsCest
{
    use PhiremockApiTestHelper;

    public function noExpectationsReturnsEmptyList(ApiTester $I)
    {
        $expectations = $this->_getPhiremockClient()->listExpectations();
        $I->assertEmpty($expectations);
    }

    public function retrievesNotEmptyExpectationsList(ApiTester $I)
    {
        $this->_getPhiremockClient()->createExpectation(
            Phiremock::on(
                getRequest()
            )->then(
                respond(418)
            )
        );
        $expectations = $this->_getPhiremockClient()->listExpectations();
        $I->assertCount(1, $expectations);
        $expectation = $expectations[0];
        $I->assertSame(MethodsEnum::GET, $expectation->getRequest()->getMethod()->getValue()->get());
        /** @var \Mcustiel\Phiremock\Domain\HttpResponse $response */
        $response = $expectation->getResponse();
        $I->assertSame(418, $response->getStatusCode()->asInt());
    }
}
