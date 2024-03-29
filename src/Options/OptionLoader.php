<?php

namespace Boscho87\ChangelogChecker\Options;

use Boscho87\ChangelogChecker\Exception\FileNotFoundException;

/**
 * @codeCoverageIgnore
 * Class OptionLoader
 */
class OptionLoader
{
    /**
     * @var Option[]
     */
    private array $options = [];
    private string $defaultConfigFile = __DIR__ . '/../../config/default.php';
    private string $configFile = '_clc.php';
    private ?string $inputConfigFile = null;
    private ?bool $forceFix;

    /**
     * OptionLoader constructor.
     */
    public function __construct(?string $inputConfigFile, ?bool $forceFix)
    {
        if ($inputConfigFile) {
            $this->inputConfigFile = sprintf('%s/%s', getcwd(), $inputConfigFile);
        }
        $this->forceFix = $forceFix;
    }


    public function load(string $workingDir): void
    {
        $configs = [];
        $defaultConfigs = [];
        $inputfileConfig = [];
        $configFile = sprintf(
            '%s/%s',
            rtrim($workingDir, '/'),
            $this->configFile
        );
        if (file_exists($configFile) && is_file($configFile)) {
            $configs = require $configFile;
        }
        if (file_exists($this->defaultConfigFile) && is_file($this->defaultConfigFile)) {
            $defaultConfigs = require $this->defaultConfigFile;
        }
        if ($this->inputConfigFile) {
            if (!file_exists($this->inputConfigFile) || !is_file($this->inputConfigFile)) {
                throw new FileNotFoundException(sprintf('Config File {%s} not found', $this->inputConfigFile));
            }
            $inputfileConfig = require $this->inputConfigFile;
        }
        $configs = array_merge($defaultConfigs, $configs, $inputfileConfig);
        foreach ($configs as $name => $config) {
            $fix = $this->forceFix !== null ? $this->forceFix : $config['fix'];
            $this->options[$name] = new Option($config['check'], $config['error'], $fix, $config);
        }
    }

    /**
     * @param $name
     * @return Option
     */
    public function __get($name)
    {
        return $this->options[$name];
    }
}
