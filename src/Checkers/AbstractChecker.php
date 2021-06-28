<?php

namespace Boscho87\ChangelogChecker\Checkers;

use Boscho87\ChangelogChecker\FileManager\FileInterface;
use Boscho87\ChangelogChecker\Options\Option;

/**
 * Class AbstractChecker
 */
abstract class AbstractChecker
{
    protected FileInterface $file;
    private array $warnings = [];
    private array $errors = [];
    private array $fixed = [];
    private Option $options;

    /**
     * AbstractChecker constructor.
     */
    public function __construct(Option $options)
    {
        $this->options = $options;
    }

    public function execute(FileInterface $file): void
    {
        $this->file = $file;
        if ($this->options->isCheck()) {
            $this->check();
            $this->file->rewind();
        }
        if ($this->options->isFix()) {
            $this->fix();
            $this->file->rewind();
        }
    }

    protected function addErrorMessage(string $message): void
    {
        if ($this->options->isError()) {
            $this->errors[] = $message;
            return;
        }
        $this->warnings[] = $message;
    }

    protected function addFixedMessage(string $message): void
    {
        $this->fixed[] = $message;
    }

    abstract protected function check(): void;

    abstract protected function fix(): void;


    public function getWarnings(): array
    {
        return array_reverse($this->warnings);
    }

    public function getErrors(): array
    {
        return array_reverse($this->errors);
    }

    public function getFixed(): array
    {
        return array_reverse($this->fixed);
    }

    protected function isVersionLine(string $line): bool
    {
        return substr($line, 0, 3) === '## ';
    }
}
