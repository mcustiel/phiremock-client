<?php

namespace Mcustiel\Phiremock\Client\Tests\Unit\Utils;

use Mcustiel\Phiremock\Client\Utils\HttpResponseBuilder;
use Mcustiel\Phiremock\Domain\Http\StatusCode;
use Mcustiel\Phiremock\Domain\HttpResponse;
use PHPUnit\Framework\TestCase;

class HttpResponseBuilderTest extends TestCase
{
    /** @var HttpResponseBuilder */
    private $builder;

    public function testCreatesAResponseExpectationWithDefaultValues()
    {
        $this->builder = new HttpResponseBuilder(new StatusCode(503));
        /** @var HttpResponse $response */
        $response = $this->builder->build();

        $this->assertInstanceOf(HttpResponse::class, $response);
        $this->assertSame(
            503,
            $response->getStatusCode()->asInt()
        );
        $this->assertSame('', $response->getBody()->asString());
        $this->assertFalse($response->hasDelayMillis());
        $this->assertTrue($response->getHeaders()->isEmpty());
    }

    public function testCreatesAResponseWithSetValues()
    {
        $this->builder = new HttpResponseBuilder(new StatusCode(418));
        $this->builder->andDelayInMillis(400);
        $this->builder->andBody('potato');
        $this->builder->andHeader('Content-Type', 'text/plain');
        $this->builder->andSetScenarioStateTo('tomatoScenarioState');

        $response = $this->builder->build();

        $this->assertInstanceOf(HttpResponse::class, $response);
        $this->assertSame('potato', $response->getBody()->asString());
        $this->assertTrue($response->hasDelayMillis());
        $this->assertSame(400, $response->getDelayMillis()->asInt());
        $this->assertTrue($response->hasNewScenarioState());
        $this->assertSame('tomatoScenarioState', $response->getNewScenarioState()->asString());
        $this->assertFalse($response->getHeaders()->isEmpty());
        $this->assertSame('Content-Type', $response->getHeaders()->current()->getName()->asString());
        $this->assertSame('text/plain', $response->getHeaders()->current()->getValue()->asString());
    }
}
