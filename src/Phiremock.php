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

namespace Mcustiel\Phiremock\Client;

use Mcustiel\Phiremock\Client\Connection\Host;
use Mcustiel\Phiremock\Client\Connection\Port;
use Mcustiel\Phiremock\Client\Utils\ConditionsBuilder;
use Mcustiel\Phiremock\Client\Utils\ExpectationBuilder;
use Mcustiel\Phiremock\Common\Http\RemoteConnectionInterface;
use Mcustiel\Phiremock\Common\StringStream;
use Mcustiel\Phiremock\Common\Utils\ArrayToExpectationConverter;
use Mcustiel\Phiremock\Domain\MockConfig;
use Mcustiel\Phiremock\Domain\Response;
use Mcustiel\Phiremock\Domain\ScenarioStateInfo;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request as PsrRequest;
use Zend\Diactoros\Uri;
use Mcustiel\Phiremock\Common\Utils\ExpectationToArrayConverter;

class Phiremock
{
    const API_EXPECTATIONS_URL = '/__phiremock/expectations';
    const API_EXECUTIONS_URL = '/__phiremock/executions';
    const API_SCENARIOS_URL = '/__phiremock/scenarios';
    const API_RESET_URL = '/__phiremock/reset';

    /** @var RemoteConnectionInterface */
    private $connection;

    /** @var ArrayToExpectationConverter */
    private $arrayToExpectationConverter;

    /** @var ArrayToExpectationConverter */
    private $expectationToArrayConverter;

    /** @var Host */
    private $host;

    /** @var Port */
    private $port;

    public function __construct(
        Host $host,
        Port $port,
        RemoteConnectionInterface $remoteConnection,
        ExpectationToArrayConverter $mockConfigToArrayConverter,
        ArrayToExpectationConverter $arrayToMockConfigConverter
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->connection = $remoteConnection;
        $this->expectationToArrayConverter = $mockConfigToArrayConverter;
        $this->arrayToExpectationConverter = $arrayToMockConfigConverter;
    }

    /**
     * Creates an expectation with a response for a given request.
     *
     * @param \Mcustiel\Phiremock\Domain\MockConfig $expectation
     */
    public function createMockConfig(MockConfig $expectation)
    {
        $uri = $this->createBaseUri()->withPath(self::API_EXPECTATIONS_URL);
        $body = @json_encode($this->expectationToArrayConverter->convert($expectation));
        if (false === $body) {
            throw new \RuntimeException('Error generating json body for request: ' . json_last_error_msg());
        }
        $request = (new PsrRequest())
            ->withUri($uri)
            ->withMethod('post')
            ->withHeader('Content-Type', 'application/json')
            ->withBody(new StringStream($body));
        $this->ensureIsNotErrorResponse($this->connection->send($request));
    }

    /**
     * Restores pre-defined expectations and resets scenarios and requests counter.
     */
    public function reset()
    {
        $uri = $this->createBaseUri()->withPath(self::API_RESET_URL);
        $request = (new PsrRequest())->withUri($uri)->withMethod('post');

        $this->ensureIsNotErrorResponse($this->connection->send($request));
    }

    /**
     * Clears all the currently configured expectations.
     */
    public function clearMockConfigs()
    {
        $uri = $this->createBaseUri()->withPath(self::API_EXPECTATIONS_URL);
        $request = (new PsrRequest())->withUri($uri)->withMethod('delete');

        $this->ensureIsNotErrorResponse($this->connection->send($request));
    }

    /**
     * Lists all currently configured expectations.
     *
     * @return \Mcustiel\Phiremock\Domain\MockConfig[]
     */
    public function listMockConfigs()
    {
        $uri = $this->createBaseUri()->withPath(self::API_EXPECTATIONS_URL);
        $request = (new PsrRequest())->withUri($uri)->withMethod('get');
        $response = $this->connection->send($request);

        if (200 === $response->getStatusCode()) {
            return $this->arrayToExpectationConverter->convert(
                json_decode($response->getBody()->__toString(), true)
            );
        }

        $this->ensureIsNotErrorResponse($response);
    }

