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

use Mcustiel\Phiremock\Domain\Condition;
use Mcustiel\Phiremock\Domain\Conditions\BodyCondition;
use Mcustiel\Phiremock\Domain\Conditions\HeaderCondition;
use Mcustiel\Phiremock\Domain\Conditions\HeaderConditionCollection;
use Mcustiel\Phiremock\Domain\Conditions\Matcher;
use Mcustiel\Phiremock\Domain\Conditions\UrlCondition;
use Mcustiel\Phiremock\Domain\Expectation;
use Mcustiel\Phiremock\Domain\Http\BinaryBody;
use Mcustiel\Phiremock\Domain\Http\HeaderName;
use Mcustiel\Phiremock\Domain\Http\Method;
use Mcustiel\Phiremock\Domain\Http\Url;
use Mcustiel\Phiremock\Domain\Options\Priority;
use Mcustiel\Phiremock\Domain\Options\ScenarioName;
use Mcustiel\Phiremock\Domain\Options\ScenarioState;
use Mcustiel\Phiremock\Domain\Request;

class RequestBuilder
{
    /** @var Request */
    private $request;
    /** @var HeaderConditionCollection */
    private $headers;
    /** @var ScenarioName */
    private $scenarioName;
    /** @var ScenarioState */
    private $scenarioIs;
    /** @var Priority */
    private $priority;

    public function __construct(Method $method, Url $url = null)
    {
        $this->headers = new HeaderConditionCollection();
        $this->request = new Request();
        $this->request->setMethod($method);
        if (null !== $url) {
            $this->request->setUrl(new UrlCondition(Matcher::equalTo(), $url->asString()));
        }
    }

    /**
     * @param string      $method
     * @param null|string $url
     *
     * @return \Mcustiel\Phiremock\Client\Utils\RequestBuilder
     */
    public static function create($method, $url = null)
    {
        return new static(new Method($method), null === $url ? null : new Url($url));
    }

    /**
     * @param \Mcustiel\Phiremock\Domain\Condition $condition
     *
     * @return \Mcustiel\Phiremock\Client\Utils\RequestBuilder
     */
    public function andBody(Condition $condition)
    {
        $this->request->setBody(BodyCondition::fromCondition($condition));

        return $this;
    }

    /**
     * @param \Mcustiel\Phiremock\Domain\Condition $condition
     *
     * @return \Mcustiel\Phiremock\Client\Utils\RequestBuilder
     */
    public function andBinaryBody(Condition $condition)
    {
        $this->request->setBody(
            new BodyCondition($condition->getMatcher(), new BinaryBody($condition->getValue()))
        );

        return $this;
    }

    /**
     * @param string                               $header
     * @param \Mcustiel\Phiremock\Domain\Condition $condition
     *
     * @return \Mcustiel\Phiremock\Client\Utils\RequestBuilder
     */
    public function andHeader($header, Condition $condition)
    {
        $this->request->getHeaders()
            ->setHeaderCondition(
                new HeaderName($header),
                HeaderCondition::fromCondition($condition)
            );

        return $this;
    }

    /**
     * @param \Mcustiel\Phiremock\Domain\Condition $condition
     *
     * @return \Mcustiel\Phiremock\Client\Utils\RequestBuilder
     */
    public function andUrl(Condition $condition)
    {
        $this->request->setUrl(UrlCondition::fromCondition($condition));

        return $this;
    }

    /**
     * @param string $scenario
     * @param string $scenarioState
     *
     * @return \Mcustiel\Phiremock\Client\Utils\RequestBuilder
     */
    public function andScenarioState($scenario, $scenarioState)
    {
        $this->scenarioName = new ScenarioName($scenario);
        $this->scenarioIs = new ScenarioState($scenarioState);

        return $this;
    }

    /**
     * @param int $priority
     */
    public function andPriority($priority)
    {
        $this->priority = new Priority($priority);
    }

    /**
     * @return \Mcustiel\Phiremock\Domain\Expectation
     */
    public function build()
    {
        $expectation = new Expectation();
        $expectation->setRequest($this->request);
        $this->setScenario($expectation);
        $this->setPriority($expectation);

        return $expectation;
    }

    /**
     * @param \Mcustiel\Phiremock\Domain\Expectation $expectation
     */
    private function setPriority(Expectation $expectation)
    {
        if (null !== $this->priority) {
            $expectation->setPriority($this->priority);
        }
    }

    /**
     * @param \Mcustiel\Phiremock\Domain\Expectation $expectation
     */
    private function setScenario(Expectation $expectation)
    {
        if ($this->scenarioName && $this->scenarioIs) {
            $expectation->setScenarioName($this->scenarioName)
                ->setScenarioStateIs($this->scenarioIs);
        }
    }
}
