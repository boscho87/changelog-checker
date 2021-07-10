<?php

namespace Boscho87\tests\Checkers;

use Boscho87\ChangelogChecker\Checkers\AscendingVersionChecker;

/**
 * Class AscendingVersionCheckerTest
 */
class AscendingVersionCheckerTest extends AbstractCheckerTest
{
    /**
     * @group unit
     */
    public function testIfCheckerDoNotComplainIfNumberIsIncreasing(): void
    {
        $checker = new AscendingVersionChecker($this->checkFixerErrorOption());
        $file = $this->getTestMockFile();
        $file->setNewContent('');
        $file->includeLinesAfter([
            '## [10.11.12]',
            '',
            '## [7.8.9]',
            '',
            '## [4.5.6]',
            '',
            '## [1.2.3]',
        ]);
        $checker->execute($file);
        $this->assertEmpty($checker->getErrors());
    }

    /**
     * @group unit
     */
    public function testIfCheckerDoComplainIfNumberIsNotIncreasing(): void
    {
        $checker = new AscendingVersionChecker($this->checkFixerErrorOption());
        $file = $this->getTestMockFile();
        $file->setNewContent('');
        $file->includeLinesAfter([
            '## [1.2.3]',
            '',
            '## [4.5.6]',
            '',
            '## [7.8.9]',
            '',
            '## [10.11.12]',
        ]);
        $checker->execute($file);
        $this->assertNotEmpty($checker->getErrors());
    }
}
