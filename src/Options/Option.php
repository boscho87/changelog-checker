<?php

namespace Boscho87\ChangelogChecker\Options;

/**
 * Class Option
 */
class Option
{
    private bool $check;
    private bool $error;
    private bool $fix;
    private array $configs;

    /**
     * Option constructor.
     */
    public function __construct(bool $check, bool $error, bool $fix, array $configs)
    {
        $this->check = $check;
        $this->error = $error;
        $this->fix = $fix;
        $this->configs = $configs;
    }

    public function isCheck(): bool
    {
        return $this->check;
    }

    public function isError(): bool
    {
        return $this->error;
    }

    public function isFix(): bool
    {
        return $this->fix;
    }

    public function __get($name)
    {
        return $this->configs[$name] ?? null;
    }
}
