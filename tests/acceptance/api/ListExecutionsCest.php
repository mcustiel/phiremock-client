<?php

/**
 * This file is part of phiremock-codeception-extension.
 *
 * phiremock-codeception-extension is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * phiremock-codeception-extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with phiremock-codeception-extension.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Mcustiel\Phiremock\Client\Tests\Acceptance;

use ApiTester;
use function Mcustiel\Phiremock\Client\getRequest;
use function Mcustiel\Phiremock\Client\postRequest;

class ListExecutionsCest
{
    use PhiremockApiTestHelper;

    public function listsRequestsBasedInDefinition(ApiTester $I)
    {
        $I->assertEmpty($this->_getPhiremockClient()->listExecutions(getRequest()));
        $I->sendGet('/tomato');
        $requests = $this->_getPhiremockClient()->listExecutions(getRequest());
        $I->assertCount(1, $requests);
        $I->assertEquals(
            [
                (object) [
                    'method'  => 'GET',
                    'url'     => $I->getPhiremockScheme() . '://localhost:8086/tomato',
                    'headers' => (object) [
                        'Host' => [
                            'localhost:8086',
                        ],
                        'User-Agent' => [
                            'Symfony BrowserKit',
                        ]
                    ],
                    'cookies' => [],
                    'body'    => '',
                ],
            ],
            $requests
        );
        $I->assertEmpty($this->_getPhiremockClient()->listExecutions(postRequest()));
    }

    public function countsAllRequests(ApiTester $I)
    {
        $expectedGetRequest = (object) [
            'method'  => 'GET',
            'url'     => $I->getPhiremockScheme() . '://localhost:8086/tomato',
            'headers' => (object) [
                'Host' => [
                    'localhost:8086',
                ],
                'User-Agent' => [
                    'Symfony BrowserKit',
                ]
            ],
            'cookies' => [],
            'body'    => '',
        ];
        $expectedPostRequest = (object) [
            'method'  => 'POST',
            'url'     => $I->getPhiremockScheme() . '://localhost:8086/potato',
            'headers' => (object) [
                'Host' => [
                    'localhost:8086',
                ],
                'User-Agent' => [
                    'Symfony BrowserKit',
                ],
                'Content-Type'   => ['application/json'],
                'Referer'        => [$I->getPhiremockScheme() . '://localhost:8086/tomato'],
                'Content-Length' => ['20'],
            ],
            'cookies' => [],
            'body'    => '{"banana":"coconut"}',
        ];

        $I->assertEmpty($this->_getPhiremockClient()->listExecutions());
        $I->sendGet('/tomato');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost('/potato', ['banana' => 'coconut']);

        $requests = $this->_getPhiremockClient()->listExecutions(getRequest());
        $I->assertCount(1, $requests);
        $I->assertEquals([$expectedGetRequest], $requests);

        $requests = $this->_getPhiremockClient()->listExecutions(postRequest());
        $I->assertCount(1, $requests);
        $I->assertEquals([$expectedPostRequest], $requests);

        $requests = $this->_getPhiremockClient()->listExecutions();
        $I->assertCount(2, $requests);
        $I->assertEquals([$expectedGetRequest, $expectedPostRequest], $requests);
    }
}
