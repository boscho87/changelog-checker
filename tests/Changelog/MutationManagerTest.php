<?php

namespace Boscho87\tests\Changelog;

use Boscho87\ChangelogChecker\Changelog\MutationManager;
use Boscho87\tests\BaseTestCase;

/**
 * Class MutationManagerTest
 */
class MutationManagerTest extends BaseTestCase
{
    /**
     * @dataProvider versionNumbers
     */
    public function testIfVersionNumberCanBeIncreased(array $lines, int $increaseType, string $expectedVersion, string $expectedNextVersion)
    {
        $file = $this->getTestMockFile();
        $file->includeLinesAfter($lines);

        $mm = new MutationManager($file);
        $version = $mm->getLastVersion();
        $this->assertEquals($expectedVersion, $version);
        $nextVersion = $mm->getIncreasedVersionNumber($increaseType);
        $this->assertEquals($expectedNextVersion, $nextVersion);
    }

    public function versionNumbers(): array
    {
        return [
            [
                [
                    '[3.1.1]',
                    '### Added',
                    '- Empty',
                ],
                MutationManager::MAJOR,
                '3.1.1',
                '4.0.0',
            ],
            [
                [
                    '[2.1.1]',
                    '### Added',
                    '- Empty',
                ],
                MutationManager::MINOR,
                '2.1.1',
                '2.2.0',
            ],
            [
                [
                    '[16.41.11]',
                    '### Added',
                    '- Empty',
                ],
                MutationManager::BUGFIX,
                '16.41.11',
                '16.41.12',
            ],
        ];
    }
}
