<?php
/**
 * This file is part of Phiremock.
 *
 * Phiremock is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Phiremock is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Phiremock.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Mcustiel\Phiremock\Client\Tests\Unit\Utils;

use Mcustiel\Phiremock\Client\Utils\ConditionsBuilder;
use Mcustiel\Phiremock\Client\Utils\ConditionsBuilderResult;
use Mcustiel\Phiremock\Domain\Condition\Conditions\BodyCondition;
use Mcustiel\Phiremock\Domain\Condition\Conditions\MethodCondition;
use Mcustiel\Phiremock\Domain\Condition\Conditions\UrlCondition;
use Mcustiel\Phiremock\Domain\Http\MethodsEnum;
use Mcustiel\Phiremock\Domain\Options\ScenarioName;
use Mcustiel\Phiremock\Domain\Conditions as RequestConditions;
use PHPUnit\Framework\TestCase;
use Mcustiel\Phiremock\Domain\Condition\Matchers\MatcherFactory;
use Mcustiel\Phiremock\Client\Utils\Is;
use Mcustiel\Phiremock\Domain\Condition\MatchersEnum;

class ConditionsBuilderTest extends TestCase
{
    /** @var ConditionsBuilder */
    private $builder;

    public function testCreatesARequestExpectationWithDefaultValues()
    {
        $this->builder = new ConditionsBuilder();
        $result = $this->builder->build();

        $this->assertInstanceOf(ConditionsBuilderResult::class, $result);
        $this->assertInstanceOf(RequestConditions::class, $result->getRequestConditions());
        $request = $result->getRequestConditions();
        $this->assertNull($request->getMethod());
        $this->assertNull($request->getBody());
        $this->assertNull($request->getUrl());
        $this->assertNull($result->getScenarioName());
        $this->assertTrue($request->getHeaders()->isEmpty());
    }

    public function testCreatesARequestExpectationWithSetValues()
    {
        $this->builder = new ConditionsBuilder();
        $this->builder->andMethod(Is::equalTo(MethodsEnum::DELETE));
        $this->builder->andUrl(Is::equalTo('/potato'));
        $this->builder->andBody(Is::containing('tomato'));
        $this->builder->andHeader('Content-Type', Is::sameStringAs('text/plain'));
        $this->builder->andScenarioState('potatoScenarioName', 'tomatoScenarioState');

        $result = $this->builder->build();

        $this->assertInstanceOf(ConditionsBuilderResult::class, $result);
        $this->assertInstanceOf(RequestConditions::class, $result->getRequestConditions());
        $request = $result->getRequestConditions();
        $this->assertSame(MethodsEnum::DELETE, $request->getMethod()->getValue()->asString());
        $this->assertInstanceof(BodyCondition::class, $request->getBody());
        $this->assertSame(MatchersEnum::CONTAINS, $request->getBody()->getMatcher()->getName());
        $this->assertSame('tomato', $request->getBody()->getValue()->asString());
        $this->assertInstanceof(UrlCondition::class, $request->getUrl());
        $this->assertSame(MatchersEnum::EQUAL_TO, $request->getUrl()->getMatcher()->getName());
        $this->assertSame('/potato', $request->getUrl()->getValue()->asString());
        $this->assertInstanceOf(ScenarioName::class, $result->getScenarioName());
        $this->assertSame('potatoScenarioName', $result->getScenarioName()->asString());
    }
}
