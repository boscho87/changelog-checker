<?php

namespace Boscho87\ChangelogChecker\Checkers\ChangedChecker;

use Boscho87\ChangelogChecker\FileManager\File;

/**
 * Class ChangeWatcher
 */
class ChangeWatcher
{
    private string $checksumFile;
    private string $commitLogFile;

    /**
     * ChangeWatcher constructor.
     */
    public function __construct()
    {
        $this->commitLogFile = getcwd() . '/.clc.version';
        $this->checksumFile = getcwd() . '/.clc.checksum';
    }


    public function changelogChangedSinceLastCommits(File $file, int $commits): bool
    {
        $command = sprintf('git log --oneline -n %d', $commits);
        $commits = shell_exec($command);
        $lastResult = $this->getLastCommit();
        if (!strpos($commits, $lastResult)) {
            return $this->changelogChanged($file);
        }
        return true;
    }

    private function changelogChanged(File $file): bool
    {
        $currentChecksum = md5($file->getContents());
        $lastChecksum = $this->getLastChangelogHash();
        if (trim($lastChecksum) !== trim($currentChecksum)) {
            return true;
        }
        return false;
    }

    private function setChangelogChanged(File $file)
    {
        $command = 'git log --oneline -n 1';
        $result = shell_exec($command);
        file_put_contents($this->commitLogFile, trim($result));
        file_put_contents($this->checksumFile, md5($file->getContents()));
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
