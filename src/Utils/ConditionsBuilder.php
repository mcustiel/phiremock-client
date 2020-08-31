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

use Mcustiel\Phiremock\Domain\Condition\Conditions\BinaryBodyCondition;
use Mcustiel\Phiremock\Domain\Condition\Conditions\BodyCondition;
use Mcustiel\Phiremock\Domain\Condition\Conditions\FormDataCondition;
use Mcustiel\Phiremock\Domain\Condition\Conditions\FormFieldCondition;
use Mcustiel\Phiremock\Domain\Condition\Conditions\HeaderCondition;
use Mcustiel\Phiremock\Domain\Condition\Conditions\HeaderConditionCollection;
use Mcustiel\Phiremock\Domain\Condition\Conditions\MethodCondition;
use Mcustiel\Phiremock\Domain\Condition\Conditions\UrlCondition;
use Mcustiel\Phiremock\Domain\Condition\Matchers\Equals;
use Mcustiel\Phiremock\Domain\Condition\Matchers\Matcher;
use Mcustiel\Phiremock\Domain\Condition\Matchers\MatcherFactory;
use Mcustiel\Phiremock\Domain\Conditions as RequestConditions;
use Mcustiel\Phiremock\Domain\Http\FormFieldName;
use Mcustiel\Phiremock\Domain\Http\HeaderName;
use Mcustiel\Phiremock\Domain\Options\ScenarioName;
use Mcustiel\Phiremock\Domain\Options\ScenarioState;

class ConditionsBuilder
{
    /** @var MethodCondition */
    private $methodCondition;
    /** @var UrlCondition */
    private $urlCondition;
    /** @var BodyCondition */
    private $bodyCondition;
    /** @var HeaderConditionCollection */
    private $headersConditions;
    /** @var ScenarioName */
    private $scenarioName;
    /** @var ScenarioState */
    private $scenarioIs;
    /** @var FormDataCondition */
    private $formData;

    public function __construct(MethodCondition $methodCondition = null, UrlCondition $urlCondition = null)
    {
        $this->headersConditions = new HeaderConditionCollection();
        $this->formData = new FormDataCondition();
        $this->methodCondition = $methodCondition;
        $this->urlCondition = $urlCondition;
    }

    public static function create(?string $method = null, ?string $url = null): self
    {
        return new static(
            $method === null ? null : new MethodCondition(MatcherFactory::sameString($method)),
            $url === null ? null : new UrlCondition(MatcherFactory::equalsTo($url))
        );
    }

    public function andMethod(Matcher $matcher): self
    {
        if ($matcher instanceof Equals) {
            $matcher = MatcherFactory::sameString($matcher->getCheckValue()->get());
        }
        $this->methodCondition = new MethodCondition($matcher);

        return $this;
    }

    public function andBody(Matcher $matcher): self
    {
        $this->bodyCondition = new BodyCondition($matcher);

        return $this;
    }

    public function andBinaryBody(Matcher $matcher): self
    {
        $this->bodyCondition = new BinaryBodyCondition($matcher);

        return $this;
    }

    public function andHeader(string $header, Matcher $matcher): self
    {
        $this->headersConditions->setHeaderCondition(
            new HeaderName($header),
            new HeaderCondition($matcher)
        );

        return $this;
    }

    public function andFormField(string $fieldName, Matcher $matcher): self
    {
        $this->formData->setFieldCondition(
            new FormFieldName($fieldName),
            new FormFieldCondition($matcher)
        );

        return $this;
    }

    public function andUrl(Matcher $matcher): self
    {
        $this->urlCondition = new UrlCondition($matcher);

        return $this;
    }

    public function andScenarioState(string $scenario, string $scenarioState): self
    {
        $this->scenarioName = new ScenarioName($scenario);
        $this->scenarioIs = new ScenarioState($scenarioState);

        return $this;
    }

    public function build(): ConditionsBuilderResult
    {
        return new ConditionsBuilderResult(
            new RequestConditions(
                $this->methodCondition,
                $this->urlCondition,
                $this->bodyCondition,
                $this->headersConditions->iterator(),
                $this->formData->iterator(),
                $this->scenarioIs
            ),
            $this->scenarioName
        );
    }
}
