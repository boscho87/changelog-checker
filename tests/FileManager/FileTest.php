<?php

namespace Boscho87\tests\FileManager;

use Boscho87\ChangelogChecker\FileManager\File;
use Boscho87\tests\BaseTestCase;

/**
 * Class FileTest
 */
class FileTest extends BaseTestCase
{
    private string $tempTestFilePath;
    private string $keepAChangelogPath = __DIR__ . '/../MockFiles/changelog-examples/KeepaChangelog.md';

    /**
     * @group unit
     */
    public function testIfFileCanBeLoaded(): void
    {
        $file = new File($this->keepAChangelogPath);
        $lineOne = $file->getLine(1);
        $this->assertEquals($file->next(), $lineOne);
    }

    /**
     * @group  unit
     */
    public function testIfNewContentCanBeSet(): void
    {
        $file = new File($this->keepAChangelogPath);
        $content = sprintf('Cow jumping over the moon%salso on line two', PHP_EOL);
        $file->setNewContent($content);
        $this->assertEquals($file->getContents(), $content);
        $this->assertEquals('also on line two', $file->getLine(1));
        $this->assertEquals('Cow jumping over the moon', $file->current());
    }

    /**
     * @group  unit
     */
    public function testIfIteratorIterates(): void
    {
        $file = new File($this->keepAChangelogPath);

        $this->assertEquals(0, $file->key());
        $file->next();
        $file->next();
        $this->assertEquals(2, $file->key());
        $this->assertEquals(3, $file->lineNumber());
        $this->assertTrue($file->valid());
        $file->rewind();
        $this->assertEquals(0, $file->key());
        foreach ($file as $line) {
        }
        $this->assertFalse($file->valid());
    }

    /**
     * @group unit
     */
    public function testIfFileCanBeWritten(): void
    {
        $newContent = 'New FileContent';
        $file = new File($this->tempTestFilePath);
        $file->setNewContent($newContent);
        $file->write();
        $this->assertEquals('New FileContent', trim($file->getContents()));
    }

    /**
     * @group unit
     */
    public function testIfBackupCanBeWritten(): void
    {
        $file = new File($this->tempTestFilePath);
        $path = $file->writeBackup();
        $this->assertEquals(file_get_contents($path), $file->getContents());
        unlink($path);
    }

    /**
     * @group unit
     */
    public function testIfFileHashCanBeGetFromFile(): void
    {
        $file = new File($this->tempTestFilePath);
        $hash = md5($file->getContents());
        $this->assertEquals($hash, $file->getHash());
    }


    public function setUp(): void
    {
        $testContent = sprintf(
            'This is a multiline tests file %s with two lines %s or three lines %s',
            PHP_EOL,
            PHP_EOL,
            PHP_EOL
        );
        $this->tempTestFilePath = sys_get_temp_dir() . '/test' . time();
        file_put_contents($this->tempTestFilePath, $testContent);
        parent::setUp();
    }

    /**
     * @group unit
     */
    public function testToIncludeLinesAfterASpecificLine(): void
    {
        $file = new File($this->keepAChangelogPath);
        $lineOneZeroThree = '- Markdown links to version tags on release headings.';
        $this->assertEquals($lineOneZeroThree, $file->getLine(102));
        $file->includeLinesAfter(['- This Changelog Message'], 102);
        $this->assertEquals($lineOneZeroThree, $file->getLine(103));
        $tempDir = sys_get_temp_dir() . '/test.file';
        $file->write($tempDir);
        $this->assertEquals($file->getContents(), file_get_contents($tempDir));
        unlink($tempDir);
    }

    public function tearDown(): void
    {
        unlink($this->tempTestFilePath);
        parent::tearDown();
    }
}
