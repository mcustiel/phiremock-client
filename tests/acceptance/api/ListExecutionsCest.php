<?php

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
