<?php

namespace Boscho87\ChangelogChecker\Checkers;

use Boscho87\ChangelogChecker\Options\Option;

/**
 * Class IncreasedChecker
 */
class IncreasedChecker extends AbstractChecker
{
    private string $commitLogFile;
    private string $checksumFile;
    private int $failAfterXCommits;

    /**
     * IncreasedChecker constructor.
     */
    public function __construct(Option $options)
    {
        parent::__construct($options);
        $this->failAfterXCommits = (int)$this->options->fail_after;
        $this->commitLogFile = getcwd() . '/.clc.version';
        $this->checksumFile = getcwd() . '/.clc.checksum';
    }

    protected function check(): void
    {
        if ($this->changelogChanged()) {
            $this->setChangelogChanged();
        }
        if (!$this->lastChangeInValidRange()) {
            $this->addErrorMessage(
                sprintf('Changelog not modified since %d commits', $this->failAfterXCommits)
            );
        }
    }

    protected function fix(): void
    {
        if ($this->lastChangeInValidRange()) {
            return;
        }

        $committedTitle = '### Committed';
        $failAfterXCommits = $this->options->fail_after;
        $command = sprintf('git log --oneline -n %d', $failAfterXCommits);
        $commits = shell_exec($command);
        $commitArray = explode(PHP_EOL, $commits);
        $commitArray = array_filter($commitArray);
        $commitArray = array_map(fn ($commit) => "- $commit", $commitArray);
        $hasFixedCommits = strpos($this->file->getContents(), $committedTitle) !== false;
        if (!empty($commitArray) && !$hasFixedCommits) {
            array_unshift($commitArray, $committedTitle);
        }
        $lineIndex = $this->getLineToAddCommits($committedTitle);
        if (!isset($lineIndex)) {
            $this->addErrorMessage(
                sprintf(
                    '%s could not be added to the Changelog, because the [Unreleased] tag is missing',
                    $commits
                )
            );
            return;
        }

        foreach ($commitArray as $key => $commit) {
            if (strpos($this->file->getContents(), $commit)) {
                unset($commitArray[$key]);
            }
        }
        $commitArray = array_filter($commitArray);
        if (!empty($commitArray)) {
            $this->file->includeLinesAfter($commitArray, $lineIndex);
            $message = sprintf(
                'Added commit changes "%s" to line %s',
                $lineIndex,
                $this->file->lineNumber()
            );
            $this->addFixedMessage($message);
            $this->setChangelogChanged();
        }
    }


    protected function lastChangeInValidRange(): bool
    {
        $command = sprintf('git log --oneline -n %d', $this->failAfterXCommits);
        $commits = shell_exec($command);
        $lastResult = $this->getCommitLog();
        if (!strpos($commits, $lastResult)) {
            return $this->changelogChanged();
        }
        return true;
    }

    protected function setChangelogChanged()
    {
        $command = 'git log --oneline -n 1';
        $result = shell_exec($command);
        file_put_contents($this->commitLogFile, trim($result));
        file_put_contents($this->checksumFile, md5($this->file->getContents()));
    }


    private function changelogChanged(): bool
    {
        $currentChecksum = md5($this->file->getContents());
        $lastChecksum = $this->getChecksum();
        return trim($lastChecksum) !== trim($currentChecksum);
    }

    private function getChecksum(): string
    {
        if (file_exists($this->checksumFile) && is_file($this->checksumFile)) {
            $lastResult = file_get_contents($this->checksumFile);
        }
        return $lastResult ?? '';
    }

    private function getCommitLog(): string
    {
        if (file_exists($this->commitLogFile) && is_file($this->commitLogFile)) {
            $lastResult = file_get_contents($this->commitLogFile);
        }
        return $lastResult ?? '';
    }


    protected function getLineToAddCommits(string $committedTitle): ?int
    {
        $hasFixedCommits = strpos($this->file->getContents(), $committedTitle) !== false;
        foreach ($this->file as $line) {
            if ($hasFixedCommits) {
                if (strpos($line, $committedTitle) !== false) {
                    $lineIndex = $this->file->key() + 1;
                    break;
                }
                continue;
            }
            if (strpos($line, '[Unreleased]')) {
                $lineIndex = $this->file->key() + 2;
                break;
            }
        }
        return $lineIndex ?? null;
    }
}
