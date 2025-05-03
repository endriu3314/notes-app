<?php

require_once __DIR__.'/../vendor/autoload.php';

use NotesApi\Kernel;

$kernel = new Kernel;
$kernel->handle();
