<?php

namespace Phphleb\Updater\Classes;

class Data
{
    private static $instance;

    private function __construct(){}

    public function __clone(){}

    public static  function instance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }


    protected static $config = null;

    protected static $paths = [];

    protected static $deleteConfirm = true;

    public static function setConfig(array $data) {
        !is_null(self::$config) or self::$config = $data;
    }

    public static function isIncludedBySpecialFirstName(string $specialFirstName) {
        if (self::$config) {
            return isset(self::$config['include_special_names'][$specialFirstName]);
        }

        return true;
    }

    public static function getComponentDesignBySpecialName(string $specialFirstName): ?string
    {
        if (self::$config) {
            $design = self::$config['include_special_names'][$specialFirstName] ?? null;
            if ($design && !empty($design['design']) && is_string($design['design'])) {
                return $design['design'];
            }
        }

        return null;
    }

    public static function getComponentFolderName(string $searchDir): ?string
    {
        if (!empty(self::$paths[$searchDir])) {
            return self::$paths[$searchDir];
        }
        $searchDirFirst = str_replace('\\', '/', trim($searchDir, '/\\ '));
        $searchDirSecond = str_replace('/', '\\', trim($searchDir, '/\\ '));
        if (self::$config) {
            $paths = self::$config['global']['paths'] ?? [];
            if ($paths && is_array($paths) && (!empty($paths[$searchDirFirst]) || !empty($paths[$searchDirSecond]))) {
                $searchDir = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, trim($searchDir, '/\\ '));
                self::$paths[$searchDir] = $paths[$searchDir];

                return self::$paths[$searchDir];
            }
        }
        return null;
    }

    public static function setDisableConfirmationOfDelete() {
        self::$deleteConfirm = false;
    }

    public static function getConfirmationOfDelete() {
        return self::$deleteConfirm;
    }

 }