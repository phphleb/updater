<?php

namespace Phphleb\Updater\Classes;

class SearchDirections
{
    protected $className = "ExampleDirectory";

    protected $directoryName = "example-directory";

    protected $designPatterns = ['base', 'dark'];

    protected $globalDesign = "base";

    protected $globalBaseDesign = 'base';

    protected $pluginDirectory;

    protected $baseDirectory;

    protected $appDirectory;

    protected $controllersDirectory;

    protected $commandsDirectory;

    protected $modelsDirectory;

    protected $publicDirectory;

    protected $publicJsDirectory;

    protected $publicCssDirectory;

    protected $publicImagesDirectory;

    protected $publicSvgDirectory;

    protected $storageDirectory;

    protected $resourcesDirectory;

    protected $vendorDirectory;

    protected $routesDirectory;

    protected $middlewareDirectory;

    protected $baseDirectoryOrigin;

    protected $actualOriginList = [];

    protected $appDirectoryOrigin;

    protected $controllersDirectoryOrigin;

    protected $publicDirectoryOrigin;

    protected $publicJsDirectoryOrigin;

    protected $publicCssDirectoryOrigin;

    protected $publicImagesDirectoryOrigin;

    protected $publicSvgDirectoryOrigin;

    protected $storageDirectoryOrigin;

    protected $resourcesDirectoryOrigin;

    protected $vendorDirectoryOrigin;

    protected $routesDirectoryOrigin;

    protected $middlewareDirectoryOrigin;

    protected $originList = [];

    protected $actualOiginList = [];

    protected $targetList = [];

    protected $actualList = [];

    protected $errors = [];

    protected $log;


    /**
     * If the resulting paths change
     */
    protected $listNames = [
        'public' => 'public',
        'storage' => 'storage',
    ];

    function __construct(string $pluginDirectory, LogInterface $log, array $designPatterns, string $directoryName, string $className) {

        $this->log = $log;

        $this->directoryName = $directoryName;

        $this->className = $className;

        $this->designPatterns = $designPatterns;

        $this->globalBaseDesign = $designPatterns[0];

        $this->pluginDirectory = $pluginDirectory;

        $this->baseDirectory = realpath(__DIR__ . '/../../../../');
    }

    public function run() {

        $this->searchDirectory('app', 'appDirectory', $this->className);

        $this->searchDirectory('app' . DIRECTORY_SEPARATOR . 'Controllers', 'controllersDirectory', $this->className);

        $this->searchDirectory('app' . DIRECTORY_SEPARATOR . 'Commands', 'commandsDirectory', $this->className);

        $this->searchDirectory('app' . DIRECTORY_SEPARATOR . 'Models', 'modelsDirectory', $this->className);

        //$this->searchDirectory('public', 'publicDirectory', $this->directoryName, true);

        $this->searchDirectory('storage', 'storageDirectory', $this->directoryName, true);

        $this->searchDirectory('storage' . DIRECTORY_SEPARATOR . 'public', 'storageDirectory', $this->directoryName, $this->listNames['storage'] . DIRECTORY_SEPARATOR . 'public');

        //$this->searchDirectory('public' . DIRECTORY_SEPARATOR . 'js', 'publicJsDirectory', $this->directoryName, true, $this->listNames['public'] . DIRECTORY_SEPARATOR . 'js');

        //$this->searchDirectory('public' . DIRECTORY_SEPARATOR . 'css', 'publicCssDirectory', $this->directoryName, true, $this->listNames['public'] . DIRECTORY_SEPARATOR . 'css');

        //$this->searchDirectory('public' . DIRECTORY_SEPARATOR . 'images', 'publicImagesDirectory', $this->directoryName, true, $this->listNames['public'] . DIRECTORY_SEPARATOR . 'images');

        //$this->searchDirectory('public' . DIRECTORY_SEPARATOR . 'svg', 'publicSvgDirectory', $this->directoryName, true, $this->listNames['public'] . DIRECTORY_SEPARATOR . 'svg');

        $this->searchDirectory('resources', 'resourcesDirectory', $this->directoryName);

        $this->searchDirectory('resources' . DIRECTORY_SEPARATOR . 'views', 'resourcesDirectory', $this->directoryName);

        $this->searchDirectory('routes', 'routesDirectory', $this->directoryName);

        $this->searchDirectory('app' . DIRECTORY_SEPARATOR . 'Middleware' . DIRECTORY_SEPARATOR . 'Before', 'middlewareDirectory', $this->className);

    }

    public function getProjectPath() {
        return $this->baseDirectory;
    }

    public function getAppPath() {
        return $this->appDirectory;
    }

    public function getAppControllersPath() {
        return $this->controllersDirectory;
    }

