<?php

namespace Boscho87\ChangelogChecker\Checkers;

/**
 * Class AscendingVersionChecker
 */
class AscendingVersionChecker extends AbstractChecker
{
    private string $versionPattern = '/## \[(\d+\.\d+\.\d+)\]/';

    protected function check(): void
    {
        preg_match_all($this->versionPattern, $this->file->getContents(), $versionTags);
        $versions = $versionTags[1] ?? [];
        if (!$versions) {
            $this->addErrorMessage('No Version Tags found in file');
        }
        $versions = array_reverse($versions);
        $lastMajorMinorBugfix = [0, 0, 0];
        $lastVersion = '0.0.0';
        foreach ($versions as $version) {
            $currentMajorMinorBugfix = explode('.', $version);
            $currentMajor = $currentMajorMinorBugfix[0];
            $currentMinor = $currentMajorMinorBugfix[1];
            $currentBugfix = $currentMajorMinorBugfix[2];
            $lastMajor = $lastMajorMinorBugfix[0];
            $lastMinor = $lastMajorMinorBugfix[1];
            $lastBugfix = $lastMajorMinorBugfix[2];
            $increased = false;
            if ($currentMajor > $lastMajor) {
                $increased = true;
            }

            if ($currentMajor === $lastMajor && $currentMinor > $lastMinor) {
                $increased = true;
            }

            if ($currentMajor === $lastMajor && $currentMinor === $lastMinor && $currentBugfix > $lastBugfix) {
                $increased = true;
            }

            if (!$increased) {
                foreach ($this->file as $line) {
                    if ($line === sprintf('## [%s]', $version)) {
                        $versionLine = $this->file->lineNumber();
                    }
                    if ($line === sprintf('## [%s]', $lastVersion)) {
                        $lastVersionLine = $this->file->lineNumber();
                    }
                }

                $this->addErrorMessage(sprintf(
                    'Version [%s] (line %s) should be before than [%s] (line %s)',
                    $version,
                    $versionLine ?? 'not found',
                    $lastVersion,
                    $lastVersionLine ?? 'not found'
                ));
            }
            $lastMajorMinorBugfix = $currentMajorMinorBugfix;
            $lastVersion = $version;
        }
    }

    protected function fix(): void
    {
        //can not be savely fixed
    }
}
