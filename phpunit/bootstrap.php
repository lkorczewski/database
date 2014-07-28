<?php

require_once __DIR__ . '/../../core/autoloader.php';

use Core\Autoloader;

Autoloader::register(__DIR__ . '/../..');

spl_autoload_register('spl_autoload');

