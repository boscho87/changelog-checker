# Changelog Checker &copy; Boscho87

Check if the Changelog has proper format, and if it was updated since the last commit. Some problems can be fixed by the
tool if you want (see more in configs)

On Every command it will create a Changelog Backup for you. It will rotate 4 Backlog files.

Follows the Guiltiness from [Keep A Changelog](https://keepachangelog.com/en/1.1.0/)

This tool should only be used in development environments.

#### Requirements

- \> php7.4
- some checks and actions requires git

### Installation

Best practice: Install in a subdirectory to avoid dependency Problems

```shell
mkdir --parents tools/changelog-checker
composer require --working-dir=tools/changelog-checker boscho87/changelog-checker
## ignore backup files
echo .gitignore >> .clc.*
echo .gitignore >> tools/changelog-checker/vendor
```

### Execute the Commands

```shell
### With default config (or own config file) if your Changelog name is CHANGELOG.md
/tools/changelog-checker/vendor/bin/changelog-checker clc:validate
### Define the Changelog File
/tools/changelog-checker/vendor/bin/changelog-checker clc:validate -f CHANGELOG.md
### Define the config File
/tools/changelog-checker/vendor/bin/changelog-checker clc:validate -c _clc.php
### Force (overwrite the configs) to not only Check, but to resolve the Problems
/tools/changelog-checker/vendor/bin/changelog-checker clc:validate --with-fix=1
### Force (overwrite the configs) to not Fix the Problems
/tools/changelog-checker/vendor/bin/changelog-checker clc:validate --with-fix=0
### Create a new Release (from the Unreleased Section) - ALPHA FEATURE
/tools/changelog-checker/vendor/bin/changelog-checker release
```

### Config

Create a file named `{project-root}/_clc.php` to Overwrite the default configs

> Be Aware that some options depends on some kind of Correctness of the Changelog
E.g. if there is no [Unreleased] tag, the Increased Checker can not add commit messages underneath it.
but the AscendingVersionChecker can resolve this and add an Unreleased Tag, so you should turn fix to true.
```php
<?php
//if you leave something empty, the default will be used
return [
    'ascendingVersion' => [
        'check' => true,
        'error' => true,
        'fix' => false,
    ],
    'versionBrackets' => [
        'check' => true,
        'error' => true,
        'fix' => false,
    ],
    'actions' => [
        'check' => true,
        'error' => true,
        'fix' => false,
    ],
    'increased' => [
        'check' => false,
        'error' => true,
        'fix' => false,
        //commits allowed without a changelog change
        'fail_after' => 1,
    ],
    'sequence' => [
        'check' => true,
        'error' => true,
        'fix' => false,
    ],
    'linkChecker' => [
        'check' => true,
        'error' => true,
        'fix' => false,
    ],
];
```

### Releaser (`release` command)

- Creates a new Release including the Git Tag

### Checkers (`validate` command)

This Checks are Implemented and can be activated

#### Default Checkers
- String replacement e.g more than two spaces will be removed with only one
- Fix > yes
- No Options available
#### Version Brackets

- Are the Version numbers in Brackets? If not > Error
- Fix > yes
- No Options available

#### Ascending Versioning

- Is the Version Ascending and will never decrease
- Also avoids duplicate Version Numbers
- Fix > no

#### Type Checker

- Check if the Type String is Valid (Added|Fixed etc.)
- Fix > no

#### Increased Checker

- Check if the Changelog Changed since the last 4 commits
- Fix Adds Commit messages to the unreleased Section
- creates two files `.clc.cheksum` and `.clc.version`
- requires git, runs `git log --oneline -n 4` (4 can change)

#### Link Checker

- Check if the Changelog has Links at the and of the File (Version Brackets are Required for this)
- Fix > (Not Yet Implemented)
- requires `git`  (git )

### Roadmap

- Add more Checkers
- Implement more fix methods (where possible)
- Implement tests for all "Checkers"
- Refactor the Checker, after tests are written
- Refactor the CreateRelease Command
- Create A Class or A little library to manage all the regex patterns  
- Check Support for alpha/beta etc. Releases and Versions
