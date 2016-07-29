<?php

require __DIR__ . '/../../../autoload.php';

$loader = new Nette\Loaders\RobotLoader;
$loader->addDirectory(__DIR__.'/../');
$loader->setCacheStorage(new Nette\Caching\Storages\FileStorage(__DIR__.'/../../../../temp'));
$loader->register();

use Nette\Application\UI\Control;
use Tester\Environment;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\ChameleonComponents\IO\DataLoaderControl;

Environment::setup();
date_default_timezone_set('Europe/Prague');

class A
{
    
}

class B
{
    
}

class TestChameleonControlA extends Control implements DataLoaderControl
{
    public function getDataDefinition()
    {
        return new DataDefinition(new DataDefinitionTarget(A::class, false));
    }
}
