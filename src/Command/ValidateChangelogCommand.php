<?php

namespace Boscho87\ChangelogChecker\Command;

use Boscho87\ChangelogChecker\Checkers\Checkable;
use Boscho87\ChangelogChecker\Checkers\FileCheckExecutor;
use Boscho87\ChangelogChecker\FileManager\File;
use Boscho87\ChangelogChecker\Options\OptionLoader;
use Boscho87\ChangelogChecker\Options\OptionResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ValidateChangelogCommand
 */
class ValidateChangelogCommand extends Command
{
    /**
     * ValidateChangelogCommand constructor.
     */
    public function __construct($name = 'clc:validate')
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addOption(
            'file',
            'f',
            InputOption::VALUE_OPTIONAL,
            'Changelog File Path',
            'CHANGELOG.md'
        );
        $this->addOption(
            'config-file',
            'c',
            InputOption::VALUE_OPTIONAL,
            'changelog-checker config file path'
        );
        $this->addOption(
            'with-fix',
            'w',
            InputOption::VALUE_OPTIONAL,
            'override the config fix params'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $filePath = $input->getOption('file');
        $configfile = $input->getOption('config-file');
        $forceFix = $input->getOption('with-fix');
        $changelogPath = realpath($filePath);
        $optionLoader = new OptionLoader($configfile, $forceFix);
        $optionLoader->load(getcwd());
        $optionResolver = new OptionResolver($optionLoader);
        $fileCheckExecutor = new FileCheckExecutor($optionResolver);
        $changelogFile = new File($changelogPath);
        $hasErrors = $fileCheckExecutor->execute($changelogFile);

        $errors = $fileCheckExecutor->getErrors();
        $warnings = $fileCheckExecutor->getWarnings();
        $fixes = $fileCheckExecutor->getFixes();

        if (!empty($errors)) {
            $style->warning(sprintf('Found %d Problems', count($errors)));
            foreach ($errors as $error) {
                $style->text($error);
            }
        }

        if (!empty($warning)) {
            $style->warning(sprintf('Found %d Problems', count($warnings)));
            foreach ($warnings as $warning) {
                $style->text($warning);
            }
        }

        if (!empty($fixes)) {
            $style->success(sprintf('Resolved %d Problems', count($fixes)));
            foreach ($fixes as $fix) {
                $style->text($fix);
            }
        }

        if (!empty($fileCheckExecutor->getFixes())) {
            $changelogFile->write();
        }


        if ($hasErrors) {
            $style->error('Your Changelog looks creepy');
            return Command::FAILURE;
        }

        $style->success('Your Changelog looks neat');

        return Command::SUCCESS;
    }
}
