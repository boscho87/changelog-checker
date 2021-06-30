<?php

namespace Boscho87\ChangelogChecker\FileManager;

use Boscho87\ChangelogChecker\Exception\FileNotFoundException;

/**
 * Class File
 */
class File implements FileInterface
{
    private string $backupFileSuffix = '.clc.bak';
    private int $holdBackupFiles = 4;
    private string $content;
    private array $lines;
    private int $currentLine = 0;
    private string $filePath;

    /**
     * File constructor.
     */
    public function __construct(string $path)
    {
        try {
            $path = realpath($path);
            $this->filePath = $path;
            $this->content = file_get_contents($path);
            $handle = @fopen($path, "r");
            if ($handle) {
                while (($buffer = fgets($handle)) !== false) {
                    $this->lines[] = trim($buffer);
                }
                if (!feof($handle)) {
                    echo "Fehler: unerwarteter fgets() Fehlschlag\n";
                }
                fclose($handle);
            }
        } catch (\Throwable $throwable) {
            throw new FileNotFoundException(sprintf(
                'File %s not found Error:{%s}',
                $path,
                $throwable->getMessage()
            ));
        }
    }

    public function getContents(): string
    {
        return $this->content;
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function getLine(int $number): string
    {
        return $this->lines[$number];
    }

    public function current()
    {
        return $this->lines[$this->currentLine];
    }

    public function next()
    {
        return $this->lines[++$this->currentLine] ?? null;
    }

    public function key()
    {
        return $this->currentLine;
    }

    public function valid()
    {
        return array_key_exists($this->currentLine, $this->lines);
    }

    public function rewind()
    {
        $this->currentLine = 0;
    }

    /**
     * @return int File line number is not the array index of line!
     */
    public function lineNumber(): int
    {
        return $this->key() + 1;
    }

    public function setLine(string $content, int $key = null): void
    {
        $line = $key ?? $this->lineNumber() - 1;
        $this->lines[$line] = $content;
        $this->content = implode(PHP_EOL, $this->lines);
    }

    public function write(): void
    {
        $lastLine = count($this->lines) - 1;
        $lastLineContent = $this->lines[$lastLine];
        if (!empty($lastLineContent)) {
            $this->setLine('', $lastLine + 1);
        }

        file_put_contents($this->filePath, $this->content);
    }

    public function writeBackup(): void
    {
        $pos = strrpos($this->filePath, '/');
        $filename = substr($this->filePath, $pos + 1);
        $filePath = sprintf(
            '%s/%s.%s-%s',
            getcwd(),
            $this->backupFileSuffix,
            time(),
            $filename
        );

        $this->removeOldBackups();
        file_put_contents($filePath, $this->getContents());
    }


    protected function removeOldBackups(): void
    {
        $backupFiles = glob(sprintf('%s/%s.*', getcwd(), $this->backupFileSuffix));
        $backupFiles = array_reverse($backupFiles);
        $backupFilesCount = count($backupFiles);

        $index = $backupFilesCount - 1;
        while (count($backupFiles) > $this->holdBackupFiles) {
            $index--;
            array_pop($backupFiles);
            unlink($backupFiles[$index]);
        }
    }

    public function setNewContent(string $contents)
    {
        $this->content = $contents;
        $this->lines = explode(PHP_EOL, $contents);
    }

    public function includeLinesAfter(array $lines, int $key = null)
    {
        $line = $key ?? $this->lineNumber();
        array_splice($this->lines, $line, count($lines), $lines);
        $this->content = implode(PHP_EOL, $this->lines);
    }
}
