<?php

namespace Boscho87\ChangelogChecker\Changelog;

use Boscho87\ChangelogChecker\Checkers\TypeChecker;
use Boscho87\ChangelogChecker\Exception\InvalidChangeTypeException;
use Boscho87\ChangelogChecker\FileManager\FileInterface;

/**
 * Class MutationManager
 */
class MutationManager
{
    private FileInterface $changelogFile;
    public const MAJOR = 0;
    public const MINOR = 1;
    public const BUGFIX = 2;
    public const UNRELEASED_VERSION_TAG = '## [Unreleased]';
    public const UNRELEASED_VERSION_LINK_TAG = '[Unreleased]: ';


    /**
     * MutationManager constructor.
     */
    public function __construct(FileInterface $changelogFile)
    {
        $this->changelogFile = $changelogFile;
    }


    public function getAllTitleVersionTags(): array
    {
        preg_match_all(Regex::VERSION_TITLE_PATTERN, $this->changelogFile->getContents(), $versionTags);
        if (!is_array($versionTags) || !array_key_exists(1, $versionTags)) {
            return [];
        }
        return array_reverse($versionTags[1]);
    }


    public function getLastVersion(): ?string
    {
        preg_match_all(Regex::VERSION_TITLE_PATTERN, $this->changelogFile->getContents(), $versions);
        return $versions[1][0] ?? null;
    }

    public function getIncreasedVersionNumber(string $versionType = self::MINOR): string
    {
        $lastVersion = $this->getLastVersion();
        $majorMinorBugfix = explode('.', $lastVersion);
        $majorMinorBugfix[$versionType]++;
        $major = $majorMinorBugfix[0];
        $minor = $majorMinorBugfix[1];
        $bugfix = $majorMinorBugfix[2];
        return sprintf('%d.%d.%d', $major, $minor, $bugfix);
    }

    public function moveUnreleasedToNewVersion(string $newVersion): void
    {
        foreach ($this->changelogFile as $line) {
            if (strpos($line, self::UNRELEASED_VERSION_TAG) !== false) {
                $lineAfterUnreleased = $this->changelogFile->getLine($this->changelogFile->lineNumber());
                if (empty($lineAfterUnreleased)) {
                    $allowed = implode(',', TypeChecker::$allowedTypes);
                    throw new InvalidChangeTypeException(sprintf(
                        'The line after Unreleased should be one of this types (with ### prefixed): %s',
                        $allowed
                    ));
                }
                $date = date('Y-m-d');
                $this->changelogFile->includeLinesAfter(['', sprintf('## [%s] - %s', $newVersion, $date)]);
                break;
            }
        }
    }

    public function updateVersionLinks(string $lastVersion, string $newVersion): void
    {
        $this->changelogFile->rewind();
        ;
        foreach ($this->changelogFile as $line) {
            if (strpos($line, self::UNRELEASED_VERSION_LINK_TAG) !== false) {
                $updatedUnreleasedLink = str_replace($lastVersion . '...', $newVersion . '...', $line);
                $this->changelogFile->setLine($updatedUnreleasedLink);
                continue;
            }
            if (strpos($line, sprintf('[%s]: ', $lastVersion)) !== false) {
                $oldLine = $line;
                $this->changelogFile->includeLinesAfter([$oldLine]);
                $line = str_replace($lastVersion, $newVersion, $line);
                $line = preg_replace(Regex::HREF_START_VERSION, $lastVersion . '...', $line);
                $this->changelogFile->setLine($line);
                break;
            }
        }
    }
}
