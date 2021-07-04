<?php

namespace Boscho87\tests\Checkers;

use Boscho87\ChangelogChecker\Checkers\DefaultChecker;
use Boscho87\ChangelogChecker\Options\Option;

/**
 * Class DefaultCheckerTest
 */
class DefaultCheckerTest extends AbstractCheckerTest
{
    /**
     * @group unit
     */
    public function testIfDefaultCheckerSetErrors(): void
    {
        $file = $this->getTestMockFile();
        $defaultChecker = new DefaultChecker(new Option(true, false, true));
        $defaultChecker->execute($file);
        $this->assertEmpty($defaultChecker->getWarnings());
        $file->includeLinesAfter([
            '## [1.0.0] - 2021-06-28',
            '### Added',
            '- Added new  Content',
            '',
            '',
            '## 1.2.0 -  2021-06-25',
            '###  Added',
            '- Added  two empty lines to make  the test fail'
        ]);
        $defaultChecker->execute($file);
        $this->assertEquals('"- Added  two empty lines to make  the test fail" has > 1 space on line 9', $defaultChecker->getWarnings()[0]);
        $this->assertEquals('"- Added new  Content" has > 1 space on line 4', $defaultChecker->getWarnings()[1]);
        $this->assertEquals('There should never been more than one linebreak', $defaultChecker->getWarnings()[2]);
    }
}
