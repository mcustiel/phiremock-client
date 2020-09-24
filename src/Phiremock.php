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

use Laminas\Diactoros\Uri;
use Mcustiel\Phiremock\Client\Connection\Host;
use Mcustiel\Phiremock\Client\Connection\Port;
use Mcustiel\Phiremock\Client\Utils\ConditionsBuilder;
use Mcustiel\Phiremock\Client\Utils\ExpectationBuilder;
use Mcustiel\Phiremock\Common\Utils\ArrayToExpectationConverter;
use Mcustiel\Phiremock\Common\Utils\ExpectationToArrayConverter;
use Mcustiel\Phiremock\Common\Utils\ScenarioStateInfoToArrayConverter;
use Mcustiel\Phiremock\Domain\Expectation;
use Mcustiel\Phiremock\Domain\HttpResponse;
use Mcustiel\Phiremock\Domain\Options\ScenarioName;
use Mcustiel\Phiremock\Domain\Options\ScenarioState;
use Mcustiel\Phiremock\Domain\ScenarioStateInfo;
use Mcustiel\Phiremock\Domain\Version;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

use function GuzzleHttp\json_encode;

class Phiremock
{
    const API_EXPECTATIONS_URL = '/__phiremock/expectations';
    const API_EXECUTIONS_URL = '/__phiremock/executions';
    const API_SCENARIOS_URL = '/__phiremock/scenarios';
    const API_RESET_URL = '/__phiremock/reset';

    /** @var ClientInterface */
    private $connection;

    /** @var ArrayToExpectationConverter */
    private $arrayToExpectationConverter;

    /** @var ExpectationToArrayConverter */
    private $expectationToArrayConverter;

    /** @var ScenarioStateInfoToArrayConverter */
    private $scenarioStateInfoToArrayConverter;

    /** @var Host */
    private $host;

    /** @var Port */
    private $port;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    public function __construct(
        Host $host,
        Port $port,
        ClientInterface $remoteConnection,
        ExpectationToArrayConverter $expectationToArrayConverter,
        ArrayToExpectationConverter $arrayToExpectationConverter,
        ScenarioStateInfoToArrayConverter $scenarioStateInfoToArrayConverter,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->connection = $remoteConnection;
        $this->expectationToArrayConverter = $expectationToArrayConverter;
        $this->arrayToExpectationConverter = $arrayToExpectationConverter;
        $this->scenarioStateInfoToArrayConverter = $scenarioStateInfoToArrayConverter;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    /** Creates an expectation with a response for a given request. */
    public function createExpectation(Expectation $expectation): void
    {
        $body = @json_encode($this->expectationToArrayConverter->convert($expectation));
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Error generating json body for request: ' . json_last_error_msg());
        }
        $this->createExpectationFromJson($body);
    }

