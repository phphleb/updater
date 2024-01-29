<?php

declare(strict_types=1);

namespace Phphleb\Updater\Src;

use FilesystemIterator;
use Hleb\Static\Settings;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

readonly class ConfigTransfer
{
    public function __construct(
        private Config $config
    )
    {
    }

    public function moveToProject(): bool
    {
        $config = $this->config;
        $libConfigDir = Settings::getRealPath("@vendor/{$config->component}/{$config->directory}/config");

        if (!$libConfigDir) {
            return true;
        }
        $projectConfigDir = Settings::getPath("@storage/lib/{$config->component}");
        \hl_create_directory($projectConfigDir);

        $configFiles = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($libConfigDir, FilesystemIterator::SKIP_DOTS)
        );
        foreach($configFiles as $configFile) {
            if (!$configFile->isFile()) {
                continue;
            }
            $configPath = $configFile->getRealPath();
            $file = \str_replace($libConfigDir, '', $configPath);
            $projectFile = $projectConfigDir . $file;
            if (!\file_exists($projectFile)) {
                \hl_create_directory($projectFile);
                \copy($configPath, $projectFile);
                echo "[+] Copied configuration file to /storage/lib/{$config->component}$file" . PHP_EOL;
            }
        }
       return true;
    }
}