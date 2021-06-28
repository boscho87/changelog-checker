<?php

namespace Boscho87\ChangelogChecker\Checkers;

/**
 * Class TypeChecker
 */
class TypeChecker extends AbstractChecker
{
    private array $allowedTypes = ['Added', 'Changed', 'Deprecated', 'Removed', 'Fixed', 'Security'];


    protected function check(): void
    {
        foreach ($this->file as $line) {
            if ($this->isTypeLine($line)) {
                preg_match($this->getAllowedTypePattern(), $line, $matches);
                $typeString = $matches[2] ?? '';
                if (!in_array($typeString, $this->allowedTypes)) {
                    $this->addErrorMessage(sprintf(
                        'Line %s has invalid Type: "%s", allowed are %s',
                        $this->file->lineNumber(),
                        $line,
                        implode(',', $this->allowedTypes)
                    ));
                }
            }
        }
    }

    protected function fix(): void
    {
        // TODO: Implement fix() method.
    }


    private function getAllowedTypePattern(): string
    {
        $types = implode('|', $this->allowedTypes);
        return sprintf('/(###\s)(%s)$/', $types);
    }
}
