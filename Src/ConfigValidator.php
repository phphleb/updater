<?php

declare(strict_types=1);

namespace Phphleb\Updater\Src;

use Hleb\Static\Settings;

class ConfigValidator
{
   public static function checkAndGet(array $config): Config
   {
       // Check if the component is correct.
       // Проверка, что компонент указан правильно.
       $component = $config['component'] ?? null;
       if (empty($component) || !\is_string($component)) {
           throw new \DomainException('The library configuration parameter must contain the appropriate `component` field.');
       }
       $libraryPath = Settings::getRealPath('@vendor/' . $component);
       $configFile = $libraryPath . DIRECTORY_SEPARATOR . 'updater.json';

       // The component must match the default library configuration file.
       // Компонент должен соответствовать дефолтному файлу конфигурации библиотеки.
       if (!$libraryPath || !\file_exists($configFile)) {
           throw new \DomainException('The library configuration parameter must contain the appropriate `component` field.');
       }

       // Check if an existing directory is specified.
       // Проверка указания существующей директории.
       $directory = $config['directory'];
       if (empty($directory) || \str_contains($directory, '\\') || \str_contains($directory, '/')) {
           throw new \DomainException("Wrong directory path in $component library configuration.");
       }
       $directory = $libraryPath . DIRECTORY_SEPARATOR . $config['directory'];
       if (!\is_dir($directory)) {
           throw new \DomainException("The directory from the $component library configuration was not found.");
       }

       $settings = $config['settings'];
       if ($settings) {
           foreach($settings as $name => $option) {
               if (empty($option['select']) || !\is_array($option['select'])) {
                   throw new \DomainException("The default value is incorrectly specified in the settings of the `$name` (select) option.");
               }
               if (empty($option['name']) || !\is_string($option['name'])) {
                   throw new \DomainException("The default value is incorrectly specified in the settings of the `$name` (name) option.");
               }
               if (empty($option['default']) || !\is_string($option['default']) || !\in_array($option['default'], $option['select'])) {
                   throw new \DomainException("The default value is incorrectly specified in the settings of the `$name` (default) option.");
               }
           }
       }
       return new Config($config);
   }
}