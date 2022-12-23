<?php

Route::get('/standard-controller/')->controller('ExampleDirectory\TestUpdaterController');

Route::get('/module-controller/')->modules('example-directory', 'Controller');


