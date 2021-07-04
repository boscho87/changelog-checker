<?php

namespace Boscho87\ChangelogChecker\Checkers;

/**
 * Class TypeChecker
 */
class TypeChecker extends AbstractChecker
{
    //Todo move this to mor generic place
    public static array $allowedTypes = ['Added', 'Changed', 'Deprecated', 'Removed', 'Fixed', 'Security'];


    protected function check(): void
    {
        foreach ($this->file as $line) {
            if ($this->isTypeLine($line)) {
                preg_match($this->getAllowedTypePattern(), $line, $matches);
                $typeString = $matches[2] ?? '';
                if (!in_array($typeString, self::$allowedTypes)) {
                    $this->addErrorMessage(sprintf(
                        'Line %s has invalid Type: "%s", allowed are %s',
                        $this->file->lineNumber(),
                        $line,
                        implode(',', self::$allowedTypes)
                    ));
                }
            }
        }
    }

    protected function fix(): void
    {
        //no fix possible for this case
    }


    private function getAllowedTypePattern(): string
    {
        $types = implode('|', self::$allowedTypes);
        return sprintf('/(###\s)(%s)$/', $types);
    }
}
