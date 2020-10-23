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

use Mcustiel\Phiremock\Client\Utils\A;
use Mcustiel\Phiremock\Client\Utils\ConditionsBuilder;
use Mcustiel\Phiremock\Client\Utils\ExpectationBuilder;
use Mcustiel\Phiremock\Client\Utils\HttpResponseBuilder;
use Mcustiel\Phiremock\Client\Utils\Is;
use Mcustiel\Phiremock\Client\Utils\Proxy;
use Mcustiel\Phiremock\Client\Utils\ProxyResponseBuilder;
use Mcustiel\Phiremock\Client\Utils\Respond;
use Mcustiel\Phiremock\Domain\Condition\Matchers\CaseInsensitiveEquals;
use Mcustiel\Phiremock\Domain\Condition\Matchers\Contains;
use Mcustiel\Phiremock\Domain\Condition\Matchers\Equals;
use Mcustiel\Phiremock\Domain\Condition\Matchers\JsonEquals;
use Mcustiel\Phiremock\Domain\Condition\Matchers\RegExp;

// ConditionBuilder creators

function request(): ConditionsBuilder
{
    return new ConditionsBuilder();
}

function getRequest(string $url = null): ConditionsBuilder
{
    $builder = A::getRequest();
    if ($url) {
        $builder->andUrl(isEqualTo($url));
    }
    return $builder;
}

function postRequest(): ConditionsBuilder
{
    return A::postRequest();
}

function putRequest(): ConditionsBuilder
{
    return A::putRequest();
}

function deleteRequest(string $url = null): ConditionsBuilder
{
    $builder = A::deleteRequest();
    if ($url) {
        $builder->andUrl(isEqualTo($url));
    }
    return $builder;
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

function isSameJsonAs($value): JsonEquals
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

// ExpectationBuilder creator

function on(ConditionsBuilder $builder): ExpectationBuilder
{
    return Phiremock::on($builder);
}

function onGetRequest(string $url = null): ExpectationBuilder
{
    return Phiremock::on(getRequest($url));
}

function onDeleteRequest(string $url = null): ConditionsBuilder
{
    return Phiremock::on(deleteRequest($url));
}
