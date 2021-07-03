<?php

namespace Boscho87\ChangelogChecker\Changelog;

/**
 * Class Regex
 */
final class Regex
{
    public const VERSION_TITLE = '/\[(\d+\.\d+\.\d+)\]\s/';
    public const HREF_START_VERSION = '/\d+\.\d+\.\d+\.\.\./';
}
