# Changelog Checker &copy; Boscho87

Check if the Changelog has proper format, and if it was updated since the last commit. Some problems can be fixed by the
tool if you want (see more in configs)

On Every command it will create a Changelog Backup for you. It will rotate 4 Backlog files.

Follows the Guiltiness from [Keep A Changelog](https://keepachangelog.com/en/1.1.0/)

### Installation

Best practice: Install in a subdirectory to avoid dependency Problems

```shell
mkdir --parents tools/changelog-checker
composer require --working-dir=tools/changelog-checker boscho87/changelog-checker
## ignore backup files
echo .gitignore >> clc.bak.*
echo .gitignore >> tools/changelog-checker/vendor
```

### Execute the Command

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
```

### Config

Create a file named `{project-root}/_clc.php` to Overwrite the default configs

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
];
```


### Checkers

This Checks are Implemented and can be activated

#### Version Brackets
 
- Are the Version numbers in Brackets? If not > Error
- Fix > yes

#### Ascending Versioning

- Is the Version Ascending and will never decrease
- Also avoids duplicate Version Numbers
- Fix > no

#### Type Checker

- Check if the Type String is Valid (Added|Fixed etc.)
- Fix > no

#### Increased Checker

- Check if the Changelog Changed since the last 4 commits
- Fix > no



