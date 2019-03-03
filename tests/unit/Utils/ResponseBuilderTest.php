<?php

namespace Mcustiel\Phiremock\Client\Tests\Unit\Utils;

use Mcustiel\Phiremock\Client\Utils\ResponseBuilder;
use Mcustiel\Phiremock\Client\Utils\ResponseBuilderResult;
use Mcustiel\Phiremock\Domain\Condition;
use Mcustiel\Phiremock\Domain\Conditions\BodyCondition;
use Mcustiel\Phiremock\Domain\Conditions\Matcher;
use Mcustiel\Phiremock\Domain\Conditions\MatchersEnum;
use Mcustiel\Phiremock\Domain\Conditions\UrlCondition;
use Mcustiel\Phiremock\Domain\Expectation;
use Mcustiel\Phiremock\Domain\Http\Method;
use Mcustiel\Phiremock\Domain\Http\MethodsEnum;
use Mcustiel\Phiremock\Domain\Http\StatusCode;
use Mcustiel\Phiremock\Domain\Response;
use PHPUnit\Framework\TestCase;

class ResponseBuilderTest extends TestCase
{
    /** @var ResponseBuilder */
    private $builder;

    public function testCreatesAResponseExpectationWithDefaultValues()
    {
        $this->builder = new ResponseBuilder(new StatusCode(503));
        $result = $this->builder->build();

        $this->assertInstanceOf(ResponseBuilderResult::class, $result);
        $this->assertInstanceOf(Response::class, $result->getResponse());
        $this->assertNull($result->getScenarioState());
        $response = $result->getResponse();
        $this->assertSame(
            503,
            $response->getStatusCode()->asInt()
        );
        $this->assertSame('', $response->getBody()->asString());
        $this->assertSame(0, $response->getDelayMillis()->asInt());
        $this->assertTrue($response->getHeaders()->isEmpty());
    }

    public function testCreatesAResponseExpectationWithSetValues()
    {
        $this->markTestSkipped('Not implemented yet');
        $this->builder = new ResponseBuilder(Method::delete());
        $this->builder->andUrl(
            new Condition(new Matcher(MatchersEnum::EQUAL_TO), '/potato')
        );
        $this->builder->andBody(
            new Condition(new Matcher(MatchersEnum::CONTAINS), 'tomato')
        );
        $this->builder->andHeader(
            'Content-Type',
            new Condition(new Matcher(MatchersEnum::SAME_STRING), 'text/plain')
        );
        $this->builder->andPriority(8);
        $this->builder->andScenarioState('potatoScenarioName', 'tomatoScenarioState');

        $expectation = $this->builder->build();

        $this->assertInstanceOf(Expectation::class, $expectation);
        $this->assertInstanceOf(Response::class, $expectation->getResponse());
        $response = $expectation->getResponse();
        $this->assertSame(
            MethodsEnum::DELETE,
            $response->getMethod()->asString()
        );
        $this->assertInstanceof(BodyCondition::class, $response->getBody());
        $this->assertSame(MatchersEnum::CONTAINS, $response->getBody()->getMatcher()->asString());
        $this->assertSame('tomato', $response->getBody()->getValue()->asString());
        $this->assertInstanceof(UrlCondition::class, $response->getUrl());
        $this->assertSame(MatchersEnum::EQUAL_TO, $response->getUrl()->getMatcher()->asString());
        $this->assertSame('/potato', $response->getUrl()->getValue()->asString());
    }
}
