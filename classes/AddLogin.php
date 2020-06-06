<?php

namespace Phphleb\Updater\Classes;

class AddLogin
{
    protected $files;

    protected $log;

    protected $num = 0;
    
    function __construct(SearchDirections $files, LogInterface $log) {
        $this->files = $files;
        $this->log = $log;
    }

    public function run() {
        $errors = $this->files->getErrors();

        if (count($errors)) {
            $this->log->die("\n" . "You must create the '" . DIRECTORY_SEPARATOR . $errors[0] . "' directory in project!" . "\n");
        }
        // Clear all
        (new RemoveLogin($this->files, $this->log, false))->run();

        $originList = $this->files->getOriginList();
        $targetList = $this->files->getTargetList();
        if (count($originList) === count($targetList)) {
            for ($i = 0; $i < count($originList); $i++) {
                if (file_exists($originList[$i])) {
                    $this->copyRecursiveFiles(
                        $originList[$i],
                        $targetList[$i]
                    );
                }
            }
        }
        $this->log->print("\n" . "\n" . "Uploaded files: " . $this->num . "\n");
        if($this->num > 0) {
            $this->log->print("\n" . "It may be necessary to clear the cache and update the autoloader, for which the command 'composer dump-autoload'." . "\n");
        }
    }

    public function copyRecursiveFiles(string $sourceDirectory, string $destDirectory, $mode = 0755) {
        if (!file_exists($destDirectory)) {
            mkdir($destDirectory, $mode, true);
        }
        $dirIterator = new \RecursiveDirectoryIterator($sourceDirectory, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($dirIterator, \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $object) {
            $tag = "[+] CREATED";
            $destPath = $destDirectory . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            if(file_exists($destPath)) {
                $tag = '[>] UPDATED';
            }
            ($object->isDir()) ? @mkdir($destPath) : copy($object, $destPath);
            if ($object->isFile()) {
                if(file_exists($destPath)) {

                    $parts = explode('-', $destPath);
                    if(count($parts) && array_pop($parts) === 'upd') {
                        rename($destPath, $destPath = implode('-', $parts));
                    }

                    $this->log->print( "\n" . $tag . " file [" . $destPath . "]");
                    $this->num++;
                } else {
                    $this->revertAllFiles();
                }
            }
        }
    }

    protected function revertAllFiles() {
        (new RemoveLogin($this->files, $this->log, false))->run();
        $this->log->die("\n" . "Failed to copy authorization files (" . "\n");
    }
}