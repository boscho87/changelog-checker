<?php

namespace Boscho87\tests;

use Boscho87\ChangelogChecker\FileManager\File;
use Boscho87\ChangelogChecker\FileManager\FileInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class BaseTestCase
 */
class BaseTestCase extends TestCase
{
    protected const CHANGELOG_FILE_PATH = __DIR__ . '/MockFiles/changelog-examples/KeepaChangelog.md';


    protected function getTestMockFile(): FileInterface
    {
        return new File(self::CHANGELOG_FILE_PATH);
    }
}
