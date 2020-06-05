<?php

namespace Phphleb\Updater;

use Phphleb\Updater\Classes\OutputLog;
use Phphleb\Updater\Classes\SearchDirections;
use Phphleb\Updater\Classes\AddLogin;

class FileUploader
{
    protected $directory;

    protected $designPatterns = ['base', 'dark'];

    protected $directoryName = 'example-directory';

    protected $className = 'ExampleDirectory';

    protected $log;

    /**
     * Creates file transfer from the specified directory and distributes the project
     * @param string $directory
     */
    public function __construct(string $directory) {
        require_once __DIR__ . "/config.php";
        $this->directory = $directory;
        $this->log = new OutputLog();
    }

    public function setDesign(array $patterns) {
        if (empty($patterns)) {
            $this->log->die("Error: No design templates specified!");
        }
        $this->designPatterns = $patterns;
    }

    public function setSpecialNames(string $directoryName, string $className){
        $this->directoryName = $directoryName;
        $this->className = $className;
    }

    public function setPluginNamespace(string $libraryPath, string $libraryClassName){
        $this->pluginPath = $libraryPath;
        $this->pluginClassName = $libraryClassName;
    }

    public function setUniqueClassLabel(string $tag) {
        $this->uniqueClassTag = $tag;
    }

    public function run() {

        $updater = new SearchDirections(
            $this->directory,
            $this->log,
            $this->designPatterns,
            $this->directoryName,
            $this->className,
            $this->pluginPath,
            $this->pluginClassName
        );

        $updater->setDesign();

        $updater->run();

        (new AddLogin($updater, $this->log, $this->uniqueClassTag))->run();
    }

}