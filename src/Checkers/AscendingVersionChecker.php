<?php

namespace Boscho87\ChangelogChecker\Checkers;

/**
 * Class AscendingVersionChecker
 */
class AscendingVersionChecker extends AbstractChecker
{
    private string $versionPattern = '/\d+\.\d+\.\d+/';

    protected function check(): void
    {
        $lastRawVersion = 999;
        $lastVersion = '999.0.0';
        $lastLine = 0;
        foreach ($this->file as $line) {
            if ($this->isVersionLine($line)) {
                preg_match($this->versionPattern, $line, $matches);
                $currentLine = $this->file->lineNumber();
                if (!empty($matches)) {
                    $currentVersion = $matches[0];
                    $currentRawVersion = (int)str_replace('.', '', $currentVersion);
                    if ($lastRawVersion <= $currentRawVersion) {
                        $this->addErrorMessage(sprintf(
                            'Version %s  on line %d  should be > than %s on line %d',
                            $lastVersion,
                            $lastLine,
                            $currentVersion,
                            $currentLine,
                        ));
                    }
                    $lastVersion = $currentVersion;
                    $lastRawVersion = $currentRawVersion;
                    $lastLine = $currentLine;
                }
            }
        }
    }

    protected function fix(): void
    {
        // TODO: Implement fix() method.
    }
}
