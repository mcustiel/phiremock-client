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

namespace Mcustiel\Phiremock\Client\Connection;

use InvalidArgumentException;

class Host
{
    /** @var string */
    private $host;

    public function __construct(string $host)
    {
        $this->ensureIsValidHost($host);
        $this->host = $host;
    }

    public static function createLocalhost(): Host
    {
        return new self('localhost');
    }

    public function asString(): string
    {
        return $this->host;
    }

    private function ensureIsValidHost(string $host): void
    {
        if (!is_string($host)) {
            throw new InvalidArgumentException('Host must be a string value');
        }
        if (filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false &&
            filter_var($host, FILTER_VALIDATE_IP) === false) {
            throw new InvalidArgumentException(sprintf('Invalid host number: %d', $host));
        }
    }
}
