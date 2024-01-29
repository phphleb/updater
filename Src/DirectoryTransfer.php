<?php

declare(strict_types=1);

namespace Phphleb\Updater\Src;

use Hleb\Helpers\DirectoryCleaner;
use Hleb\Static\Path;
use Hleb\Static\Settings;

readonly class DirectoryTransfer
{
    private DirectoryCleaner $cleaner;

    public function __construct()
    {
        $this->cleaner = new DirectoryCleaner();
    }

    /**
     * Transferring files to a project.
     *
     * Перенос файлов в проект.
     */
    public function moveToProject(array $map, Config $file): bool
    {
        $path = Settings::getRealPath('global');
        $updater = Settings::getRealPath("@vendor/phphleb/updater");
        $component = Settings::getRealPath("@vendor/{$file->component}");
        $I = DIRECTORY_SEPARATOR;
        $public = Path::getReal('public');
        $publicName = basename($public);
        $matching = $component . $I . $file->directory . $I . 'rewrite';
        $attention = $updater . $I . 'templates' . $I . 'ATTENTION.md';
        $insertion = \file_get_contents($updater . $I . 'templates' . $I . 'insertion.php');

        foreach ($map as $configFile => $targetFile) {
            $target = $path . $this->updatePublicDir($targetFile, $publicName);
            $file = $matching . $configFile;
            if (empty($configFile) || \str_ends_with($targetFile, '/') || !\str_contains($targetFile, '.')) {
                echo "[?] Not added $targetFile..." . PHP_EOL;
                return false;
            }
            if (\file_exists($target)) {
                \unlink($target);
            }
            echo "[+] Added file $targetFile" . PHP_EOL;
            $dir = \dirname($target);
            \hl_create_directory($dir);

            if (!file_exists($file)) {
                $file .= '-upd';
            }
            \copy($file, $target);
            if (!\file_exists($target)) {
                return false;
            }
            $resultContent = \file_get_contents($target);
            if (\str_ends_with($target, '.php')) {
                if (\str_starts_with($resultContent, '<?php')) {
                    $content = $insertion . \substr($resultContent, 5);
                } else {
                    $content = $insertion . '?>' . $resultContent;
                }
                \file_put_contents($target, $content);
            }
            if ($attention && !\str_starts_with($targetFile, $I . 'public')) {
                \copy($attention, $dir . $I . 'ATTENTION.md');
            }
        }

        return true;
    }

    /**
     * Complete removal of directories and contents.
     *
     * Полное удаление директорий и содержимого.
     */
    public function removeFromProject(array $map, bool $hidden = false): bool
    {
        $path = Settings::getRealPath('global');
        $public = Path::getReal('public');
        $publicName = basename($public);
        foreach ($map as $dirName) {
            $directory = $path . $this->updatePublicDir($dirName, $publicName);
            if (\file_exists($directory)) {
                if (!$hidden) {
                    echo "[-] Remove directory $dirName" . PHP_EOL;
                }
                $this->cleaner->removeDir($directory);
                if (\file_exists($directory)) {
                    return false;
                }
            }
        }
        return true;
    }

    private function updatePublicDir(string $relativePath, string $publicDirName): string
    {
        $relativeParts = explode('/', str_replace('\\', '/', trim($relativePath, '/\\')));
        if (current($relativeParts) !== 'public') {
            return $relativePath;
        }
        $relativeParts[0] = $publicDirName;

        return DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $relativeParts);
    }
}