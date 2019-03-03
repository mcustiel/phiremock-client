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

use Mcustiel\Phiremock\Domain\Http\Uri;

class ExpectationBuilder
{
    /**
     * @var \Mcustiel\Phiremock\Domain\Expectation
     */
    private $expectation;

    /**
     * @param RequestBuilder $requestBuilder
     */
    public function __construct(RequestBuilder $requestBuilder)
    {
        $this->expectation = $requestBuilder->build();
    }

    /**
     * @param \Mcustiel\Phiremock\Domain\Response $responseBuilder
     *
     * @return \Mcustiel\Phiremock\Domain\Expectation
     */
    public function then(ResponseBuilder $responseBuilder)
    {
        $responseBuilderResult = $responseBuilder->build();

        return $this->expectation
            ->setNewScenarioState($responseBuilderResult->getScenarioState())
            ->setResponse($responseBuilderResult->getResponse());
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
        $response = ResponseBuilder::create($statusCode)
            ->andBody($body)
            ->build()
            ->getResponse();

        return $this->expectation->setResponse($response);
    }

    /**
     * @param string $url
     *
     * @throws \Exception
     *
     * @return \Mcustiel\Phiremock\Domain\Expectation
     */
    public function proxyTo($url)
    {
        // return $this->noResponse()->setProxyTo(new Uri($url));
        return $this->expectation->setProxyTo(new Uri($url));
    }

    /*
     * @return \Mcustiel\Phiremock\Domain\Expectation
     */
//     public function noResponse()
//     {
//         return $this->expectation->setResponse(new Response());
//     }
}
