<?php

namespace Phphleb\Updater\Classes;

class SearchDirections
{
    protected $className = "ExampleDirectory";

    protected $directoryName = "example-directory";

    protected $designPatterns = ['base', 'dark'];

    protected $globalDesign = "base";

    protected $pluginDirectory;

    protected $baseDirectory;

    protected $appDirectory;

    protected $controllersDirectory;

    protected $publicDirectory;

    protected $publicJsDirectory;

    protected $storageDirectory;

    protected $resourcesDirectory;

    protected $vendorDirectory;

    protected $routesDirectory;

    protected $middlewareDirectory;

    protected $baseDirectoryOrigin;

    protected $appDirectoryOrigin;

    protected $controllersDirectoryOrigin;

    protected $publicDirectoryOrigin;

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
    
    protected $libraryPath;
    
    protected $libraryClassName;

    /**
     * If the resulting paths change
     */
    protected $listNames = [
        'public' => 'public',
        'storage' => 'storage',
    ];

    function __construct(string $pluginDirectory, LogInterface $log, array $designPatterns, string $directoryName, string $className, string $libraryPath = null, string $libraryClassName = null) {

        $this->log = $log;

        $this->directoryName = $directoryName;

        $this->libraryPath = $libraryPath;

        $this->libraryClassName = $libraryClassName;

        $this->className = $className;

        $this->designPatterns = $designPatterns;

        $globalDesign = $designPatterns[0];

        $this->pluginDirectory = $pluginDirectory;

        $this->baseDirectory = realpath(__DIR__ . '/../../../../');
    }

    public function run() {

        $this->searchDirectory('app', 'appDirectory', $this->className);

        $this->searchDirectory('app' . DIR_S . 'Controllers', 'controllersDirectory', $this->className);

        $this->searchDirectory('public', 'publicDirectory', $this->directoryName, true);

        $this->searchDirectory('storage', 'storageDirectory', $this->directoryName, true);

        $this->searchDirectory('storage' . DIR_S . 'public', 'storageDirectory', $this->directoryName, $this->listNames['storage'] . DIR_S . 'public');

        $this->searchDirectory('public' . DIR_S . 'js', 'publicJsDirectory', $this->directoryName, true, $this->listNames['public'] . DIR_S . 'js');

        $this->searchDirectory('resources' . DIR_S . 'views', 'resourcesDirectory', $this->directoryName);

        $this->searchDirectory('routes', 'routesDirectory', $this->directoryName);

        $this->searchDirectory('app' . DIR_S . 'Middleware' . DIR_S . 'Before', 'middlewareDirectory', $this->className);

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
        $list =  implode("|", $this->designPatterns);
        $design = trim($this->log->readline("Which of the following designs to install? $list >"));
        if(empty($design)){
            return;
        }
        if(!in_array($design, $this->designPatterns)){
            $this->setDesign($original);
            return;
        }
        $this->globalDesign = $design;

        $this->createSpecFile($design);
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
        if ($search && !file_exists($this->baseDirectory . DIR_S . $actualName)) {
            $actualName = $this->readlineDir($name);
            if (!file_exists($this->baseDirectory . DIR_S . $actualName)) {
                $this->searchDirectory($name, $value, $system, true);
                return;
            }
            if (array_key_exists($name, $this->listNames)) {
                $this->listNames[$name] = $actualName;
            }
        }
        if (file_exists($this->baseDirectory . DIR_S . $actualName)) {

            $this->$value = $this->baseDirectory . DIR_S . $actualName . DIR_S . $system;
            $systemResource = $this->pluginDirectory .  DIR_S . $name . DIR_S . $system;
            $baseRecource = $systemResource . DIR_S . "base";
            $designRecource = $systemResource . DIR_S . $this->globalDesign;
            if(!file_exists($designRecource)){
                $designRecource = $baseRecource;
            }
            $this->$originDirectory = $designRecource;
            if (is_dir($this->$value)) {
                $this->actualList[] = $this->$value;
            }
            if (is_dir($this->$originDirectory)) {
                $this->actuaOriginlList[] = $this->$originDirectory;
            }
            $this->originList[] = $this->$originDirectory;
            $this->targetList[] = $this->$value;
        } else {
            $this->errors[] = $name;
        }
    }

    private function readlineDir(string $name) {
        return $this->log->readline("Enter the current '$name' folder name>");
    }
    
    private function createSpecFile(string $name) {
        
        $content = "<?php" . "\n" . "\n" .
            "namespace Phphleb\\{$this->libraryClassName};" . "\n" . "\n" .
            "class SpecDeterminant" . "\n" .
            "{" . "\n" . "  const SPEC_NAME = '$name';" . "\n" . "}" . "\n";

        file_put_contents($this->libraryPath . DIR_S . "SpecDeterminant.php", $content);
    }

}