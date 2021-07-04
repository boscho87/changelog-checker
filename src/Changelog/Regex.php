<?php

namespace Boscho87\ChangelogChecker\Changelog;

/**
 * Class Regex
 */
final class Regex
{
    public const VERSION_TITLE_PATTERN = '/(\[(\d+\.\d+\.\d+)\])\s/';
    public const HREF_START_VERSION = '/\d+\.\d+\.\d+\.\.\./';
    public static array $allowedTypes = ['Added', 'Changed', 'Deprecated', 'Removed', 'Fixed', 'Security'];



    public static function allowedTypesPattern(): string
    {
        $types = implode('|', self::$allowedTypes);
        return sprintf('/(###\s)(%s)$/', $types);
    }
}
