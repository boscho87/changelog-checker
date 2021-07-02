<?php

namespace Boscho87\ChangelogChecker\Checkers\ChangedChecker;

use Boscho87\ChangelogChecker\FileManager\File;
use Boscho87\ChangelogChecker\FileManager\FileInterface;

/**
 * Class ChangeWatcher
 */
class ChangeWatcher
{
    private string $checksumFile;
    private string $commitLogFile;
    private FileInterface $changelogFile;

    /**
     * ChangeWatcher constructor.
     */
    public function __construct(FileInterface $file)
    {
        $this->changelogFile = $file;
        $this->commitLogFile = getcwd() . '/.clc.version';
        $this->checksumFile = getcwd() . '/.clc.checksum';
    }


    public function changelogChangedSinceLastCommits(int $commits): bool
    {
        $command = sprintf('git log --oneline -n %d', $commits);
        $commits = shell_exec($command);
        $lastResult = $this->getLastCommit();
        if (!strpos($commits, $lastResult)) {
            return $this->changelogChanged();
        }
        return true;
    }

    private function changelogChanged(): bool
    {
        $currentChecksum = $this->changelogFile->getHash();
        $lastChecksum = $this->getLastChangelogHash();
        if (trim($lastChecksum) !== trim($currentChecksum)) {
            $this->setChangelogChanged();
            return true;
        }
        return false;
    }

    private function setChangelogChanged()
    {
        $command = 'git log --oneline -n 1';
        $result = shell_exec($command);
        file_put_contents($this->commitLogFile, trim($result));
        file_put_contents($this->checksumFile, $this->changelogFile->getHash());
    }


    private function getLastCommit(): string
    {
        $path = realpath($this->commitLogFile);
        if ($path) {
            $commit = trim(file_get_contents($this->commitLogFile));
        }
        touch($this->commitLogFile);
        return $commit ?? '';
    }


    private function getLastChangelogHash(): string
    {
        $path = realpath($this->checksumFile);
        if ($path) {
            $checksum = trim(file_get_contents($this->checksumFile));
        }
        touch($this->checksumFile);
        return $checksum ?? '';
    }
}
