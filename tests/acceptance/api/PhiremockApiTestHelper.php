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

namespace Mcustiel\Phiremock\Client\Tests\Acceptance;

use ApiTester;
use Mcustiel\Phiremock\Client\Phiremock;

trait PhiremockApiTestHelper
{
    /** @var Phiremock */
    private $phiremock;

    public function _before(ApiTester $I)
    {
        if ($this->phiremock === null) {
            $this->phiremock = $I->getPhiremockClient();
        }
        $this->phiremock->reset();
    }

    public function _getPhiremockClient(): Phiremock
    {
        return $this->phiremock;
    }
}
