<?php

namespace Boscho87\ChangelogChecker\Checkers;

use Boscho87\ChangelogChecker\Changelog\Regex;

/**
 * Class TypeChecker
 */
class TypeChecker extends AbstractChecker
{
    protected function check(): void
    {
        foreach ($this->file as $line) {
            if ($this->isTypeLine($line)) {
                preg_match(Regex::allowedTypesPattern(), $line, $matches);
                $typeString = $matches[2] ?? '';
                if (!in_array($typeString, Regex::$allowedTypes)) {
                    $this->addErrorMessage(sprintf(
                        'Line %s has invalid Type: "%s", allowed are %s',
                        $this->file->lineNumber(),
                        $line,
                        implode(',', Regex::$allowedTypes)
                    ));
                }
            }
        }
    }

    protected function fix(): void
    {
        //no fix possible for this case
    }
}
