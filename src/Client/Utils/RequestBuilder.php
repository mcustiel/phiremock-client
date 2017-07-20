<?php

namespace Mcustiel\Phiremock\Client\Utils;

use Mcustiel\Phiremock\Domain\Condition;
use Mcustiel\Phiremock\Domain\Expectation;
use Mcustiel\Phiremock\Domain\Request;

class RequestBuilder
{
    /**
     * @var \Mcustiel\Phiremock\Domain\Request
     */
    private $request;
    /**
     * @var array
     */
    private $headers = [];
    /**
     * @var string
     */
    private $scenarioName;
    /**
     * @var string
     */
    private $scenarioIs;
    /**
     * @var int
     */
    private $priority;

    /**
     * @param string $method
     */
    private function __construct($method)
    {
        $this->request = new Request();
        $this->request->setMethod($method);
    }

    /**
     * @param string $method
     *
     * @return \Mcustiel\Phiremock\Client\Utils\RequestBuilder
     */
    public static function create($method)
    {
        return new static($method);
    }

    /**
     * @param \Mcustiel\Phiremock\Domain\Condition $condition
     *
     * @return \Mcustiel\Phiremock\Client\Utils\RequestBuilder
     */
    public function andBody(Condition $condition)
    {
        $this->request->setBody($condition);

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
        $this->headers[$header] = $condition;

        return $this;
    }

    /**
     * @param \Mcustiel\Phiremock\Domain\Condition $condition
     *
     * @return \Mcustiel\Phiremock\Client\Utils\RequestBuilder
     */
    public function andUrl(Condition $condition)
    {
        $this->request->setUrl($condition);

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
        $this->scenarioName = $scenario;
        $this->scenarioIs = $scenarioState;

        return $this;
    }

    /**
     * @param int $priority
     */
    public function andPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return \Mcustiel\Phiremock\Domain\Expectation
     */
    public function build()
    {
        if (!empty($this->headers)) {
            $this->request->setHeaders($this->headers);
        }
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
        if ($this->priority) {
            $expectation->setPriority((int) $this->priority);
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
