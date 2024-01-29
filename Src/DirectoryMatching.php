<?php

declare(strict_types=1);

namespace Phphleb\Updater\Src;

use FilesystemIterator;
use Hleb\Helpers\NameConverter;
use Hleb\Static\Settings;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DirectoryMatching
{
    private NameConverter $converter;

    private array $options = [];

    private array $readOptions = [];

    public function __construct(
        private readonly Config $config,
        private readonly bool   $noInteractive,
    )
    {
        $this->converter = new NameConverter();
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
     * Gathering data to match the files in the library with the project files.
     * Interactively prompts for user selection.
     *
     * Сбор данных для соответствия файлов в библиотеке с файлами проекта.
     * В интерактивном режиме запрашивает выбор пользователя.
     */
    public function getInteractiveMap(): array
    {
        $this->options = [];
        $I = DIRECTORY_SEPARATOR;

        if (!$this->config->settings) {
            return [];
        }
        if (!($map = $this->getMap())) {
            return [];
        }
        [$rows, $options] = $map;
        $settingKeys = \array_keys($this->config->settings);
        foreach ($options as $option) {
            if (!\in_array($option, $settingKeys)) {
                throw new \DomainException("Required option $option not found in library configuration.");
            }
        }
        $matches = [];
        $num = 0;
        foreach ($settingKeys as $setting) {
            if (!\in_array($setting, $options)) {
                $num++;
                if ($this->noInteractive) {
                    $this->readOptions[$setting] = $this->config->settings[$setting]['default'];
                } else {
                    $this->readOptions[$setting] = $this->asksAQuestion($setting, $num);
                }
            }
        }
        foreach ($options as $option) {
            if (\count($this->config->settings[$option]['select']) > 1) {
                $num++;
            }
            if ($this->noInteractive) {
                $matches[$option] = $this->config->settings[$option]['default'];
            } else {
                $matches[$option] = $this->asksAQuestion($option, $num);
            }
            $this->readOptions[$option] = $matches[$option];
        }
        $result = [];
        $preview = [];
        foreach ($rows as $row) {            ;
            $fracture = \explode('~', $row);
            $primaryPath = \rtrim(\array_shift($fracture), '\\/');
            $parts = \explode($I, \trim(\end($fracture), '\\/'));
            $optionName = \array_shift($parts);
            $option = \array_shift($parts);
            $endPath = \implode($I, $parts);
            $preview[$primaryPath][$optionName][$option][] = [$endPath, $row];
        }
        $resultOptions = [];
        foreach ($preview as $primaryPath => $data) {
            // $rowOptions = [...[relative_path, full_path]]
            $rowOptions = \end($data);
            // Getting the option name as the name of the data array.
            // Получение названия опции как имени массива с данными.
            $optionName = \array_search($rowOptions, $data, true);
            $optionLowerCase = $this->converter->convertClassNameToStr($optionName);
            $default = $this->config->settings[$optionLowerCase]['default'];
            $option = $matches[$optionLowerCase] ?? $default;
            if (empty($rowOptions[$option])) {
                $option = $default;
            }
            $resultOptions[$optionLowerCase] = $option;
            $files = $rowOptions[$option];
            foreach ($files as $file) {
                [$endPath, $row] = $file;
                $result[$row] = \implode($I, [$primaryPath, $optionName, $endPath]);
            }
        }

        foreach ($this->config->settings as $key => $setting) {
            if (isset($resultOptions[$key])) {
                $this->options[$key] = $resultOptions[$key];
                continue;
            }
            $this->options[$key] = $this->config->settings[$key]['default'];
        }

        return $result;
    }

    /**
     * Returns a list of directories to delete to roll back changes.
     *
     * Возвращает список директорий на удаление для отката изменений.
     */
    public function getListToDelete(): array
    {
        $this->options = [];
        if (!$this->config->settings) {
            return [];
        }
        if (!($map = $this->getMap())) {
            return [];
        }
        $rows = $map[0];

        $result = [];
        foreach ($rows as $row) {
            $parts = \explode('~', $row);
            $result[] = $parts[0] . \explode(DIRECTORY_SEPARATOR, $parts[1])[0];
        }
        return \array_unique($result);
    }

    /**
     * Returns a list of found file matches.
     *
     * Возвращает список найденных соответствий файлов.
     */
    private function getMap(): array
    {
        $publicDir = Settings::getRealPath('public');
        if (!$publicDir) {
            throw new \DomainException('Project public folder not found! Set the correct value for the HLEB_PUBLIC_DIR constant in the ./console file.');
        }
        $component = $this->config->component;
        $directory = $this->config->directory;
        $dir = Settings::getRealPath("@vendor/$component/$directory/rewrite");
        if (!$dir) {
            return [];
        }
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );

        $rows = [];
        $options = [];
        foreach ($files as $file) {
            $row = $this->trimPostfixIfExists(\str_replace($dir, '', $file->getRealPath()));
            $rows[] = $row;
            if (!\str_contains($row, DIRECTORY_SEPARATOR . '~')) {
                throw new \DomainException("The location of the option in one of the directories must be assigned: $file");
            }
            $parts = \explode(DIRECTORY_SEPARATOR, $row);
            $branches = [];
            foreach ($parts as $part) {
                if (\str_starts_with($part, '~')) {
                    $option = \trim($part, '~');
                    $convertOption = $this->converter->convertClassNameToStr($option);
                    if ($convertOption) {
                        $options[] = $convertOption;
                    }
                    if (\in_array($convertOption, $branches)) {
                        throw new \DomainException("The $option option can only be assigned once.");
                    }
                    $branches[] = $convertOption;
                }
            }
        }
        return [$rows, \array_unique($options)];
    }

    private function asksAQuestion(string $option, int $number): string
    {
        $config = $this->config->settings[$option];
        $name = $config['name'];
        $variants = $config['select'];
        $rowVariants = \implode(', ', $config['select']);
        $default = $config['default'];
        if (\count($variants) === 1) {
            return $default;
        }
        $text = "$number) Select $name from [$rowVariants], default is `$default`>";

        return $this->getLine($text, $variants, $default);
    }

    private function getLine(string $text, array $variants, string $default): string
    {
        return (new InteractiveAnswer())->getLine($text, $variants, $default);
    }

    private function trimPostfixIfExists(mixed $str): string
    {
      $str = (string)$str;
      if (str_ends_with($str, '-upd')) {
          $parts = explode('-', $str);
          array_pop($parts);

          return implode('-', $parts);
      }
      return $str;
    }
}