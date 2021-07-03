<?php

namespace Boscho87\ChangelogChecker\Command;

use Boscho87\ChangelogChecker\FileManager\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Todo refactor and extract Classes and methods
 * Class CreateReleaseCommand
 */
class CreateReleaseCommand extends Command
{
    /**
     * ValidateChangelogCommand constructor.
     */
    public function __construct($name = 'release')
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addOption(
            'changelog-file',
            'f',
            InputOption::VALUE_OPTIONAL,
            'Changelog File Path',
            'CHANGELOG.md'
        );
        $this->addOption(
            'composer-file',
            'c',
            InputOption::VALUE_OPTIONAL,
            'Composer File Path',
            'composer.json'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $changelogPath = realpath($input->getOption('changelog-file'));
        $composerFile = realpath($input->getOption('composer-file'));
        $changelogFile = new File($changelogPath);
        $changelogFile->writeBackup();
        preg_match_all('/\[(\d+\.\d+\.\d+)\]\s/', $changelogFile->getContents(), $versions);
        $lastVersion = $versions[1][0] ?? null;
        $majorMinorBugfix = explode('.', $lastVersion);
        $major = $majorMinorBugfix[0];
        $minor = $majorMinorBugfix[1] + 1;
        $bugfix = $majorMinorBugfix[2];
        $release = sprintf('%d.%d.%d', $major, $minor, $bugfix);


        foreach ($changelogFile as $line) {
            if (strpos($line, '[Unreleased]')) {
                $lineAfterUnreleased = $changelogFile->getLine($changelogFile->lineNumber());
                if (empty($lineAfterUnreleased)) {
                    //todo check specific for One of the allowed verbs -> KeepAChangelog (Added,Fixed.....)
                    throw new \Exception('invalid Changelog format, the unreleased section is empty (or the line underneath is missing)');
                }
                $date = date('Y-m-d');
                $changelogFile->includeLinesAfter(['', sprintf('## [%s] - %s', $release, $date)]);
                break;
            }
        }

        if ($composerFile) {
            $composerContent = file_get_contents($composerFile);
            $replaced = preg_replace('/"version":"\d+\.\d+\.\d+"/', sprintf('"version": "%s"', $release), $composerContent);
            if ($composerContent !== $replaced) {
                file_put_contents($composerFile, $replaced);
                $style->info('Updated composer File');
            } else {
                $style->info('Version in comopser.json not found (absolutely normal if you not implement open Source Libraries or something like that');
            }
        }


        foreach ($changelogFile as $line) {
            if (strpos($line, '[Unreleased]: ') !== false) {
                $updatedUnreleasedLink = str_replace($lastVersion . '...', $release . '...', $line);
                $changelogFile->setLine($updatedUnreleasedLink);
                continue;
            }
            if (strpos($line, sprintf('[%s]: ', $lastVersion)) !== false) {
                $oldLine = $line;
                $changelogFile->includeLinesAfter([$oldLine]);
                $line = str_replace($lastVersion, $release, $line);
                $line = preg_replace('/\d+\.\d+\.\d+\.\.\./', $lastVersion . '...', $line);
                $changelogFile->setLine($line);
                break;
            }
        }


        $changelogFile->write();

        if (shell_exec('which git')) {
            $commands = [
                'git add .',
                sprintf('git commit -m "Release: %s"', $release),
                sprintf('git tag %s', $release),
            ];
            foreach ($commands as $command) {
                $result = shell_exec($command);
                $style->newLine();
                $style->info(sprintf('execute: %s, result: %s', $command, $result));
            }
        } else {
            $style->info('Did not Create git tags, git missing?');
        }


        $style->success(sprintf(
            'Created new Release %s',
            $release
        ));
        return Command::SUCCESS;
    }
}
