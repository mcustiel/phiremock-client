<?php

namespace Mcustiel\Phiremock\Client\Utils;

use Mcustiel\Phiremock\Domain\Options\ScenarioState;
use Mcustiel\Phiremock\Domain\Response;

class ResponseBuilderResult
{
    /** @var Response */
    private $response;

    /** @var ScenarioState */
    private $scenarioState;

    public function __construct(Response $response, ScenarioState $scenarioState = null)
    {
        $this->response = $response;
        $this->scenarioState = $scenarioState;
    }

    /**
     * @return \Mcustiel\Phiremock\Domain\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \Mcustiel\Phiremock\Domain\Options\ScenarioState
     */
    public function getScenarioState()
    {
        return $this->scenarioState;
    }
}
