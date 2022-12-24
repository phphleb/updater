<?php

    /**
     * Demo removal of functionality.
     */

    include_once __DIR__ . "/FileRemover.php";

    $remover = new \Phphleb\Updater\FileRemover(__DIR__ . DIRECTORY_SEPARATOR . "hleb-project-relationship");

    $remover->setSpecialNames('example-directory', 'ExampleDirectory');

    $remover->run();
