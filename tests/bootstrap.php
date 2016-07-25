<?php

require __DIR__ . '/../../../autoload.php';

use Tester\Environment;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\ChameleonComponents\IO\DataLoaderControl;

Environment::setup();
date_default_timezone_set('Europe/Prague');

class A
{
    
}

class TestChameleonControlA extends \Nette\Application\UI\Control implements DataLoaderControl
{
    public function getDataDefinition()
    {
        return new DataDefinition(new DataDefinitionTarget(A::class, false));
    }
}
