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
use function Mcustiel\Phiremock\Client\contains;
use function Mcustiel\Phiremock\Client\getRequest;
use function Mcustiel\Phiremock\Client\isEqualTo;
use function Mcustiel\Phiremock\Client\on;
use Mcustiel\Phiremock\Client\Phiremock;
use function Mcustiel\Phiremock\Client\request;
use function Mcustiel\Phiremock\Client\respond as frespond;
use Mcustiel\Phiremock\Client\Utils\A;
use Mcustiel\Phiremock\Client\Utils\Is;
use Mcustiel\Phiremock\Client\Utils\Respond;
use Mcustiel\Phiremock\Domain\Condition\MatchersEnum;
use Mcustiel\Phiremock\Domain\Expectation;
use Mcustiel\Phiremock\Domain\Http\MethodsEnum;
use Mcustiel\Phiremock\Client\Utils\ConditionsBuilder;
use function Mcustiel\Phiremock\Client\postRequest;
use function Mcustiel\Phiremock\Client\isSameJsonAs;

class ExpectationCreationCest
{
    use PhiremockApiTestHelper;

    public function createsExpectationUsingHelperClasses(ApiTester $I)
    {
        $this->_getPhiremockClient()->createExpectation(
            Phiremock::on(
                A::getRequest()
                    ->andUrl(Is::equalTo('/potato/tomato'))
                    ->andBody(Is::containing('42'))
                    ->andHeader('Accept', Is::equalTo('application/banana'))
                    ->andFormField('name', Is::equalTo('potato'))
            )->then(
                Respond::withStatusCode(418)
                    ->andBody('Is the answer to the Ultimate Question of Life, The Universe, and Everything')
                    ->andHeader('Content-Type', 'application/banana')
            )
        );
        $expectations = $this->_getPhiremockClient()->listExpectations();
        $this->assertExpectationWasCorrectlyCreated($I, $expectations);
    }

    public function createsExpectationUsingHelperFunctions(ApiTester $I)
    {
        $this->_getPhiremockClient()->createExpectation(
            on(
                getRequest()
                    ->andUrl(isEqualTo('/potato/tomato'))
                    ->andBody(contains('42'))
                    ->andHeader('Accept', isEqualTo('application/banana'))
                    ->andFormField('name', isEqualTo('potato'))
            )->then(
                frespond(418)
                    ->andBody('Is the answer to the Ultimate Question of Life, The Universe, and Everything')
                    ->andHeader('Content-Type', 'application/banana')
            )
        );

        $expectations = $this->_getPhiremockClient()->listExpectations();
        $this->assertExpectationWasCorrectlyCreated($I, $expectations);
    }

    public function createsExpectationUsingShortcuts(\ApiTester $I)
    {
        $this->_getPhiremockClient()->createExpectation(
            Phiremock::onRequest(MethodsEnum::GET, '/potato/tomato')
                ->thenRespond(418, 'Is the answer to the Ultimate Question of Life, The Universe, and Everything')
        );
        $expectations = $this->_getPhiremockClient()->listExpectations();
        $I->assertCount(1, $expectations);
        $expectation = $expectations[0];
        $I->assertSame('2', $expectation->getVersion()->asString());
        $I->assertTrue($expectation->getRequest()->hasMethod());
        $I->assertSame(MatchersEnum::SAME_STRING, $expectation->getRequest()->getMethod()->getMatcher()->getName());
        $I->assertSame(MethodsEnum::GET, $expectation->getRequest()->getMethod()->getMatcher()->getCheckValue()->get());
        $I->assertTrue($expectation->getRequest()->hasUrl());
        $I->assertSame(MatchersEnum::EQUAL_TO, $expectation->getRequest()->getUrl()->getMatcher()->getName());
        $I->assertSame('/potato/tomato', $expectation->getRequest()->getUrl()->getMatcher()->getCheckValue()->get());

        /** @var \Mcustiel\Phiremock\Domain\HttpResponse $response */
        $response = $expectation->getResponse();
        $I->assertSame(418, $response->getStatusCode()->asInt());
        $I->assertSame('Is the answer to the Ultimate Question of Life, The Universe, and Everything', $response->getBody()->asString());
    }

    public function keepsFloatType(\ApiTester $I)
    {
        $this->_getPhiremockClient()->createExpectation(
            Phiremock::on(
                postRequest()->andUrl(isEqualTo('/some/path'))
                ->andBody(isSameJsonAs(['whatIs' => 42.0]))
            )->thenRespond(418, 'Is the answer to the Ultimate Question of Life, The Universe, and Everything')
        );

        $I->sendPost('/some/path', ['whatIs' => 42]);
        $I->seeResponseCodeIs(404);
        $I->sendPost('/some/path', json_encode(['whatIs' => 42.0], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRESERVE_ZERO_FRACTION));
        $I->seeResponseCodeIs(418);
    }

    public function createsCatchAllExpectation(\ApiTester $I)
    {
        $this->_getPhiremockClient()->createExpectation(
            Phiremock::on(request())->thenRespond(418, 'Is the answer to the Ultimate Question of Life, The Universe, and Everything')
        );
        $expectations = $this->_getPhiremockClient()->listExpectations();
        $I->assertCount(1, $expectations);
        $expectation = $expectations[0];
        $I->assertSame('2', $expectation->getVersion()->asString());
        $I->assertFalse($expectation->getRequest()->hasMethod());
        $I->assertFalse($expectation->getRequest()->hasUrl());
        $I->assertFalse($expectation->getRequest()->hasBody());
        $I->assertFalse($expectation->getRequest()->hasHeaders());

        /** @var \Mcustiel\Phiremock\Domain\HttpResponse $response */
        $response = $expectation->getResponse();
        $I->assertSame(418, $response->getStatusCode()->asInt());
        $I->assertSame('Is the answer to the Ultimate Question of Life, The Universe, and Everything', $response->getBody()->asString());

        $I->sendPOST('/does/not/matter');
        $I->seeResponseCodeIs(418);
        $I->seeResponseEquals('Is the answer to the Ultimate Question of Life, The Universe, and Everything');

        $I->sendGET('/potato');
        $I->seeResponseCodeIs(418);
        $I->seeResponseEquals('Is the answer to the Ultimate Question of Life, The Universe, and Everything');
    }

    private function assertExpectationWasCorrectlyCreated(ApiTester $I, array $expectations)
    {
        $I->assertCount(1, $expectations);
        /** @var Expectation $expectation */
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

        $I->assertGreaterThan(0, $expectation->getRequest()->getFormFields()->count());
        $I->assertSame('name', $expectation->getRequest()->getFormFields()->key()->asString());
        $I->assertSame(MatchersEnum::EQUAL_TO, $expectation->getRequest()->getFormFields()->current()->getMatcher()->getName());
        $I->assertSame('potato', $expectation->getRequest()->getFormFields()->current()->getValue()->get());

        /** @var \Mcustiel\Phiremock\Domain\HttpResponse $response */
        $response = $expectation->getResponse();
        $I->assertSame(418, $response->getStatusCode()->asInt());
        $I->assertSame('Is the answer to the Ultimate Question of Life, The Universe, and Everything', $response->getBody()->asString());
        $I->assertSame('Content-Type', $response->getHeaders()->current()->getName()->asString());
        $I->assertSame('application/banana', $response->getHeaders()->current()->getValue()->asString());
    }
}
