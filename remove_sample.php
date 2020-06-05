<?php

    /**
     * Demo removal of functionality.
     */

    require __DIR__ . "/FileRemover.php";

    $uploader = new \Phphleb\Updater\FileRemover(__DIR__ . DIRECTORY_SEPARATOR . "hleb-project-ratio");

    $uploader->setSpecialNames('example-directory', 'ExampleDirectory');

    $uploader->run();
