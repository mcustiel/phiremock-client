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

use Mcustiel\Phiremock\Domain\Expectation;
use Mcustiel\Phiremock\Domain\Http\Body;
use Mcustiel\Phiremock\Domain\Response;

class ExpectationBuilder
{
    /** @var ConditionsBuilder */
    private $requestBuilder;

    public function __construct(ConditionsBuilder $requestBuilder)
    {
        $this->requestBuilder = $requestBuilder;
    }

    /**
     * @param \Mcustiel\Phiremock\Domain\Response $responseBuilder
     *
     * @return \Mcustiel\Phiremock\Domain\Expectation
     */
    public function then(ResponseBuilder $responseBuilder)
    {
        return $this->createExpectation($responseBuilder->build());
    }

    /**
     * Shortcut.
     *
     * @param int    $statusCode
     * @param string $body
     *
     * return \Mcustiel\Phiremock\Domain\Expectation
     */
    public function thenRespond($statusCode, $body)
    {
        $response = HttpResponseBuilder::create($statusCode)
            ->andBody(new Body($body))
            ->build();

        return $this->createExpectation($response);
    }

    /** @return \Mcustiel\Phiremock\Domain\Expectation */
    private function createExpectation(Response $response)
    {
        $requestOptions = $this->requestBuilder->build();

        return new Expectation(
            $requestOptions->getRequestConditions(),
            $response,
            $requestOptions->getScenarioName()
        );
    }
}
