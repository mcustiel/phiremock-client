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

namespace Mcustiel\Codeception\Modules;

use Codeception\Lib\ModuleContainer;
use Codeception\Module;
use Mcustiel\Phiremock\Client\Connection\Host;
use Mcustiel\Phiremock\Client\Connection\Port;
use Mcustiel\Phiremock\Client\Connection\Scheme;
use Mcustiel\Phiremock\Client\Factory as ClientFactory;
use Mcustiel\Phiremock\Client\Phiremock;

class PhiremockClient extends Module
{
    protected $config = ['https' => false];

    /** @var Scheme */
    private $scheme;

    public function __construct(ModuleContainer $moduleContainer, $config = null)
    {
        parent::__construct($moduleContainer, $config);
        $this->scheme = $this->config['https'] ? Scheme::createHttps() : Scheme::createHttp();
    }

    public function getPhiremockClient(): Phiremock
    {
        $factory = ClientFactory::createDefault();
        return $factory->createPhiremockClient(new Host('localhost'), new Port(8086), $this->scheme);
    }

    public function getPhiremockScheme(): string
    {
        return $this->scheme->asString();
    }
}
