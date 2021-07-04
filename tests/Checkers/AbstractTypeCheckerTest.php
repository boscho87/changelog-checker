<?php

namespace Boscho87\tests\Checkers;

use Boscho87\ChangelogChecker\FileManager\File;
use Boscho87\ChangelogChecker\FileManager\FileInterface;
use Boscho87\tests\BaseTestCase;

/**
 * Class AbstractTypeCheckerTest
 */
class AbstractTypeCheckerTest extends BaseTestCase
{
    protected const CHANGELOG_FILE_PATH = __DIR__ . '/../MockFiles/changelog-examples/KeepaChangelog.md';


    protected function getTestMockFile(): FileInterface
    {
        return new File(self::CHANGELOG_FILE_PATH);
    }
}
