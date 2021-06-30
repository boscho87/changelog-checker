<?php

namespace Boscho87\ChangelogChecker\Checkers;

use Boscho87\ChangelogChecker\Options\Option;

/**
 * Class DefaultChecker
 */
class DefaultChecker extends AbstractChecker
{
    public function __construct()
    {
        $options = new Option(true, false, true, []);
        parent::__construct($options);
    }

    protected function check(): void
    {
        foreach ($this->file as $line) {
            $replacement = preg_replace('/\s{2}/', ' ', $line);
            if ($line !== $replacement) {
                $this->addErrorMessage(sprintf(
                    '"%s" has > 1 space on line %s',
                    $line,
                    $this->file->lineNumber()
                ));
            }
        }
    }

    protected function fix(): void
    {
        foreach ($this->file as $line) {
            $replacement = preg_replace('/\s{2}/', ' ', $line);
            if ($line !== $replacement) {
                $this->file->setLine($replacement);
                $this->addFixedMessage(sprintf(
                    '"%s" replaced all spaces > 1 on line %s',
                    $line,
                    $this->file->lineNumber()
                ));
            }
        }
    }
}
