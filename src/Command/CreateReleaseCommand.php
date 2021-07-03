<?php

namespace Boscho87\ChangelogChecker\Command;

use Boscho87\ChangelogChecker\Changelog\MutationManager;
use Boscho87\ChangelogChecker\Composer\ComposerMutationManager;
use Boscho87\ChangelogChecker\FileManager\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Todo refactor and extract Classes and methods
 * @codeCoverageIgnore
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
        $this->addArgument(
            'commit',
            null,
            'should the command create a git tag, after the changelog is up to date',
            false
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $changelogPath = realpath($input->getOption('changelog-file'));
        $composerFile = realpath($input->getOption('composer-file'));
        $shouldCommit = $input->getArgument('commit');
        $changelogFile = new File($changelogPath);
        $mutationManager = new MutationManager($changelogFile);
        $composerMutationManager = new ComposerMutationManager();
        $changelogFile->writeBackup();
        $newVersion = $mutationManager->getIncreasedVersionNumber();
        $lastVersion = $mutationManager->getLastVersion();


        $mutationManager->moveUnreleasedToNewVersion($newVersion);


        if ($composerFile && $composerMutationManager->replaceComposerVersion($composerFile, $newVersion)) {
            $style->info('Updated composer.json File version');
        }

        $mutationManager->updateVersionLinks($lastVersion, $newVersion);
        $changelogFile->write();

        $hasGit = shell_exec('which git');
        if (!$hasGit) {
            $style->info('Did not Create git tags, git missing?');
        }

        if ($shouldCommit && !$hasGit) {
            $commands = [
                'git add .',
                sprintf('git commit -m "Release: %s"', $newVersion),
                sprintf('git tag %s', $newVersion),
            ];
            foreach ($commands as $command) {
                $result = shell_exec($command);
                $style->newLine();
                $style->info(sprintf('execute: %s, result: %s', $command, $result));
            }
        }


        $style->success(sprintf(
            'Created new Release %s',
            $newVersion
        ));
        return Command::SUCCESS;
    }
}
