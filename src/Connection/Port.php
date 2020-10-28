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

class Port
{
    /** @var int */
    private $port;

    public function __construct(int $port)
    {
        $this->ensureIsValidPort($port);
        $this->port = $port;
    }

    public function asInt(): int
    {
        return $this->port;
    }

    private function ensureIsValidPort(int $port): void
    {
        if (!is_int($port)) {
            throw new InvalidArgumentException('Port must be an integer value');
        }
        if ($port < 1 || $port > 65535) {
            throw new InvalidArgumentException(sprintf('Invalid port number: %d', $port));
        }
    }
}
