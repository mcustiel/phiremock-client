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

namespace Mcustiel\Codeception\Extensions;

use Codeception\Event\SuiteEvent;
use Codeception\Events;
use Symfony\Component\Process\Process;

class ServerControl extends \Codeception\Extension
{
    public static $events = [
        Events::SUITE_BEFORE => 'suiteBefore',
        Events::SUITE_AFTER  => 'suiteAfter',
    ];
    protected $config = [
        'https' => false,
    ];

    /** @var Process */
    private $application;

    public function suiteBefore(SuiteEvent $event)
    {
        $this->writeln('Starting Phiremock server');

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        $commandLine = [
            $isWindows ? PHP_BINARY : 'exec',
            './vendor/bin/phiremock',
            '-d',
        ];
        if ($this->config['https']) {
            $commandLine = array_merge(
                $commandLine,
                [
                    '--certificate=' . codecept_data_dir('certificate-cert.pem'),
                    '--certificate-key=' . codecept_data_dir('certificate-key.key'),
                ]
            );
        }

        $commandLine += array_merge(
            $commandLine,
            [
                '>',
                codecept_log_dir('phiremock.log'),
                '2>&1',
            ]
        );

        $this->application = Process::fromShellCommandline(implode(' ', $commandLine));
        $this->writeln($this->application->getCommandLine());
        $this->application->start();
        sleep(1);
    }

    public function suiteAfter()
    {
        $this->writeln('Stopping Phiremock server');
        if (!$this->application->isRunning()) {
            return;
        }

        $signal = defined('SIGTERM') ? \SIGTERM : 15;
        $this->application->stop(5, $signal);
        $this->writeln('Phiremock is stopped');
    }
}
