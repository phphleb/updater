<?php

namespace Phphleb\Updater\Classes;

interface LogInterface
{
    public function die(string $message);
    
    public function print(string $message);

    public function exit();

    public function readline(string $message);
}