    /** Creates an expectation from a json configuration */
    public function createExpectationFromJson(string $body): void
    {
        $uri = $this->createBaseUri()->withPath(self::API_EXPECTATIONS_URL);
        $request = $this->requestFactory->createRequest('POST', $uri)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($body));
        $this->ensureIsExpectedResponse(201, $this->connection->sendRequest($request));
    }

    /** Restores pre-defined expectations and resets scenarios and requests counter. */
    public function reset(): void
    {
        $uri = $this->createBaseUri()->withPath(self::API_RESET_URL);
        $request = $this->requestFactory->createRequest('POST', $uri);

        $this->ensureIsExpectedResponse(200, $this->connection->sendRequest($request));
    }

    /** Clears all the currently configured expectations. */
    public function clearExpectations(): void
    {
        $uri = $this->createBaseUri()->withPath(self::API_EXPECTATIONS_URL);
        $request = $this->requestFactory->createRequest('DELETE', $uri);

        $this->ensureIsExpectedResponse(200, $this->connection->sendRequest($request));
    }

    /** @return \Mcustiel\Phiremock\Domain\Expectation[] */
    public function listExpectations(): array
    {
        $uri = $this->createBaseUri()->withPath(self::API_EXPECTATIONS_URL);
        $request = $this->requestFactory->createRequest('GET', $uri);
        $response = $this->connection->sendRequest($request);

        $this->ensureIsExpectedResponse(200, $response);

        $arraysList = json_decode((string)$response->getBody(), true);
        $expectationsList = [];

        foreach ($arraysList as $expectationArray) {
            $expectationsList[] = $this->arrayToExpectationConverter
                ->convert($expectationArray);
        }
        return $expectationsList;
    }

    /** @return int */
    public function countExecutions(?ConditionsBuilder $requestBuilder = null): int
    {
        $uri = $this->createBaseUri()->withPath(self::API_EXECUTIONS_URL);

        $request = $this->requestFactory->createRequest('POST', $uri)
            ->withHeader('Content-Type', 'application/json');

        if ($requestBuilder !== null) {
            $requestBuilderResult = $requestBuilder->build();
            $expectation = new Expectation(
                $requestBuilderResult->getRequestConditions(),
                HttpResponse::createEmpty(),
                $requestBuilderResult->getScenarioName(),
                null,
                new Version('2')
            );
            $jsonBody = json_encode($this->expectationToArrayConverter->convert($expectation));
            $request = $request->withBody($this->streamFactory->createStream($jsonBody));
        }

        $response = $this->connection->sendRequest($request);

        $this->ensureIsExpectedResponse(200, $response);
        $json = json_decode((string)$response->getBody());

        return $json->count;
    }

    /** @return array */
    public function listExecutions(?ConditionsBuilder $requestBuilder = null): array
    {
        $uri = $this->createBaseUri()->withPath(self::API_EXECUTIONS_URL);

        $request = $this->requestFactory->createRequest('PUT', $uri)
            ->withHeader('Content-Type', 'application/json');
        if ($requestBuilder !== null) {
            $requestBuilderResult = $requestBuilder->build();
            $expectation = new Expectation(
                $requestBuilderResult->getRequestConditions(),
                HttpResponse::createEmpty(),
                $requestBuilderResult->getScenarioName(),
                null,
                new Version('2')
            );

            $request = $request->withBody(
                $this->streamFactory->createStream(
                    json_encode($this->expectationToArrayConverter->convert($expectation))
                )
            );
        }

        $response = $this->connection->sendRequest($request);
        $this->ensureIsExpectedResponse(200, $response);
        return json_decode($response->getBody()->__toString());
    }

    /** Sets scenario state. */
    public function setScenarioState(string $scenarioName, string $scenarioState): void
    {
        $scenarioStateInfo = new ScenarioStateInfo(
            new ScenarioName($scenarioName),
            new ScenarioState($scenarioState)
        );
        $uri = $this->createBaseUri()->withPath(self::API_SCENARIOS_URL);
        $request = $this->requestFactory->createRequest('PUT', $uri)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(
                $this->streamFactory->createStream(
                    json_encode(
                        $this->scenarioStateInfoToArrayConverter->convert($scenarioStateInfo)
                    )
                )
            );

        $response = $this->connection->sendRequest($request);
        $this->ensureIsExpectedResponse(200, $response);
    }

    /** Resets all the scenarios to start state. */
    public function resetScenarios(): void
    {
        $uri = $this->createBaseUri()->withPath(self::API_SCENARIOS_URL);
        $request = $this->requestFactory->createRequest('DELETE', $uri);

        $this->ensureIsExpectedResponse(200, $this->connection->sendRequest($request));
    }

    /** Resets all the requests counters to 0. */
    public function resetRequestsCounter(): void
    {
        $uri = $this->createBaseUri()->withPath(self::API_EXECUTIONS_URL);
        $request = $this->requestFactory->createRequest('DELETE', $uri);

        $this->ensureIsExpectedResponse(200, $this->connection->sendRequest($request));
    }

    /**
     * Inits the fluent interface to create an expectation.
     *
     * @return \Mcustiel\Phiremock\Client\Utils\ExpectationBuilder
     */
    public static function on(ConditionsBuilder $requestBuilder): ExpectationBuilder
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
    public static function onRequest(string $method, string $url): ExpectationBuilder
    {
        return new ExpectationBuilder(
            ConditionsBuilder::create($method, $url)
        );
    }

    private function createBaseUri(): Uri
    {
        return (new Uri())
            ->withScheme('http')
            ->withHost($this->host->asString())
            ->withPort($this->port->asInt());
    }

    /**
     * @throws \RuntimeException
     */
    private function ensureIsExpectedResponse(int $statusCode, ResponseInterface $response)
    {
        $responseStatusCode = $response->getStatusCode();
        if ($responseStatusCode !== $statusCode) {
            if ($responseStatusCode >= 500) {
                $errors = json_decode($response->getBody()->__toString(), true)['details'];

                throw new \RuntimeException('An error occurred creating the expectation: ' . ($errors ? var_export($errors, true) : '') . $response->getBody()->__toString());
            }

            if ($responseStatusCode >= 400) {
                throw new \RuntimeException('Request error while creating the expectation');
            }
            throw new \RuntimeException('Unexpected response while creating the expectation');
        }
    }
}
