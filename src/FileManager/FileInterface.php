<?php

namespace Boscho87\ChangelogChecker\FileManager;

/**
 * Interface FileInterface
 */
interface FileInterface extends \Iterator
{
    public function getContents(): string;

    public function setNewContents(string $content): void;

    public function getLines(): array;

    public function getLine(int $number): string;

    public function lineNumber(): int;

    public function setLine(string $replaced, int $key);
}
