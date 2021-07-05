<?php

namespace Boscho87\ChangelogChecker\Checkers\ChangedChecker;

use Boscho87\ChangelogChecker\FileManager\File;
use Boscho87\ChangelogChecker\FileManager\FileInterface;

/**
 * Class CommitManager
 */
class CommitManager
{
    private const COMMIT_TITLE = '### Committed';
    private FileInterface $changelogFile;


    /**
     * CommitManager constructor.
     */
    public function __construct(FileInterface $changelogFile)
    {
        $this->changelogFile = $changelogFile;
    }


    public function getLastCommits(int $commits): array
    {
        $command = sprintf('git log --oneline -n %d', $commits);
        $commits = shell_exec($command);
        $commitArray = explode(PHP_EOL, $commits);
        $commitArray = array_filter($commitArray);
        return array_map(fn ($commit) => "- $commit", $commitArray);
    }

    public function hasFixedCommits(): bool
    {
        return strpos($this->changelogFile->getContents(), self::COMMIT_TITLE) !== false;
    }

    public function addCommitTitleToCommitArray(array $commits): array
    {
        if (!empty($commits) && !$this->hasFixedCommits()) {
            array_unshift($commits, self::COMMIT_TITLE);
        }
        return $commits;
    }

    public function getLineToAddCommits(): ?int
    {
        foreach ($this->changelogFile as $line) {
            if ($this->hasFixedCommits()) {
                if (strpos($line, self::COMMIT_TITLE) !== false) {
                    $lineIndex = $this->changelogFile->key() + 1;
                    break;
                }
                continue;
            }
            if (strpos($line, '[Unreleased]')) {
                $lineIndex = $this->changelogFile->key() + 2;
                break;
            }
        }
        return $lineIndex ?? null;
    }

    public function filterCommitsAlreadyInChangelog(array $commits): array
    {
        foreach ($commits as $key => $commit) {
            if (strpos($this->changelogFile->getContents(), $commit)) {
                unset($commits[$key]);
            }
        }
        return array_filter($commits);
    }
}
