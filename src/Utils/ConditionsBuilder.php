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

namespace Mcustiel\Phiremock\Client\Utils;

use Mcustiel\Phiremock\Domain\Conditions\BinaryBody\BinaryBodyCondition;
use Mcustiel\Phiremock\Domain\Conditions\BinaryBody\BinaryBodyMatcher;
use Mcustiel\Phiremock\Domain\Conditions\Body\BodyCondition;
use Mcustiel\Phiremock\Domain\Conditions\Body\BodyMatcher;
use Mcustiel\Phiremock\Domain\Conditions\Header\HeaderCondition;
use Mcustiel\Phiremock\Domain\Conditions\Header\HeaderConditionCollection;
use Mcustiel\Phiremock\Domain\Conditions\Header\HeaderMatcher;
use Mcustiel\Phiremock\Domain\Conditions\Method\MethodCondition;
use Mcustiel\Phiremock\Domain\Conditions\Method\MethodMatcher;
use Mcustiel\Phiremock\Domain\Conditions\StringValue;
use Mcustiel\Phiremock\Domain\Conditions\Url\UrlCondition;
use Mcustiel\Phiremock\Domain\Conditions\Url\UrlMatcher;
use Mcustiel\Phiremock\Domain\Http\HeaderName;
use Mcustiel\Phiremock\Domain\Http\Method;
use Mcustiel\Phiremock\Domain\Http\Url;
use Mcustiel\Phiremock\Domain\Options\ScenarioName;
use Mcustiel\Phiremock\Domain\Options\ScenarioState;
use Mcustiel\Phiremock\Domain\RequestConditions;

class ConditionsBuilder
{
    /** @var MethodCondition */
    private $methodCondition;
    /** @var UrlCondition */
    private $urlCondition;
    /** @var BodyCondition */
    private $bodyCondition;
    /** @var HeaderConditionCollection */
    private $headers;
    /** @var ScenarioName */
    private $scenarioName;
    /** @var ScenarioState */
    private $scenarioIs;

    public function __construct(MethodCondition $methodCondition, UrlCondition $urlCondition = null)
    {
        $this->headers = new HeaderConditionCollection();
        $this->methodCondition = $methodCondition;
        $this->urlCondition = $urlCondition;
    }

    /**
     * @param string      $method
     * @param string|null $url
     *
     * @return self
     */
    public static function create($method, $url = null)
    {
        return new static(
            new MethodCondition(MethodMatcher::equalTo(), new StringValue($method)),
            null === $url
                ? null :
                new UrlCondition(UrlMatcher::equalTo(), new StringValue($url))
        );
    }

    /**
     * @param Condition $condition
     *
     * @return self
     */
    public function andBody(Condition $condition)
    {
        $this->bodyCondition = new BodyCondition(
            new BodyMatcher($condition->getMatcherName()),
            new StringValue($condition->getValue())
        );

        return $this;
    }

    /**
     * @param Condition $condition
     *
     * @return self
     */
    public function andBinaryBody(Condition $condition)
    {
        $this->bodyCondition = new BinaryBodyCondition(
            new BinaryBodyMatcher($condition->getMatcherName()),
            new StringValue($condition->getValue())
        );

        return $this;
    }

    /**
     * @param string    $header
     * @param Condition $condition
     *
     * @return self
     */
    public function andHeader($header, Condition $condition)
    {
        $this->headers->setHeaderCondition(
            new HeaderName($header),
            new HeaderCondition(
                new HeaderMatcher($condition->getMatcherName()),
                new StringValue($condition->getValue())
            )
        );

        return $this;
    }

    /**
     * @param Condition $condition
     *
     * @return self
     */
    public function andUrl(Condition $condition)
    {
        $this->urlCondition = new UrlCondition(
            new UrlMatcher($condition->getMatcherName()),
            new StringValue($condition->getValue())
        );

        return $this;
    }

    /**
     * @param string $scenario
     * @param string $scenarioState
     *
     * @return self
     */
    public function andScenarioState($scenario, $scenarioState)
    {
        $this->scenarioName = new ScenarioName($scenario);
        $this->scenarioIs = new ScenarioState($scenarioState);

        return $this;
    }

    /** @return ConditionsBuilderResult */
    public function build()
    {
        return new ConditionsBuilderResult(
            new RequestConditions(
                $this->methodCondition,
                $this->urlCondition,
                $this->bodyCondition,
                $this->headers
            ),
            $this->scenarioName
        );
    }
}
