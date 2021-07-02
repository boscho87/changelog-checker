<?php

namespace Boscho87\ChangelogChecker\Command;

use Boscho87\ChangelogChecker\FileManager\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
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
        $changelogPath = realpath($input->getOption('file'));
        $changelogFile = new File($changelogPath);
        $changelogFile->writeBackup();

        //get last version from the changelog
        //increase it by one minor step
        // move all unreleased entries to the new version section
        //modify the unreleased link (get current base git url)
        // add the new version link
        //set version to composer.json
        //git commit with release message
        //git create tag
    }
}
