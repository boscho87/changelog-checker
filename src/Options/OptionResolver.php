<?php

namespace Boscho87\ChangelogChecker\Options;

use Boscho87\ChangelogChecker\Checkers\IncreasedChecker;
use Boscho87\ChangelogChecker\Checkers\SequenceChecker;
use Boscho87\ChangelogChecker\Checkers\TypeChecker;
use Boscho87\ChangelogChecker\Checkers\AscendingVersionChecker;
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
        $checkers = [
            new AscendingVersionChecker($this->loader->ascendingVersion),
            new BracketChecker($this->loader->versionBrackets),
            new TypeChecker($this->loader->actions),
            new IncreasedChecker($this->loader->increased),
            new SequenceChecker($this->loader->sequence),
        ];



        return $checkers;
    }
}
