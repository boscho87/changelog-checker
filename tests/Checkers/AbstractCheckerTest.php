<?php

namespace Boscho87\tests\Checkers;

use Boscho87\ChangelogChecker\Options\Option;
use Boscho87\tests\BaseTestCase;

/**
 * Class AbstractCheckerTest
 */
abstract class AbstractCheckerTest extends BaseTestCase
{
    protected function checkFixerErrorOption(array $moreOptions = []): Option
    {
        return new Option(true, true, true, $moreOptions);
    }
}
