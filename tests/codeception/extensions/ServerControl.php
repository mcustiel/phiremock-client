<?php

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

    /** @var Process */
    private $application;

    public function suiteBefore(SuiteEvent $event)
    {
        $this->writeln('Starting Phiremock server');

        $commandLine = [
            'exec',
            './vendor/bin/phiremock',
            '-d',
            '>',
            codecept_log_dir('phiremock.log'),
            '2>&1',
        ];
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
        $this->application->stop(5, SIGTERM);
        $this->writeln('Phiremock is stopped');
    }
}
