<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

$action = true;

if (end($argv) === '--help') {
    die (
        "\n" . "Test functional update for the HLEB project." .
        "\n" . "--remove (delete files)" .
        "\n" . "--add    (update files)" . "\n"
    );
}

if (end($argv) === '--remove') {
    $action = false;
} else if (end($argv) === '--add') {
    $action = true;
} else {
    exit(PHP_EOL . 'For details, repeat the command with the `--help` flag.' . PHP_EOL);
}

include __DIR__ . ($action ? "/add_sample.php" : "/remove_sample.php");