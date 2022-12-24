<?php

    /**
     * Demo addition of functionality.
     */

    include_once __DIR__ . "/FileUploader.php";

    $designPatterns = ['base', 'dark']; // The first value will be the main

    $uploader = new \Phphleb\Updater\FileUploader(__DIR__ . DIRECTORY_SEPARATOR . "hleb-project-relationship");

    $uploader->setDesign($designPatterns);

    $uploader->setPluginNamespace(__DIR__, 'Updater');

    $uploader->setSpecialNames('example-directory', 'ExampleDirectory');

    $uploader->run();



