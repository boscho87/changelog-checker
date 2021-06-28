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
    private int $failAfterXCommits = 4;

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
        $command = sprintf('git log --oneline -n %d', $this->failAfterXCommits);
        $result = shell_exec($command);
        $lines = array_filter(explode(PHP_EOL, $result));
        $newestCommit = $lines[0] ?? '';
        $commitLog = $this->getCommitLog();
        $position = strpos($commitLog, $newestCommit);

        if (!$this->changelogChanged()) {
            if ($position === false) {
                $this->addErrorMessage(
                    sprintf('Changelog not modified since %d commits', $this->failAfterXCommits)
                );
                return;
            }
            file_put_contents($this->commitLogFile, implode(PHP_EOL, $lines));
            return;
        }

        file_put_contents($this->checksumFile, md5($this->file->getContents()));
    }

    protected function fix(): void
    {
        // TODO: Implement fix() method.
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
}
