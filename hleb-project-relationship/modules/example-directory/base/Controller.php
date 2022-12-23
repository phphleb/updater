<?php


namespace Modules\ExampleDirectory;

/**
 * Demo controller for the module.
 */
class Controller
{
    public function index(): array
    {
        return view('templates/content', ['moduleController' => __CLASS__]);
    }
}

