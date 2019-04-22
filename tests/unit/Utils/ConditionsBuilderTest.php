<?php

namespace Mcustiel\Phiremock\Client\Tests\Unit\Utils;

use Mcustiel\Phiremock\Client\Utils\ConditionsBuilder;
use Mcustiel\Phiremock\Client\Utils\ConditionsBuilderResult;
use Mcustiel\Phiremock\Domain\Condition;
use Mcustiel\Phiremock\Domain\Conditions\BodyCondition;
use Mcustiel\Phiremock\Domain\Conditions\Matcher;
use Mcustiel\Phiremock\Domain\Conditions\MatchersEnum;
use Mcustiel\Phiremock\Domain\Conditions\UrlCondition;
use Mcustiel\Phiremock\Domain\Http\Method;
use Mcustiel\Phiremock\Domain\Http\MethodsEnum;
use Mcustiel\Phiremock\Domain\Options\Priority;
use Mcustiel\Phiremock\Domain\Options\ScenarioName;
use Mcustiel\Phiremock\Domain\RequestConditions;
use PHPUnit\Framework\TestCase;

class ConditionsBuilderTest extends TestCase
{
    /** @var ConditionsBuilder */
    private $builder;

    public function testCreatesARequestExpectationWithDefaultValues()
    {
        $this->builder = new ConditionsBuilder(Method::delete());
        $result = $this->builder->build();

        $this->assertInstanceOf(ConditionsBuilderResult::class, $result);
        $this->assertInstanceOf(RequestConditions::class, $result->getRequestConditions());
        $request = $result->getRequestConditions();
        $this->assertSame(
            MethodsEnum::DELETE,
            $request->getMethod()->asString()
        );
        $this->assertNull($request->getBody());
        $this->assertNull($request->getUrl());
        $this->assertNull($result->getPriority());
        $this->assertNull($result->getScenarioName());
        $this->assertTrue($request->getHeaders()->isEmpty());
    }

    public function testCreatesARequestExpectationWithSetValues()
    {
        $this->builder = new ConditionsBuilder(Method::delete());
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

        $result = $this->builder->build();

        $this->assertInstanceOf(ConditionsBuilderResult::class, $result);
        $this->assertInstanceOf(RequestConditions::class, $result->getRequestConditions());
        $request = $result->getRequestConditions();
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
        $this->assertInstanceOf(Priority::class, $result->getPriority());
        $this->assertSame(8, $result->getPriority()->asInt());
        $this->assertInstanceOf(ScenarioName::class, $result->getScenarioName());
        $this->assertSame('potatoScenarioName', $result->getScenarioName()->asString());
    }
}
