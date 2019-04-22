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

use Mcustiel\Phiremock\Domain\Options\Priority;
use Mcustiel\Phiremock\Domain\Options\ScenarioName;
use Mcustiel\Phiremock\Domain\RequestConditions;

class ConditionsBuilderResult
{
    /** @var RequestConditions */
    private $request;
    /** @var ScenarioName */
    private $scenarioName;
    /** @var Priority */
    private $priority;

    public function __construct(
        RequestConditions $request,
        ScenarioName $scenarioName = null,
        Priority $priority = null
    ) {
        $this->request = $request;
        $this->scenarioName = $scenarioName;
        $this->priority = $priority;
    }

    /** @return RequestConditions */
    public function getRequestConditions()
    {
        return $this->request;
    }

    /** @return Priority|null */
    public function getPriority()
    {
        return $this->priority;
    }

    /** @return ScenarioName|null */
    public function getScenarioName()
    {
        return $this->scenarioName;
    }
}
