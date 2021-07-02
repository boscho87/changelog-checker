<?php

namespace Boscho87\ChangelogChecker\Checkers;

use Boscho87\ChangelogChecker\Checkers\ChangedChecker\ChangeWatcher;
use Boscho87\ChangelogChecker\Checkers\ChangedChecker\CommitManager;
use Boscho87\ChangelogChecker\Options\Option;

/**
 * Class ChangeChecker
 */
class ChangeChecker extends AbstractChecker
{
    private int $failAfterXCommits;
    private ChangeWatcher $changeWatcher;
    private CommitManager $commitManager;

    /**
     * ChangeChecker constructor.
     */
    public function __construct(Option $options)
    {
        parent::__construct($options);
        $this->failAfterXCommits = (int)$this->options->fail_after;
        $this->changeWatcher = new ChangeWatcher();
    }

    protected function check(): void
    {
        $this->commitManager = new CommitManager($this->file);

        if ($this->changeWatcher->changelogChangedSinceLastCommits(
            $this->file,
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
        $this->commitManager = new CommitManager($this->file);
        ;
        $failAfterXCommits = $this->options->fail_after;

        if (!$this->changeWatcher->changelogChangedSinceLastCommits(
            $this->file,
            $failAfterXCommits
        )) {
            return;
        }

        $commits = $this->commitManager->getLastCommits($failAfterXCommits);
        $this->commitManager->addCommitTitleToCommitArray($commits);
        $lineIndex = $this->commitManager->getLineToAddCommits();
        if (!isset($lineIndex)) {
            $this->addErrorMessage(
                sprintf(
                    '%s could not be added to the Changelog, because the [Unreleased] tag is missing',
                    $commits
                )
            );
            return;
        }

        foreach ($commits as $key => $commit) {
            if (strpos($this->file->getContents(), $commit)) {
                unset($commits[$key]);
            }
        }
        $commits = array_filter($commits);
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
