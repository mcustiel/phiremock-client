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
        $this->assertSame(503, $response->getStatusCode()->asInt());
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
