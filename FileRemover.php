<?php

namespace Phphleb\Updater;

use Phphleb\Updater\Classes\OutputLog;
use Phphleb\Updater\Classes\RemoveLogin;
use Phphleb\Updater\Classes\SearchDirections;

class FileRemover
{
    protected $directory;

    protected $directoryName = 'example-directory';

    protected $className = 'ExampleDirectory';

    protected $log;

    /**
     * Creates file transfer from the specified directory and distributes the project
     * @param string $directory
     */
    public function __construct(string $directory) {
        require_once __DIR__ . "/loader.php";
        $this->directory = $directory;
        $this->log = new OutputLog();
    }

    public function setSpecialNames(string $directoryName, string $className){
        $this->directoryName = $directoryName;
        $this->className = $className;
    }

    public function run() {

        $updater = new SearchDirections($this->directory, $this->log, ['base'], $this->directoryName, $this->className);

        $updater->run();

        (new RemoveLogin($updater, $this->log, true))->run();
    }

}