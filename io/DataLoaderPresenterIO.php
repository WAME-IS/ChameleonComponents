<?php

namespace Wame\ChameleonComponents\IO;

use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Object;
use Wame\ChameleonComponents\DataLoader;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class DataLoaderPresenterIO extends Object
{

    /** @var DataLoader */
    private $dataLoader;

    public function __construct(DataLoader $dataLoader)
    {
        $this->dataLoader = $dataLoader;
    }

    public function load(Presenter $presenter)
    {
        $dataDefinitions = $this->readDataDefinitions($presenter);

        $dataSpaces = $this->dataLoader->processDataDefinitions($dataDefinitions);

        foreach ($dataSpaces as $dataSpace) {
            $this->bindResult($dataSpace->getTopControls(), $dataSpace->getData());
        }
    }

    private function bindResult($controls, $data)
    {
        foreach ($controls as $control) {
            
        }
    }

    /**
     * Reads DataDefinitions from control and its childs
     * 
     * @param Control $control
     * @return array
     */
    private function readDataDefinitions(Control $control)
    {

        $dataDefinition = null;
        if ($control instanceof DataLoaderControl) {
            $dataDefinition = $control->getDataDefinition();
        }

        $childDataDefinitions = [];
        foreach ($control->getComponents() as $subcontrol) {
            $childDataDefinitions = array_merge($childDataDefinitions, $this->readDataDefinitions($subcontrol));
        }

        if ($dataDefinition) {
            $dataDefinition->setChilds($childDataDefinitions);
            return [$dataDefinition];
        } else {
            return $childDataDefinitions;
        }
    }
}
