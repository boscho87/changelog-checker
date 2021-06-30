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

    /**
     * IncreasedChecker constructor.
     */
    public function __construct(Option $options)
    {
        $this->commitLogFile = getcwd() . '/.clc.version';
        $this->checksumFile = getcwd() . '/.clc.checksum';
        parent::__construct($options);
    }

    protected function check(): void
    {
        $failAfterXCommits = $this->options->fail_after;
        $command = sprintf('git log --oneline -n %d', $failAfterXCommits);
        $result = shell_exec($command);
        $lines = array_filter(explode(PHP_EOL, $result));
        $newestCommit = $lines[0] ?? '';
        $commitLog = $this->getCommitLog();
        $position = strpos($commitLog, $newestCommit);

        if (!$this->changelogChanged()) {
            if ($position === false) {
                $this->addErrorMessage(
                    sprintf('Changelog not modified since %d commits', $failAfterXCommits)
                );
                return;
            }
            file_put_contents($this->commitLogFile, implode(PHP_EOL, $lines));
            return;
        }

        $this->markChangedChangelog();
    }

    protected function fix(): void
    {
        if ($this->changelogChanged()) {
            return;
        }
        $committedTitle = '### Committed';
        $failAfterXCommits = $this->options->fail_after;
        $command = sprintf('git log --oneline -n %d', $failAfterXCommits);
        $commits = shell_exec($command);
        $lines = array_filter(explode(PHP_EOL, $commits));
        $newestCommit = $lines[0] ?? '';
        $commitLog = $this->getCommitLog();
        $position = strpos($commitLog, $newestCommit);
        if ($position > 0) {
            return;
        }
        $commitArray = explode(PHP_EOL, $commits);
        $commitArray = array_filter($commitArray);
        $commitArray = array_map(fn ($commit) => "- $commit", $commitArray);
        $hasFixedCommits = strpos($this->file->getContents(), $committedTitle) !== false;
        if (!empty($commitArray) && !$hasFixedCommits) {
            array_unshift($commitArray, $committedTitle);
        }

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
        if (!isset($lineIndex)) {
            $this->addErrorMessage(
                sprintf(
                    '%s could not be added to the Changelog, because the [Unreleased] tag is missing',
                    $commits
                )
            );
            return;
        }
        $message = sprintf(
            'Added commit changes "%s" to line %s',
            $lineIndex,
            $this->file->lineNumber()
        );
        $this->addFixedMessage($message);
        foreach ($commitArray as $key => $commit) {
            if (strpos($this->file->getContents(), $commit)) {
                unset($commitArray[$key]);
            }
        }
        $commitArray = array_filter($commitArray);
        if (!empty($commitArray)) {
            $this->file->includeLinesAfter($commitArray, $lineIndex);
        }
        file_put_contents($this->commitLogFile, implode(PHP_EOL, $lines));
        $this->changelogChanged();
    }


    private function getCommitLog(): string
    {
        if (file_exists($this->commitLogFile) && is_file($this->commitLogFile)) {
            $lastResult = file_get_contents($this->commitLogFile);
        }
        return $lastResult ?? '';
    }

    private function getChecksum(): string
    {
        if (file_exists($this->checksumFile) && is_file($this->checksumFile)) {
            $lastResult = file_get_contents($this->checksumFile);
        }
        return $lastResult ?? '';
    }

    private function changelogChanged(): bool
    {
        $currentChecksum = md5($this->file->getContents());
        $lastChecksum = $this->getChecksum();
        return $lastChecksum !== $currentChecksum;
    }

    protected function markChangedChangelog(): void
    {
        file_put_contents($this->checksumFile, md5($this->file->getContents()));
    }
}
