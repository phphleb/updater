<?php

namespace Phphleb\Updater;

use Phphleb\Updater\Classes\OutputLog;
use Phphleb\Updater\Classes\SearchDirections;
use Phphleb\Updater\Classes\AddLogin;
use Phphleb\Updater\Classes\Data;

class FileUploader
{
    protected $directory;

    protected $designPatterns = ['base', 'dark'];

    protected $directoryName = 'example-directory';

    protected $className = 'ExampleDirectory';

    protected $log;

    protected $design = 'base';

    /**
     * Creates file transfer from the specified directory and distributes the project
     * @param string $directory
     */
    public function __construct(string $directory) {
        require_once __DIR__ . "/loader.php";
        $this->directory = $directory;
        $this->log = new OutputLog();
    }

    public function setDesign(array $patterns) {
        if (empty($patterns)) {
            $this->log->die("Error: No design templates specified!");
        }
        $this->designPatterns = $patterns;
    }

    public function getDesign() {
        return $this->design;
    }

    public function setSpecialNames(string $directoryName, string $className){
        $this->directoryName = $directoryName;
        $this->className = $className;
    }

    public function setPluginNamespace(string $libraryPath, string $libraryClassName){
        $this->pluginPath = $libraryPath;
        $this->pluginClassName = $libraryClassName;
    }

    public function run() {

        if (!Data::isIncludedBySpecialFirstName($this->directoryName)) {
            echo PHP_EOL . "Omitted due to being missing from the config file." . PHP_EOL . PHP_EOL;

            return false;
        }

        $updater = new SearchDirections(
            $this->directory,
            $this->log,
            $this->designPatterns,
            $this->directoryName,
            $this->className
        );

        $updater->setDesign();

        $this->design = $updater->getGlobalDesign();

        $updater->run();

        (new AddLogin($updater, $this->log))->run();

        return true;
    }

}