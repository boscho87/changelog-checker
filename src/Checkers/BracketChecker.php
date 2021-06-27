<?php

namespace Boscho87\ChangelogChecker\Checkers;

/**
 * Class BracketChecker
 */
class BracketChecker extends AbstractChecker
{
    private string $missingBracketsPattern = '/(\s)(\d+\.\d+\.\d+)(\s)/';
    private string $hasBracketsPattern = '/\[\d+\.\d+\.\d+\]/';
    private string $replacement = ' [$2] ';


    protected function check(): void
    {
        foreach ($this->file as $line) {
            if ($this->isVersionLine($line)) {
                preg_match($this->missingBracketsPattern, $line, $missingBrackets);
                preg_match($this->hasBracketsPattern, $line, $brackets);
                if (array_key_exists(2, $missingBrackets)) {
                    $this->addErrorMessage(
                        sprintf(
                            'Version %s has missing brackets - line {%s}',
                            $missingBrackets[2],
                            $this->file->lineNumber()
                        )
                    );
                }
            }
        }
    }

    protected function fix(): string
    {
        foreach ($this->file as $line) {
            if ($this->isVersionLine($line)) {
                $replaced = preg_replace($this->missingBracketsPattern, $this->replacement, $line);
                $this->file->setLine($replaced, $this->file->key());
                if ($line !== $replaced) {
                    $this->addFixedMessage(sprintf(
                        'Fixed "%s" and added brackets - line {%s}',
                        $line,
                        $this->file->lineNumber()
                    ));
                }
            }
        }

        return $this->file->getContents();
    }


    protected function isVersionLine($line): bool
    {
        return substr($line, 0, 3) === '## ';
    }
}
