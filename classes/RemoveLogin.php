<?php

namespace Phphleb\Updater\Classes;

class RemoveLogin
{
    protected $files;

    protected $log;

    protected $origin;
    
    function __construct(SearchDirections $files, LogInterface $log, bool $origin = true) {
        $this->files = $files;
        $this->log = $log;
        $this->origin = $origin;
    }  

    public function run() {
        if($this->origin) {
            if ($this->confirm('Remove authorization from the project?') === false) {
                return;
            }
        }

        $list = $this->files->getActualList();

        if(!count($list) && $this->origin){
            $this->log->die( "\n" . "No files found for deletion" . "\n");
        }
        $count = 0;
        foreach($list as $path){
            if($this->deleteDirectory($path)) {
                if($this->origin) {
                    $this->log->print("\n" . "[-] DELETE directory [" . $path . DIR_S . "*]");
                    $count++;
                }
            }
        }
        if($this->origin) {
            $this->log->print("\n" . "Delete directories: " . $count . "\n");
        }
    }
    
    protected function confirm($message){
        $confirmation = $this->log->readline($message . " Enter Y(yes) or N(no)>");
        if($confirmation == 'Y'){
            return true;
        }
        if($confirmation == 'N'){
            return false;
        }
        return $this->confirm($message);
    }

    protected function deleteDirectory(string $path) {
        if (file_exists($path)) {
            $files = array_diff(scandir($path), ['.', '..']);
            foreach ($files as $file) {
                (is_dir($path . '/' . $file)) ? $this->deleteDirectory($path . '/' . $file) : unlink($path . '/' . $file);
            }
            return rmdir($path);
        }
    }
}