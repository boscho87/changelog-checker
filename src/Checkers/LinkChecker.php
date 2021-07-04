<?php

namespace Boscho87\ChangelogChecker\Checkers;

/**
 * Class LinkChecker
 */
class LinkChecker extends AbstractChecker
{
    protected function check(): void
    {
        $versions = [];
        $versionTags = $this->mutationManager->getAllTitleVersionTags();
        foreach ($versionTags as $tag) {
            if (!in_array($tag, $versions)) {
                $pattern = preg_replace('/\[/', '\[', $tag);
                $pattern = preg_replace('/\]/', '\]', $pattern);
                $pattern = preg_replace('/\./', '\.', $pattern);
                $pattern = preg_replace('/\s/', '', $pattern);
                $pattern = sprintf('/(%s)\: (https:\/\/)/', $pattern);
                preg_match($pattern, $this->file->getContents(), $link);
                if (!array_key_exists(0, $link)) {
                    $this->addErrorMessage(sprintf(
                        'Version link for %s is missing at the end of the file',
                        $tag
                    ));
                }
                $versions[] = $tag;
            }
        }
    }

    /**
     * //implement test and method (TDD)
     * @codeCoverageIgnore
     */
    protected function fix(): void
    {
        // This can be fixed but is a bit complicated
        // we can fetch the git remote url with shell exec and then create the url's
        /**
         * [Unreleased]: https://github.com/olivierlacan/keep-a-changelog/compare/v1.0.0...HEAD
        [1.0.0]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.3.0...v1.0.0
        [0.3.0]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.2.0...v0.3.0
        [0.2.0]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.1.0...v0.2.0
        [0.1.0]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.0.8...v0.1.0
        [0.0.8]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.0.7...v0.0.8
        [0.0.7]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.0.6...v0.0.7
        [0.0.6]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.0.5...v0.0.6
        [0.0.5]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.0.4...v0.0.5
        [0.0.4]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.0.3...v0.0.4
        [0.0.3]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.0.2...v0.0.3
        [0.0.2]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.0.1...v0.0.2
        [0.0.1]: https://github.com/olivierlacan/keep-a-changelog/releases/tag/v0.0.1
         */
        // TODO: Implement fix() method.
    }
}
