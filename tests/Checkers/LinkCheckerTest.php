<?php

namespace Boscho87\tests\Checkers;

use Boscho87\ChangelogChecker\Checkers\LinkChecker;
use Boscho87\ChangelogChecker\Options\Option;
use Boscho87\tests\BaseTestCase;

/**
 * Class LinkCheckerTest
 */
class LinkCheckerTest extends AbstractTypeCheckerTest
{
    /**
     * @group unit
     */
    public function testIfCheckDoesAddErrorsOnMissingLinkLines(): void
    {
        $file = $this->getTestMockFile();
        $linkChecker = new LinkChecker(new Option(true, true, false));
        $linkChecker->execute($file);
        $this->assertEmpty($linkChecker->getErrors());
        $file->setNewContent('');
        $linkChecker->execute($file);
        $this->assertEmpty($linkChecker->getErrors());
        $file->includeLinesAfter([
            '## [Unreleased]',
            '',
            '## [1.1.0] - 2020-06-23',
            '### Added',
            '- Test ',
            '',
            '## [1.0.0] - 2020-06-22',
            '### Changed',
            '- Changed the File for the test',

            '### Added',
            '- Added new Line',
            '',
            '[Unreleased] https://githublink.com/compare/1.0.0...HEAD',
            '[1.0.0]: https://githublink',
        ]);
        $linkChecker->execute($file);
        $expectedError = ['Version link for [1.1.0] is missing at the end of the file'];
        $this->assertEquals($expectedError, $linkChecker->getErrors());
    }
}
