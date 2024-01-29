<?php

declare(strict_types=1);

namespace Phphleb\Updater;

use Hleb\Static\Log;
use Phphleb\Updater\Src\ConfigValidator;
use Phphleb\Updater\Src\DirectoryTransfer;
use Phphleb\Updater\Src\DirectoryMatching;
use Phphleb\Updater\Src\InteractiveAnswer;

readonly class RemoveAction
{
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
        private array $config,
        private bool  $noInteraction,
        private bool  $quiet,
    )
    {
    }

    /**
     * Performs all data rollback actions for a library in a project,
     * this can be deleting files by matching folder specified as `directory`
     * in the configuration, but the library configuration is not rolled back,
     * since it can be changed by the user.
     * Returns the execution code (0 - successful).
     *
     *
     * Выполняет все действия для отката данных для библиотеки в проекте,
     * это может быть удаление файлов по соответствию папки указанной как
     * `directory` в конфигурации, но откат конфигурации библиотеки
     * не производится, так как она может быть изменена пользователем.
     * Возвращает код выполнения (0 - успешно).
     */
    public function run(): int
    {
        $code = 0;
        $config = ConfigValidator::checkAndGet($this->config);
        $text = "The {$config->component} library data is being rolled back from the project." . PHP_EOL;
        $noInteraction = $this->noInteraction;
        if ($this->quiet) {
            \ob_start();
            $noInteraction = true;
        }
        echo \str_repeat('#', 7) . " {$config->name} " . \str_repeat('#', 7) . PHP_EOL;
        echo $config->description .  PHP_EOL . $text;

        if (!$noInteraction && !$this->getConfirm($config->component)) {
            echo PHP_EOL . 'Cancelled!' . PHP_EOL;
            return 1;
        }

        $map = (new DirectoryMatching($config, $noInteraction))->getListToDelete();
        if ($map) {
            $code = (int)!(new DirectoryTransfer())->removeFromProject($map);
        }
        $this->quiet and \ob_end_clean();

        Log::info("Remove component {$config->component}", [$this->config]);

        return $code;
    }

    private function getConfirm(string $component): bool
    {
        $text = "Are you sure you want to remove all deployed $component component data from the project?";
        $text .= ' [yes, no], default `no`>';
        return (new InteractiveAnswer())->getLine($text, ['yes', 'no'], 'no') === 'yes';
    }
}