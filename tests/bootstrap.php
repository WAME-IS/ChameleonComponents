<?php
require __DIR__ . '/../../../autoload.php';

use Nette\Caching\Storages\FileStorage;
use Nette\Loaders\RobotLoader;
use Tester\Environment;

$loader = new RobotLoader;
$loader->addDirectory(__DIR__ . '/../');
$loader->setCacheStorage(new FileStorage(__DIR__ . '/../../../../temp/cache'));
$loader->register();


Environment::setup();
date_default_timezone_set('Europe/Prague');
