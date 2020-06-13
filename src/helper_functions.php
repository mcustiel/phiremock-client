<?php

namespace Mcustiel\Phiremock\Client;

use Mcustiel\Phiremock\Client\Utils\ConditionsBuilder;
use Mcustiel\Phiremock\Client\Utils\A;
use Mcustiel\Phiremock\Domain\Condition\Matchers\Matcher;
use Mcustiel\Phiremock\Domain\Condition\Matchers\Equals;
use Mcustiel\Phiremock\Domain\Condition\Matchers\MatcherFactory;
use Mcustiel\Phiremock\Client\Utils\Is;
use Mcustiel\Phiremock\Domain\Condition\Matchers\CaseInsensitiveEquals;
use Mcustiel\Phiremock\Domain\Condition\Matchers\RegExp;
use Mcustiel\Phiremock\Domain\Condition\Matchers\Contains;
use Mcustiel\Phiremock\Client\Utils\HttpResponseBuilder;
use Mcustiel\Phiremock\Client\Utils\Respond;
use Mcustiel\Phiremock\Client\Utils\ProxyResponseBuilder;
use Mcustiel\Phiremock\Client\Utils\Proxy;

// ConditionBuilder creators

function getRequest(): ConditionsBuilder
{
    return A::getRequest();
}

function postRequest(): ConditionsBuilder
{
    return A::postRequest();
}

function putRequest(): ConditionsBuilder
{
    return A::putRequest();
}

function deleteRequest(): ConditionsBuilder
{
    return A::deleteRequest();
}

function patchRequest(): ConditionsBuilder
{
    return A::patchRequest();
}

function headRequest(): ConditionsBuilder
{
    return A::headRequest();
}

function optionsRequest(): ConditionsBuilder
{
    return A::optionsRequest();
}

function fetchRequest(): ConditionsBuilder
{
    return A::fetchRequest();
}

function linkRequest(): ConditionsBuilder
{
    return A::linkRequest();
}

// Matcher creators

function isEqualTo($value): Equals
{
    return Is::equalTo($value);
}

function isSameStringAs(string $value): CaseInsensitiveEquals
{
    return Is::sameStringAs($value);
}

function matchesRegex(string $value): RegExp
{
    return Is::matching($value);
}

function isSameJsonAs($value): RegExp
{
    return Is::sameJsonObjectAs($value);
}

function contains(string $value): Contains
{
    return Is::containing($value);
}

// ResponseBuilder creators

function respond(int $statusCode): HttpResponseBuilder
{
    return Respond::withStatusCode($statusCode);
}

function proxyTo(string $url): ProxyResponseBuilder
{
    return Proxy::to($url);
}
