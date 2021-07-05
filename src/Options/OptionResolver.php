<?php

namespace Boscho87\ChangelogChecker\Options;

use Boscho87\ChangelogChecker\Checkers\DefaultChecker;
use Boscho87\ChangelogChecker\Checkers\ChangeChecker;
use Boscho87\ChangelogChecker\Checkers\LinkChecker;
use Boscho87\ChangelogChecker\Checkers\SequenceChecker;
use Boscho87\ChangelogChecker\Checkers\TypeChecker;
use Boscho87\ChangelogChecker\Checkers\AscendingVersionChecker;
use Boscho87\ChangelogChecker\Checkers\BracketChecker;
use Boscho87\ChangelogChecker\Checkers\Checkable;

/**
 * @codeCoverageIgnore
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
        //register all the checkers here
        $checkers = [
            new DefaultChecker(), // this should always run first
            new BracketChecker($this->loader->versionBrackets), // this should run second
            new AscendingVersionChecker($this->loader->ascendingVersion),
            new TypeChecker($this->loader->actions),
            new ChangeChecker($this->loader->increased),
            new LinkChecker($this->loader->linkChecker)
        ];

        return $checkers;
    }
}
