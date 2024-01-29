<?php

declare(strict_types=1);

namespace Phphleb\Updater;

use Hleb\Static\Log;
use Phphleb\Updater\Src\ConfigTransfer;
use Phphleb\Updater\Src\ConfigValidator;
use Phphleb\Updater\Src\DirectoryTransfer;
use Phphleb\Updater\Src\DirectoryMatching;

class AddAction
{
    private array $options = [];

    private array $readOptions = [];

    /**
     * @param array $config - configuration similar to the updater.json file
     *                      - конфигурация по образцу файла updater.json
     *
     * @param bool $noInteraction - disable user interaction and apply default settings.
     *                            - отключение взаимодействия с пользователем и применение дефолтных настроек.
     *
     * @param bool $quiet - disabling the output of the script, respectively, disabling interactivity.
     *                    - отключение вывода работы скрипта, соответственно, отключение интерактивности.
     */
    public function __construct(
        private readonly array $config,
        private readonly bool  $noInteraction,
        private readonly bool  $quiet,
    )
    {
    }

    /**
     * Returns the total values of the options.
     *
     * Возвращает итоговые значения опций.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Returns the option values selected by the user.
     *
     * Возвращает выбранные пользователем значения опций.
     */
    public function getReadOptions(): array
    {
        return $this->readOptions;
    }

    /**
     * Performs all actions to deploy a library in a project,
     * this can be transferring files from folders specified
     * as `directory` in the configuration, as well as making
     * edits and transferring your own configuration.
     * Returns the execution code (0 - successful).
     *
     * Выполняет все действия для развертывания библиотеки в проекте,
     * это может быть перенос файлов из папки указанной как
     * `directory` в конфигурации, так и внесение правок и перенос
     * собственной конфигурации.
     * Возвращает код выполнения (0 - успешно).
     */
    public function run(): int
    {
        $config = ConfigValidator::checkAndGet($this->config);
        $text = "The {$config->component} library is being deployed to the project." . PHP_EOL;
        $noInteraction = $this->noInteraction;
        if ($this->quiet) {
            \ob_start();
            $noInteraction = true;
        }
        echo \str_repeat('#', 7) . " {$config->name} " . \str_repeat('#', 7) . PHP_EOL;
        echo $config->description .  PHP_EOL . $text;

        $deleteMap = (new DirectoryMatching($config, $noInteraction))->getListToDelete();
        if ($deleteMap) {
            (new DirectoryTransfer())->removeFromProject($deleteMap, hidden: true);
        }

        $code = (int)!(new ConfigTransfer($config))->moveToProject();
        $mapper = new DirectoryMatching($config, $noInteraction);
        $map = $mapper->getInteractiveMap();
        $this->options = $mapper->getOptions();
        $this->readOptions = $mapper->getReadOptions();
        if ($map) {
            $isMoved = (new DirectoryTransfer())->moveToProject($map, $config);
            $code = $code ?: (int)!$isMoved;
        }
        $this->quiet and \ob_end_clean();

        Log::info("Deploying component {$config->component}", [$this->config]);

        return $code;
    }
}