    public function getAppCommandsPath() {
        return $this->commandsDirectory;
    }

    public function getAppModelsPath() {
        return $this->modelsDirectory;
    }

    public function getPublicPath() {
        return $this->publicDirectory;
    }

    public function getStoragePublicPath() {
        return $this->storageDirectory;
    }

    public function getPublicJsPath() {
        return $this->publicJsDirectory;
    }

    public function getResourcesViewsPath() {
        return $this->resourcesDirectory;
    }

    public function getVendorPath(){
        return $this->vendorDirectory;
    }

    public function getRoutesPath() {
        return $this->routesDirectory;
    }

    public function getAppMiddlewareBeforePath() {
        return $this->middlewareDirectory;
    }

    public function getProjectOriginPath() {
        return $this->baseDirectoryOrigin;
    }

    public function getAppOriginPath() {
        return $this->appDirectoryOrigin;
    }

    public function getAppControllersOriginPath() {
        return $this->controllersDirectoryOrigin;
    }

    public function getPublicOriginPath() {
        return $this->publicDirectoryOrigin;
    }

    public function getPublicJsOriginPath() {
        return $this->publicJsDirectoryOrigin;
    }

    public function getPublicCssOriginPath() {
        return $this->publicCssDirectoryOrigin;
    }

    public function getPublicImagesOriginPath() {
        return $this->publicImagesDirectoryOrigin;
    }

    public function getPublicSvgOriginPath() {
        return $this->publicSvgDirectoryOrigin;
    }

    public function getStoragePublicOriginPath() {
        return $this->storageDirectoryOrigin;
    }

    public function getResourcesViewsOriginPath() {
        return $this->resourcesDirectoryOrigin;
    }

    public function getVendorOriginPath() {
        return $this->vendorDirectoryOrigin;
    }

    public function getRoutesOriginPath() {
        return $this->routesDirectoryOrigin;
    }

    public function getAppMiddlewareBeforeOriginPath(){
        return $this->middlewareDirectoryOrigin;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getOriginList() {
        return $this->originList;
    }

    public function getActualOriginList() {
        return $this->actualOiginList;
    }

    public function getActualList() {
        return $this->actualList;
    }

    public function getTargetList() {
        return $this->targetList;
    }

    public function setDesign($original = 'base') {
        $this->globalDesign = $original;
        $design = 'base';
        if (count($this->designPatterns) > 1) {
            $list = implode("|", $this->designPatterns);
            $design = trim($this->log->readline("Which of the following designs to install? $list >"));
            if (empty($design)) {
                return;
            }

            if (!in_array($design, $this->designPatterns)) {
                $this->setDesign($original);
                return;
            }
        }
        Data::setDesign($design);
        $this->globalDesign = $design;
    }

    protected function readlineDir(string $name) {
        return $this->log->readline("Enter the current '$name' folder name>");
    }

    /**
     * @param string $name - origin directory name
     * @param string $value - params name
     * @param string $system - system name
     * @param bool $search - variable name
     * @param string|null $target - target name
     */
    private function searchDirectory(string $name, string $value, string $system, bool $search = false, $target = null) {
        $originDirectory = $value . 'Origin';
        $actualName = empty($target) ? $name : $target;
        if ($search && !file_exists($this->baseDirectory . DIRECTORY_SEPARATOR . $actualName)) {
            $actualName = $this->readlineDir($name);
            if (!file_exists($this->baseDirectory . DIRECTORY_SEPARATOR . $actualName)) {
                $this->searchDirectory($name, $value, $system, true);
                return;
            }
            if (array_key_exists($name, $this->listNames)) {
                $this->listNames[$name] = $actualName;
            }
        }
        if (file_exists($this->baseDirectory . DIRECTORY_SEPARATOR . $actualName)) {

            $this->$value = $this->baseDirectory . DIRECTORY_SEPARATOR . $actualName . DIRECTORY_SEPARATOR . $system;
            $systemResource = $this->pluginDirectory .  DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $system;
            $baseRecource = $systemResource . DIRECTORY_SEPARATOR . $this->globalBaseDesign;
            $designRecource = $systemResource . DIRECTORY_SEPARATOR . $this->globalDesign;
            // If the design is not found, then the basic
            if(!file_exists($designRecource)){
                $designRecource = $baseRecource;
            }
            $this->$originDirectory = $designRecource;
            if (is_dir($this->$value)) {
                $this->actualList[] = $this->$value;
            }
            if (is_dir($this->$originDirectory)) {
                $this->actualOriginList[] = $this->$originDirectory;
            }
            $this->originList[] = $this->$originDirectory;
            $this->targetList[] = $this->$value;
        } else {
            $this->errors[] = $name;
        }
    }

}
