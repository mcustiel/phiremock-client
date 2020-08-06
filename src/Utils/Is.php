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

use Mcustiel\Phiremock\Domain\Condition\Matchers\CaseInsensitiveEquals;
use Mcustiel\Phiremock\Domain\Condition\Matchers\Contains;
use Mcustiel\Phiremock\Domain\Condition\Matchers\Equals;
use Mcustiel\Phiremock\Domain\Condition\Matchers\JsonEquals;
use Mcustiel\Phiremock\Domain\Condition\Matchers\MatcherFactory;
use Mcustiel\Phiremock\Domain\Condition\Matchers\RegExp;

class Is
{
    public static function equalTo($value): Equals
    {
        return MatcherFactory::equalsTo($value);
    }

    public static function matching($value): RegExp
    {
        return MatcherFactory::matches($value);
    }

    public static function sameStringAs($value): CaseInsensitiveEquals
    {
        return MatcherFactory::sameString($value);
    }

    public static function containing($value): Contains
    {
        return MatcherFactory::contains($value);
    }

    public static function sameJsonObjectAs($value): JsonEquals
    {
        if (\is_string($value)) {
            return MatcherFactory::jsonEquals($value);
        }

        return MatcherFactory::jsonEquals(json_encode($value));
    }
}
