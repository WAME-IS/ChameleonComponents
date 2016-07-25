<?php

namespace Wame\ChameleonComponents\IO;

class DataLoaderPresenterIO extends \Nette\Object
{
    
    /** @var \Wame\DataLoader\Model\DataLoader */
    private $dataLoader;
    
    public function __construct(\Wame\DataLoader\Model\DataLoader $dataLoader)
    {
        $this->dataLoader = $dataLoader;
    }
    
    public function load(\Nette\Application\UI\Presenter $presenter)
    {
        $dataDefinitions = $this->readDataDefinitions($presenter);
        
        $dataSpaces = $this->dataLoader->processDataDefinitions($dataDefinitions);
        
        foreach($dataSpaces as $dataSpace) {
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
     * @param \Nette\Application\UI\Control $control
     * @return array
     */
    private function readDataDefinitions(\Nette\Application\UI\Control $control) {
        
        $dataDefinition = null;
        if($control instanceof DataLoaderControl) {
            $dataDefinition = $control->getDataDefinition();
        }
        
        $childDataDefinitions = [];
        foreach($control->getComponents() as $subcontrol) {
            $childDataDefinitions = array_merge($childDataDefinitions, $this->readDataDefinitions($subcontrol));
        }
        
        if($dataDefinition) {
            $dataDefinition->setChilds($childDataDefinitions);
            return [$dataDefinition];
        } else {
            return $childDataDefinitions;
        }
    }
    
}
