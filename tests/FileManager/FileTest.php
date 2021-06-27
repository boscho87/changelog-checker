<?php

namespace Boscho87\tests\FileManager;

use Boscho87\ChangelogChecker\FileManager\File;
use Boscho87\tests\BaseTestCase;

/**
 * Class FileTest
 */
class FileTest extends BaseTestCase
{
    public function testIfFileCanBeLoaded()
    {
        $file = new File(__DIR__ . '/../MockFiles/changelog-examples/KeepaChangelog.md');
        $content = $file->getContents();
        $lineOne = $file->getLine(1);
        $this->assertEquals($file->next(), $lineOne);
    }
}
