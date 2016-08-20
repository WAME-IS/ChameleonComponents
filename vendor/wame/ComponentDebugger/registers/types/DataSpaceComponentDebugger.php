<?php

namespace Wame\ChameleonComponents\Vendor\Wame\ComponentDebugger\Registers\Types;

use RecursiveIteratorIterator;
use Wame\ChameleonComponents\Definition\RecursiveTreeDefinitionIterator;
use Wame\ChameleonComponents\IO\DataLoaderControl;
use Wame\ChameleonComponents\IO\DataLoaderPresenterListener;
use Wame\ComponentDebugger\Registers\Types\IComponentDebuggerType;

class DataSpaceComponentDebugger implements IComponentDebuggerType
{

    /** @var DataLoaderPresenterListener */
    private $dataLoaderPresenterListener;

    public function __construct(DataLoaderPresenterListener $dataLoaderPresenterListener)
    {
        $this->dataLoaderPresenterListener = $dataLoaderPresenterListener;
    }

    public function getControlType()
    {
        return DataLoaderControl::class;
    }

    public function getTitle()
    {
        return "Chameleon definition";
    }

    public function getBorderColor()
    {
        return null;
    }

    public function getControlData($control)
    {
        if ($this->dataLoaderPresenterListener->getDataDefinitions()) {
            $iterator = new RecursiveIteratorIterator(new RecursiveTreeDefinitionIterator($this->dataLoaderPresenterListener->getDataDefinitions()), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($iterator as $controlDataDefinition) {
                if ($controlDataDefinition->getControl() === $control) {
                    return $controlDataDefinition->getDataDefinitions();
                }
            }
        }
    }
}
