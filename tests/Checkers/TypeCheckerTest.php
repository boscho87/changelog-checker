<?php

namespace Boscho87\tests\Checkers;

use Boscho87\ChangelogChecker\Checkers\TypeChecker;
use Boscho87\ChangelogChecker\Options\Option;

/**
 * Class TypeCheckerTest
 */
class TypeCheckerTest extends AbstractTypeCheckerTest
{
    /**
     * @group unit
     */
    public function testIfCheckOnlyAllowsValidTypes(): void
    {
        $typeChecker = new TypeChecker(new Option(true, true, true));
        $file = $this->getTestMockFile();
        $typeChecker->execute($file);
        $this->assertEmpty($typeChecker->getErrors());
        $file->includeLinesAfter([
            '### ADded',
            '### Changed',
            '### Deprecated',
            '### Removed',
            '### Flixed',
            '### Security',
        ]);
        $typeChecker->execute($file);
        $firstError = $typeChecker->getErrors()[0];
        $secondError = $typeChecker->getErrors()[1];
        $this->assertEquals('Line 2 has invalid Type: "### ADded", allowed are Added,Changed,Deprecated,Removed,Fixed,Security', $secondError);
        $this->assertEquals('Line 6 has invalid Type: "### Flixed", allowed are Added,Changed,Deprecated,Removed,Fixed,Security', $firstError);
    }
}
