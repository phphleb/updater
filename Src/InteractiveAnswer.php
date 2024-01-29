<?php

declare(strict_types=1);

namespace Phphleb\Updater\Src;

use Hleb\DynamicStateException;
use Hleb\Main\Console\WebConsole;

class InteractiveAnswer
{
    /**
     * @param string $text - question text.
     *                     - текст вопроса.
     *
     * @param array $variants - an array of possible answers.
     *                        - массив возможных вариантов ответа.
     *
     * @param string $default - default value in case of no response.
     *                        - дефолтное значение при отсутствии ответа.
     *
     * @return string
     */
    public function getLine(string $text, array $variants, string $default): string
    {
        if (WebConsole::isUsed()) {
            $error = 'Sorry, but the Web Console does not support interactive queries.';
            print 'ERROR: ' . $error . PHP_EOL;
            throw new DynamicStateException($error);
        }
        $result = \readline($text);
        if ($result === '') {
            return $default;
        }
        if (!\in_array($result, $variants)) {
            echo PHP_EOL . "The entered value is incorrect." . PHP_EOL;

            return $this->getLine($text, $variants, $default);
        }
        return $result;
    }
}