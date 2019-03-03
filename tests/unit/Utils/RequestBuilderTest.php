<?php

namespace Mcustiel\Phiremock\Client\Tests\Unit\Utils;

use Mcustiel\Phiremock\Client\Utils\RequestBuilder;
use Mcustiel\Phiremock\Domain\Condition;
use Mcustiel\Phiremock\Domain\Conditions\BodyCondition;
use Mcustiel\Phiremock\Domain\Conditions\Matcher;
use Mcustiel\Phiremock\Domain\Conditions\MatchersEnum;
use Mcustiel\Phiremock\Domain\Conditions\UrlCondition;
use Mcustiel\Phiremock\Domain\Expectation;
use Mcustiel\Phiremock\Domain\Http\Method;
use Mcustiel\Phiremock\Domain\Http\MethodsEnum;
use Mcustiel\Phiremock\Domain\Request;
use PHPUnit\Framework\TestCase;

class RequestBuilderTest extends TestCase
{
    /** @var RequestBuilder */
    private $builder;

    public function testCreatesARequestExpectationWithDefaultValues()
    {
        $this->builder = new RequestBuilder(Method::delete());
        $expectation = $this->builder->build();

        $this->assertInstanceOf(Expectation::class, $expectation);
        $this->assertInstanceOf(Request::class, $expectation->getRequest());
        $request = $expectation->getRequest();
        $this->assertSame(
            MethodsEnum::DELETE,
            $request->getMethod()->asString()
        );
        $this->assertNull($request->getBody());
        $this->assertNull($request->getUrl());
        $this->assertTrue($request->getHeaders()->isEmpty());
    }

    public function testCreatesARequestExpectationWithSetValues()
    {
        $this->builder = new RequestBuilder(Method::delete());
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
        $this->assertInstanceOf(Request::class, $expectation->getRequest());
        $request = $expectation->getRequest();
        $this->assertSame(
            MethodsEnum::DELETE,
            $request->getMethod()->asString()
        );
        $this->assertInstanceof(BodyCondition::class, $request->getBody());
        $this->assertSame(MatchersEnum::CONTAINS, $request->getBody()->getMatcher()->asString());
        $this->assertSame('tomato', $request->getBody()->getValue()->asString());
        $this->assertInstanceof(UrlCondition::class, $request->getUrl());
        $this->assertSame(MatchersEnum::EQUAL_TO, $request->getUrl()->getMatcher()->asString());
        $this->assertSame('/potato', $request->getUrl()->getValue()->asString());
    }
}
