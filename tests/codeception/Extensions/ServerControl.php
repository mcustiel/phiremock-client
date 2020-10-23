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
    protected $config = [
        'https' => false,
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
        $this->application->stop(5, SIGTERM);
        $this->writeln('Phiremock is stopped');
    }
}
