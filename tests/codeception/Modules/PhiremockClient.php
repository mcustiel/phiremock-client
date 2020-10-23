<?php

namespace Mcustiel\Codeception\Modules;

use Codeception\Lib\ModuleContainer;
use Codeception\Module;
use Mcustiel\Phiremock\Client\Connection\Host;
use Mcustiel\Phiremock\Client\Connection\Port;
use Mcustiel\Phiremock\Client\Factory as ClientFactory;
use Mcustiel\Phiremock\Client\Phiremock;
use Mcustiel\Phiremock\Client\Utils\Http\Scheme;

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
