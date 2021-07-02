<?php

namespace Boscho87\ChangelogChecker\Checkers;

use Boscho87\ChangelogChecker\Checkers\ChangedChecker\ChangeWatcher;
use Boscho87\ChangelogChecker\Checkers\ChangedChecker\CommitManager;
use Boscho87\ChangelogChecker\Options\Option;

/**
 * //todo refactor (also the watcher and der manager)!
 * Class ChangeChecker
 */
class ChangeChecker extends AbstractChecker
{
    private int $failAfterXCommits;


    /**
     * ChangeChecker constructor.
     */
    public function __construct(Option $options)
    {
        parent::__construct($options);
        $this->failAfterXCommits = (int)$this->options->fail_after;
    }

    protected function check(): void
    {
        $changeWatcher = new ChangeWatcher($this->file);
        if ($changeWatcher->changelogChangedSinceLastCommits(
            $this->failAfterXCommits
        )) {
            return;
        }
        $this->addErrorMessage(
            sprintf('Changelog not modified since %d commits', $this->failAfterXCommits)
        );
    }

    protected function fix(): void
    {
        $commitManager = new CommitManager($this->file);;
        $changeWatcher = new ChangeWatcher($this->file);
        if ($changeWatcher->changelogChangedSinceLastCommits(
            $this->failAfterXCommits,
        )) {
            return;
        }
        $commits = $commitManager->getLastCommits($this->failAfterXCommits);
        $commitManager->addCommitTitleToCommitArray($commits);
        $lineIndex = $commitManager->getLineToAddCommits();
        if (!$lineIndex) {
            $this->addErrorMessage(
                sprintf(
                    '%s could not be added to the Changelog, because the [Unreleased] tag is missing',
                    $commits
                )
            );
            return;
        }
        $commits = $commitManager->filterCommitsAlreadyInChangelog($commits);
        if (!empty($commits)) {
            $this->file->includeLinesAfter($commits, $lineIndex);
            $message = sprintf(
                'Added commit changes "%s" to line %s',
                $lineIndex,
                $this->file->lineNumber()
            );
            $this->addFixedMessage($message);
        }
    }
}
