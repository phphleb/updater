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
    $action = (bool) selectAction();
}

include __DIR__ . ($action ? "/add_sample.php" : "/remove_sample.php");

function selectAction()
{
    $actionType = readline('What action should be performed? Enter symbol to add(A) or remove(R) test files>');
    if ($actionType === "A") {
        return true;
    }
    if ($actionType === "R") {
        return false;
    }

    return selectAction();

}


