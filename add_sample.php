<?php

    /**
     * Demo addition of functionality.
     */

    require __DIR__ . "/FileUploader.php";

    $designPatterns = ['base', 'dark']; // The first value will be the main

    $uploader = new \Phphleb\Updater\FileUploader(__DIR__ . DIRECTORY_SEPARATOR . "hleb-project-ratio");

    $uploader->setDesign($designPatterns);

    $uploader->setPluginNamespace(__DIR__, 'Updater');

    $uploader->setSpecialNames('example-directory', 'ExampleDirectory');

    $uploader->run();



