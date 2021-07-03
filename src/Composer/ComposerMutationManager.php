<?php

namespace Boscho87\ChangelogChecker\Composer;

/**
 * Class MutationManager
 * todo exception handling
 * todo tests
 */
class ComposerMutationManager
{
    public function replaceComposerVersion(string $composerFilePath, string $newVersion): bool
    {
        $composerContent = file_get_contents($composerFilePath);
        $replaced = preg_replace('/"version":"\d+\.\d+\.\d+"/', sprintf('"version": "%s"', $newVersion), $composerContent);
        if ($composerContent !== $replaced) {
            file_put_contents($composerFilePath, $replaced);
            return true;
        }
        return false;
    }
}
