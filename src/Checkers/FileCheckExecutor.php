<?php

namespace Boscho87\ChangelogChecker\Checkers;

use Boscho87\ChangelogChecker\Exception\InvalidInstanceException;
use Boscho87\ChangelogChecker\Exception\InvalidOptionsException;
use Boscho87\ChangelogChecker\FileManager\FileInterface;
use Boscho87\ChangelogChecker\Options\OptionResolver;

/**
 * @codeCoverageIgnore
 * Class FileCheckExecutor
 */
class FileCheckExecutor
{
    /**
     * @var AbstractChecker[]
     */
    private array $checkers;
    private array $warnings = [];
    private array $errors = [];
    private array $fixes = [];

    /**
     * FileCheckExecutor constructor.
     */
    public function __construct(OptionResolver $resolver)
    {
        $this->setCheckers($resolver->getCheckers());
    }

    public function execute(FileInterface $file): bool
    {
        if (empty($this->checkers)) {
            throw new InvalidOptionsException('with your configured options, no checks are executed');
        }

        foreach ($this->checkers as $checker) {
            $checker->execute($file);
            $this->fixes = array_merge($this->fixes, $checker->getFixed());
            $this->warnings = array_merge($this->warnings, $checker->getWarnings());
            $this->errors = array_merge($this->errors, $checker->getErrors());
        }


        return count($this->errors);
    }

    private function setCheckers(array $checkers)
    {
        foreach ($checkers as $checker) {
            if (!$checker instanceof AbstractChecker) {
                throw new InvalidInstanceException(
                    sprintf('checker must be instanceof %s', AbstractChecker::class)
                );
            }
            $this->checkers[] = $checker;
        }
    }


    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFixes(): array
    {
        return $this->fixes;
    }
}
