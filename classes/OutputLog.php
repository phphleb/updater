<?php

namespace Phphleb\Updater\Classes;

class OutputLog implements LogInterface
{
     public function die(string $text) {
         die($text);
     }

     public function print(string $message) {
         print($message);
     }

    public function readline(string $message) {
        return readline($message);
    }

    public function exit() {
        exit();
     }

}