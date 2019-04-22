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
    /** @return ConditionsBuilder */
    public static function getRequest()
    {
        return ConditionsBuilder::create(MethodsEnum::GET);
    }

    /** @return ConditionsBuilder */
    public static function postRequest()
    {
        return ConditionsBuilder::create(MethodsEnum::POST);
    }

    /** @return ConditionsBuilder */
    public static function putRequest()
    {
        return ConditionsBuilder::create(MethodsEnum::PUT);
    }

    /** @return ConditionsBuilder */
    public static function optionsRequest()
    {
        return ConditionsBuilder::create(MethodsEnum::OPTIONS);
    }

    /** @return ConditionsBuilder */
    public static function headRequest()
    {
        return ConditionsBuilder::create(MethodsEnum::HEAD);
    }

    /** @return ConditionsBuilder */
    public static function fetchRequest()
    {
        return ConditionsBuilder::create(MethodsEnum::FETCH);
    }

    /** @return ConditionsBuilder */
    public static function deleteRequest()
    {
        return ConditionsBuilder::create(MethodsEnum::DELETE);
    }

    /** @return ConditionsBuilder */
    public static function patchRequest()
    {
        return ConditionsBuilder::create(MethodsEnum::PATCH);
    }
}
