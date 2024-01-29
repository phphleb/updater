<?php

declare(strict_types=1);

namespace Phphleb\Updater\Src;

readonly class Config
{
    public ?string $name;

    public ?string $description;

    public ?string $directory;

    public array $settings;

    public string $component;

    public string $target;

    public function __construct(private array $config)
    {
        $config = $this->config;
        $this->name = $config['name'] ?? null;
        $this->description = $config['description'] ?? null;
        $this->directory = $config['directory'] ?? null;
        $this->settings = $config['settings'] ?? [];
        $this->component = $config['component'];
        $componentName = \explode('/', $config['component']);
        $this->target = $config['target'] ?? \end($componentName);
    }
}