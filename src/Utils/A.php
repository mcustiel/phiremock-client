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

use Mcustiel\Phiremock\Domain\Http\MethodsEnum;

class A
{
    public static function getRequest(): ConditionsBuilder
    {
        return ConditionsBuilder::create(MethodsEnum::GET);
    }

    public static function postRequest(): ConditionsBuilder
    {
        return ConditionsBuilder::create(MethodsEnum::POST);
    }

    public static function putRequest(): ConditionsBuilder
    {
        return ConditionsBuilder::create(MethodsEnum::PUT);
    }

    public static function optionsRequest(): ConditionsBuilder
    {
        return ConditionsBuilder::create(MethodsEnum::OPTIONS);
    }

    public static function headRequest(): ConditionsBuilder
    {
        return ConditionsBuilder::create(MethodsEnum::HEAD);
    }

    public static function fetchRequest(): ConditionsBuilder
    {
        return ConditionsBuilder::create(MethodsEnum::FETCH);
    }

    public static function deleteRequest(): ConditionsBuilder
    {
        return ConditionsBuilder::create(MethodsEnum::DELETE);
    }

    public static function patchRequest(): ConditionsBuilder
    {
        return ConditionsBuilder::create(MethodsEnum::PATCH);
    }

    public static function linkRequest(): ConditionsBuilder
    {
        return ConditionsBuilder::create(MethodsEnum::LINK);
    }
}
