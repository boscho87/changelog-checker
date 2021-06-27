<?php

namespace Boscho87\ChangelogChecker\Options;

use Boscho87\ChangelogChecker\Checkers\BracketChecker;
use Boscho87\ChangelogChecker\Checkers\Checkable;

/**
 * Class OptionResolver
 */
class OptionResolver
{
    private OptionLoader $loader;

    /**
     * OptionResolver constructor.
     */
    public function __construct(OptionLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @return array|Checkable[]
     */
    public function getCheckers(): array
    {
        $checkers[] = new BracketChecker($this->loader->versionBrackets);


        return $checkers;
    }
}
