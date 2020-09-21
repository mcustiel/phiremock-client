# Phiremock Client

Phiremock client provides a nice API to interact with [Phiremock Server](https://github.com/mcustiel/phiremock-server), allowing developers to setup expectations, clear state, scenarios etc. Through a fluent interface.

[![Build Status](https://scrutinizer-ci.com/g/mcustiel/phiremock-client/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mcustiel/phiremock/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mcustiel/phiremock-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mcustiel/phiremock/?branch=master)

## Installation

### Default installation through composer

This project is published in packagist, so you just need to add it as a dependency in your composer.json:

```json
    "require-dev": {
        "mcustiel/phiremock-client": "^1.0",
        "guzzlehttp/guzzle": "^6.0"
    }
```
Phiremock Client requires guzzle client v6 to work. This dependency can be avoided and you can choose any psr18-compatible http client and overwrite Phiremock Client's factory to provide it.


### Overwriting the factory class

If guzzle client v6 is provided as a dependency no extra configuration is needed. If you want to use a different http client you need to provide it to phiremock server as a psr18-compatible client.
For instance, if you want to use guzzle client v7 you need to extend phiremock server's factory class:

```php
<?php
namespace My\Namespace;

use Mcustiel\Phiremock\Client\Factory;
use GuzzleHttp;
use Psr\Http\Client\ClientInterface;

class FactoryWithGuzzle7 extends Factory
{
    public function createRemoteConnection(): ClientInterface
    {
        return new GuzzleHttp\Client();
    }
}
```
Then use this factory class to create the Phiremock Client Facade.

## Usage

### Creating the Client Facade
Create the Client Facade by requesting it from the factory object:

```php
<?php
use Mcustiel\Phiremock\Client\Connection\Host;
use Mcustiel\Phiremock\Client\Connection\Port;

$phiremockClient = Factory::createDefault()->createPhiremockClient(new Host('my.phiremock.host'), new Port('8080'));
```

Now you can use `$phiremockClient` to access all the configuration options of Phiremock Server. 

### Expectation creation

```php
<?php
use Mcustiel\Phiremock\Client\Phiremock;
use Mcustiel\Phiremock\Client\Utils\A;
use Mcustiel\Phiremock\Client\Utils\Is;
use Mcustiel\Phiremock\Client\Utils\Respond;

// ...
$phiremockClient->createExpectation(
    Phiremock::on(
        A::getRequest()
            ->andUrl(Is::equalTo('/potato/tomato'))
            ->andBody(Is::containing('42'))
            ->andHeader('Accept', Is::equalTo('application/banana'))
            ->andFormField('name', Is::equalTo('potato'))
    )->then(
        Respond::withStatusCode(418)
            ->andBody('Is the answer to the Ultimate Question of Life, The Universe, and Everything')
            ->andHeader('Content-Type', 'application/banana')
    )->withPriority(5);
);

```

Also a cleaner/shorter way to create expectations is provided by using helper functions:

```php
<?php
use Mcustiel\Phiremock\Client\Phiremock;
use function Mcustiel\Phiremock\Client\contains;
use function Mcustiel\Phiremock\Client\getRequest;
use function Mcustiel\Phiremock\Client\isEqualTo;
use function Mcustiel\Phiremock\Client\request;
use function Mcustiel\Phiremock\Client\respond;
use function Mcustiel\Phiremock\Client\on;
// ...
$phiremockClient->createExpectation(
    on(
        getRequest()
            ->andUrl(isEqualTo('/potato/tomato'))
            ->andBody(contains('42'))
            ->andHeader('Accept', isEqualTo('application/banana'))
            ->andFormField('name', isEqualTo('potato'))
    )->then(
        respond(418)
            ->andBody('Is the answer to the Ultimate Question of Life, The Universe, and Everything')
            ->andHeader('Content-Type', 'application/banana')
    )->withPriority(5)
);
```

### Listing created expectations
The `listExpecatations` method returns an array of instances of the Expectation class containing all the current expectations checked by Phiremock Server.

```php
<?php
$expectations = $phiremockClient->listExpectations();
```

### Clear all configured expectations
This will remove all expectations checked, causing Phiremock Server to return 404 for every non-phiremock-api request.

```php
<?php
$phiremockClient->clearExpectations();
```

### List requests received by Phiremock
Use this method to get a list of Psr-compatible Requests received by Phiremock Server.

Lists all requests:

```php
<?php
$phiremockClient->listExecutions();
```

Lists requests matching condition:

```php
<?php
$phiremockClient->listExecutions(getRequest()->andUrl(isEqualTo('/test'));
```

**Note:** Phiremock's API request are excluded from this list.

### Count requests received by Phiremock
Provides an integer >= 0 representing the amount of requests received by Phiremock Server.

Count all requests:

```php
<?php
$phiremockClient->listExecutions();
```

Count requests matching condition:

```php
<?php
$phiremockClient->listExecutions(getRequest()->andUrl(isEqualTo('/test'));
```

**Note:** Phiremock's API request are excluded from this list.

### Clear stored requests
This will clean the list of requests saved on Phiremock Server and resets the counter to 0. 

```php
<?php
$phiremockClient->clearRequests();
```

### Set Scenario State
Force a scenario to have certain state on Phiremock Server.

```php
<?php
$phiremockClient->setScenarioState('myScenario', 'loginExecuted');
```

### Reset Scenarios States
Reset all scenarios to the initial state (Scenario.START).

```php
<?php
$phiremockClient->resetScenarios();
```

### Reset all
Sets Phiremock Server to its initial state. This will cause Phiremock Server to:
1. clear all expectations.
2. clear the stored requests.
3. reset all the scenarios.
4. reload all expectations stored in files.

```php
<?php
$phiremockClient->reset();
```

## Appendix

### See also:

Phiremock Server: https://github.com/mcustiel/phiremock-server
Examples in tests: https://github.com/mcustiel/phiremock-client/tree/master/tests/acceptance/api

### Contributing:

Just submit a pull request. Don't forget to run tests and php-cs-fixer first and write documentation.

### Thanks to:

* Denis Rudoi ([@drudoi](https://github.com/drudoi))
* Henrik Schmidt ([@mrIncompetent](https://github.com/mrIncompetent))
* Nils Gajsek ([@linslin](https://github.com/linslin))
* Florian Levis ([@Gounlaf](https://github.com/Gounlaf))

And [everyone](https://github.com/mcustiel/phiremock/graphs/contributors) who submitted their Pull Requests.
