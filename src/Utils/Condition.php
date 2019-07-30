<?php

namespace Mcustiel\Phiremock\Client\Utils;

use InvalidArgumentException;
use Mcustiel\Phiremock\Domain\Conditions\MatchersEnum;

class Condition
{
    /** @var string */
    private $matcherName;
    /** @var string */
    private $value;

    /**
     * @param string $matcherName
     * @param string $value
     */
    public function __construct($matcherName, $value)
    {
        MatchersEnum::isValidMatcher($matcherName);
        $this->ensureIsString($value);
        $this->matcherName = $matcherName;
        $this->value = $value;
    }

    /** @return string */
    public function getMatcherName()
    {
        return $this->matcherName;
    }

    /** @return string */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $pattern
     *
     * @throws InvalidArgumentException
     */
    private function ensureIsString($pattern)
    {
        if (!\is_string($pattern)) {
            throw new InvalidArgumentException(
                sprintf('Expected string got: %s', \gettype($pattern))
            );
        }
    }
}
