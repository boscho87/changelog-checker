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
        $contents = preg_replace('/\n{2,}/', '', $this->file->getContents());
        if ($contents !== $this->file->getContents()) {
            $this->addErrorMessage('There should never been more than one linebreak');
        }

        foreach ($this->file as $line) {
            $replacement = preg_replace('/\s{2,}/', ' ', $line);
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
        $contents = preg_replace('/\n{2,}/', '', $this->file->getContents());
        var_dump($contents);
        if ($contents !== $this->file->getContents()) {
            //    $this->file->setNewContent($contents);
            $this->addFixedMessage('Replaced all double line breaks with only one');
        }

        foreach ($this->file as $line) {
            $replacement = preg_replace('/\s{2,}/', ' ', $line);
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
