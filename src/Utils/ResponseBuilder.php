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

use Mcustiel\Phiremock\Domain\Options\Delay;
use Mcustiel\Phiremock\Domain\Options\ScenarioState;
use Mcustiel\Phiremock\Domain\Response;

abstract class ResponseBuilder
{
    /** @var ScenarioState */
    private $newScenarioState;
    /** @var Delay */
    private $delay;

    public function andSetScenarioStateTo(string $scenarioState): ResponseBuilder
    {
        $this->newScenarioState = new ScenarioState($scenarioState);

        return $this;
    }

    public function andDelayInMillis(int $delay): ResponseBuilder
    {
        $this->delay = new Delay($delay);

        return $this;
    }

    /** @return Response */
    public function build(): Response
    {
        return new Response(
            $this->delay,
            $this->newScenarioState
        );
    }
}
