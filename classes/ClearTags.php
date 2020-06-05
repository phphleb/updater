<?php

namespace Phphleb\Updater\Classes;

class ClearTags
{
    protected $tag;

    function __construct(string $tagName) {
        $this->tag = $tagName;
    }

    function handler(string $filePath) {

        $file = fopen($filePath, 'r');
        $text = fread($file, filesize($filePath));
        fclose($file);
        $file = fopen($filePath, 'w');
        fwrite($file, str_replace($this->tag, '', $text));
        fclose($file);

    }
}