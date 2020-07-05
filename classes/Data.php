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

    /////////////////////////////////////
    protected static $design;

    public static function setDesign(string $type) {
        self::$design = $type;
    }

    public static function getDesign() {
        return self::$design;
    }

 }