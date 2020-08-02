<?php

namespace Mcustiel\Phiremock\Client\Tests\Acceptance;

use ApiTester;
use Mcustiel\Phiremock\Client\Connection\Host;
use Mcustiel\Phiremock\Client\Connection\Port;
use function Mcustiel\Phiremock\Client\contains;
use Mcustiel\Phiremock\Client\Factory as ClientFactory;
use function Mcustiel\Phiremock\Client\getRequest;
use function Mcustiel\Phiremock\Client\isEqualTo;
use Mcustiel\Phiremock\Client\Phiremock;
use function Mcustiel\Phiremock\Client\respond as frespond;
use Mcustiel\Phiremock\Client\Utils\A;
use Mcustiel\Phiremock\Client\Utils\Is;
use Mcustiel\Phiremock\Client\Utils\Respond;
use Mcustiel\Phiremock\Domain\Condition\MatchersEnum;
use Mcustiel\Phiremock\Domain\Http\MethodsEnum;

class ExpectationCreationCest
{
    private $phiremock;

    public function _before(ApiTester $I)
    {
        $factory = ClientFactory::createDefault();
        $this->phiremock = $factory->createPhiremockClient(new Host('localhost'), new Port(8086));
    }

    public function createExpectationUsingHelperClasses(ApiTester $I)
    {
        $this->phiremock->createExpectation(
            Phiremock::on(
                A::getRequest()
                    ->andUrl(Is::equalTo('/potato/tomato'))
                    ->andBody(Is::containing('42'))
                    ->andHeader('Accept', Is::equalTo('application/banana'))
            )->then(
                Respond::withStatusCode(418)
                    ->andBody('Is the answer to the Ultimate Question of Life, The Universe, and Everything')
                    ->andHeader('Content-Type', 'application/banana')
            )
        );
        $expectations = $this->phiremock->listExpectations();
        $I->assertNotEmpty($expectations);
        $expectation = $expectations[0];
        $I->assertSame('2', $expectation->getVersion()->asString());
        $I->assertTrue($expectation->getRequest()->hasMethod());
        $I->assertSame(MatchersEnum::SAME_STRING, $expectation->getRequest()->getMethod()->getMatcher()->getName());
        $I->assertSame(MethodsEnum::GET, $expectation->getRequest()->getMethod()->getMatcher()->getCheckValue()->get());
        $I->assertTrue($expectation->getRequest()->hasUrl());
        $I->assertSame(MatchersEnum::EQUAL_TO, $expectation->getRequest()->getUrl()->getMatcher()->getName());
        $I->assertSame('/potato/tomato', $expectation->getRequest()->getUrl()->getMatcher()->getCheckValue()->get());
        $I->assertTrue($expectation->getRequest()->hasBody());
        $I->assertSame(MatchersEnum::CONTAINS, $expectation->getRequest()->getBody()->getMatcher()->getName());
        $I->assertSame('42', $expectation->getRequest()->getBody()->getMatcher()->getCheckValue()->get());
        $I->assertGreaterThan(0, $expectation->getRequest()->getHeaders()->count());
        $I->assertSame('Accept', $expectation->getRequest()->getHeaders()->key()->asString());
        $I->assertSame(MatchersEnum::EQUAL_TO, $expectation->getRequest()->getHeaders()->current()->getMatcher()->getName());
        $I->assertSame('application/banana', $expectation->getRequest()->getHeaders()->current()->getValue()->get());

        /** @var \Mcustiel\Phiremock\Domain\HttpResponse $response */
        $response = $expectation->getResponse();
        $I->assertSame(418, $response->getStatusCode()->asInt());
        $I->assertSame('Is the answer to the Ultimate Question of Life, The Universe, and Everything', $response->getBody()->asString());
        $I->assertSame('Content-Type', $response->getHeaders()->current()->getName()->asString());
        $I->assertSame('application/banana', $response->getHeaders()->current()->getValue()->asString());
    }

    public function createExpectationUsingHelperFunctions(ApiTester $I)
    {
        $this->phiremock->createExpectation(
            Phiremock::on(
                getRequest()
                    ->andUrl(isEqualTo('/potato/tomato'))
                    ->andBody(contains('42'))
                    ->andHeader('Accept', isEqualTo('application/banana'))
                    )->then(
                        frespond(418)
                            ->andBody('Is the answer to the Ultimate Question of Life, The Universe, and Everything')
                            ->andHeader('Content-Type', 'application/banana')
                    )
            );

        $expectations = $this->phiremock->listExpectations();
        $I->assertNotEmpty($expectations);
        $expectation = $expectations[0];
        $I->assertSame('2', $expectation->getVersion()->asString());
        $I->assertTrue($expectation->getRequest()->hasMethod());
        $I->assertSame(MatchersEnum::SAME_STRING, $expectation->getRequest()->getMethod()->getMatcher()->getName());
        $I->assertSame(MethodsEnum::GET, $expectation->getRequest()->getMethod()->getMatcher()->getCheckValue()->get());
        $I->assertTrue($expectation->getRequest()->hasUrl());
        $I->assertSame(MatchersEnum::EQUAL_TO, $expectation->getRequest()->getUrl()->getMatcher()->getName());
        $I->assertSame('/potato/tomato', $expectation->getRequest()->getUrl()->getMatcher()->getCheckValue()->get());
        $I->assertTrue($expectation->getRequest()->hasBody());
        $I->assertSame(MatchersEnum::CONTAINS, $expectation->getRequest()->getBody()->getMatcher()->getName());
        $I->assertSame('42', $expectation->getRequest()->getBody()->getMatcher()->getCheckValue()->get());
        $I->assertGreaterThan(0, $expectation->getRequest()->getHeaders()->count());
        $I->assertSame('Accept', $expectation->getRequest()->getHeaders()->key()->asString());
        $I->assertSame(MatchersEnum::EQUAL_TO, $expectation->getRequest()->getHeaders()->current()->getMatcher()->getName());
        $I->assertSame('application/banana', $expectation->getRequest()->getHeaders()->current()->getValue()->get());

        /** @var \Mcustiel\Phiremock\Domain\HttpResponse $response */
        $response = $expectation->getResponse();
        $I->assertSame(418, $response->getStatusCode()->asInt());
        $I->assertSame('Is the answer to the Ultimate Question of Life, The Universe, and Everything', $response->getBody()->asString());
        $I->assertSame('Content-Type', $response->getHeaders()->current()->getName()->asString());
        $I->assertSame('application/banana', $response->getHeaders()->current()->getValue()->asString());
    }
}
