<?php

    /**
     * Demo removal of functionality.
     */

    require __DIR__ . "/FileRemover.php";

    $remover = new \Phphleb\Updater\FileRemover(__DIR__ . DIRECTORY_SEPARATOR . "hleb-project-relationship");

    $remover->setSpecialNames('example-directory', 'ExampleDirectory');

    $remover->run();