    /**
     * Counts the amount of times a request was executed in phiremock.
     *
     * @param \Mcustiel\Phiremock\Client\Utils\ConditionsBuilder $requestBuilder
     *
     * @return int
     */
    public function countExecutions(ConditionsBuilder $requestBuilder)
    {
        $expectation = $requestBuilder->build();
        $expectation->setResponse(new Response());
        $uri = $this->createBaseUri()->withPath(self::API_EXECUTIONS_URL);

        $request = (new PsrRequest())
            ->withUri($uri)
            ->withMethod('post')
            ->withHeader('Content-Type', 'application/json')
            ->withBody(new StringStream(json_encode($expectation)));

        $response = $this->connection->send($request);

        if (200 === $response->getStatusCode()) {
            $json = json_decode($response->getBody()->__toString());

            return $json->count;
        }

        $this->ensureIsNotErrorResponse($response);
    }

    /**
     * List requests was executed in phiremock.
     *
     * @param \Mcustiel\Phiremock\Client\Utils\ConditionsBuilder $requestBuilder
     *
     * @return array
     */
    public function listExecutions(ConditionsBuilder $requestBuilder)
    {
        $expectation = $requestBuilder->build();
        $expectation->setResponse(Response::createEmpty());
        $uri = $this->createBaseUri()->withPath(self::API_EXECUTIONS_URL);

        $request = (new PsrRequest())
            ->withUri($uri)
            ->withMethod('put')
            ->withHeader('Content-Type', 'application/json')
            ->withBody(new StringStream(json_encode($expectation)));

        $response = $this->connection->send($request);

        if (200 === $response->getStatusCode()) {
            return json_decode($response->getBody()->__toString());
        }

        $this->ensureIsNotErrorResponse($response);
    }

    /**
     * Sets scenario state.
     *
     * @param \Mcustiel\Phiremock\Domain\ScenarioStateInfo $scenarioState
     */
    public function setScenarioState(ScenarioStateInfo $scenarioState)
    {
        $uri = $this->createBaseUri()->withPath(self::API_SCENARIOS_URL);
        $request = (new PsrRequest())
            ->withUri($uri)
            ->withMethod('put')
            ->withHeader('Content-Type', 'application/json')
            ->withBody(new StringStream(json_encode($scenarioState)));

        $response = $this->connection->send($request);
        if (200 !== $response->getStatusCode()) {
            $this->ensureIsNotErrorResponse($response);
        }
    }

    /**
     * Resets all the scenarios to start state.
     */
    public function resetScenarios()
    {
        $uri = $this->createBaseUri()->withPath(self::API_SCENARIOS_URL);
        $request = (new PsrRequest())->withUri($uri)->withMethod('delete');

        $this->ensureIsNotErrorResponse($this->connection->send($request));
    }

    /**
     * Resets all the requests counters to 0.
     */
    public function resetRequestsCounter()
    {
        $uri = $this->createBaseUri()->withPath(self::API_EXECUTIONS_URL);
        $request = (new PsrRequest())->withUri($uri)->withMethod('delete');

        $this->ensureIsNotErrorResponse($this->connection->send($request));
    }

    /**
     * Inits the fluent interface to create an expectation.
     *
     * @param \Mcustiel\Phiremock\Client\Utils\ConditionsBuilder $requestBuilder
     *
     * @return \Mcustiel\Phiremock\Client\Utils\ExpectationBuilder
     */
    public static function on(ConditionsBuilder $requestBuilder)
    {
        return new ExpectationBuilder($requestBuilder);
    }

    /**
     * Shortcut.
     *
     * @param string $method
     * @param string $url
     *
     * @return \Mcustiel\Phiremock\Client\Utils\ExpectationBuilder
     */
    public static function onRequest($method, $url)
    {
        return new ExpectationBuilder(
            ConditionsBuilder::create($method, $url)
        );
    }

    /**
     * @return \Zend\Diactoros\Uri
     */
    private function createBaseUri()
    {
        return (new Uri())
            ->withScheme('http')
            ->withHost($this->host->asString())
            ->withPort($this->port->asInt());
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @throws \RuntimeException
     */
    private function ensureIsNotErrorResponse(ResponseInterface $response)
    {
        if ($response->getStatusCode() >= 500) {
            $errors = json_decode($response->getBody()->__toString(), true)['details'];

            throw new \RuntimeException(
                'An error occurred creating the expectation: '
                . ($errors ? var_export($errors, true) : '')
                . $response->getBody()->__toString());
        }

        if ($response->getStatusCode() >= 400) {
            throw new \RuntimeException('Request error while creating the expectation');
        }
    }
}
