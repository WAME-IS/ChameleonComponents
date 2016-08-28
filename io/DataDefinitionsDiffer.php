<?php

namespace Wame\ChameleonComponents\IO;

use RecursiveIteratorIterator;
use SplObjectStorage;
use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\ChameleonComponents\Definition\RecursiveTreeDefinitionIterator;

class DataDefinitionsDiffer
{

    /**
     * @param ControlDataDefinition[] $previousDataDefinitions
     * @param ControlDataDefinition[] $dataDefinitions
     * @return ControlDataDefinition[]
     */
    public function diff($previousDataDefinitions, $dataDefinitions)
    {
        $changedDefinitions = [];

        $previousControlDefinitionMap = $this->getControlDefinitionMap($previousDataDefinitions);
        $controlDefinitionMap = $this->getControlDefinitionMap($dataDefinitions);

        $previousControlDefinitionMap->removeAllExcept($controlDefinitionMap);

        foreach ($previousControlDefinitionMap as $control) {
            $controlDefinition = $controlDefinitionMap->offsetGet($control);
            if ($this->diffControlDataDefinitions($previousControlDefinitionMap->offsetGet($control), $controlDefinition)) {
                $changedDefinitions[] = $controlDefinition;
            }
        }

//        dump($changedDefinitions);
        
        return $changedDefinitions;
    }

    /**
     * @param ControlDataDefinition[] $controlDataDefinitions
     * @return SplObjectStorage
     */
    private function getControlDefinitionMap($controlDataDefinitions)
    {
        $controlDefinitionMap = new SplObjectStorage();

        $iterator = new RecursiveIteratorIterator(new RecursiveTreeDefinitionIterator($controlDataDefinitions), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $controlDataDefinition) {
            $controlDefinitionMap->attach($controlDataDefinition->getControl(), $controlDataDefinition);
        }

        return $controlDefinitionMap;
    }

    /**
     * 
     * @param ControlDataDefinition $controlDataDefinition1
     * @param ControlDataDefinition $controlDataDefinition2
     * @return boolean Whenever there is a difference
     */
    private function diffControlDataDefinitions($controlDataDefinition1, $controlDataDefinition2)
    {
        if (count($controlDataDefinition1->getDataDefinitions()) != count($controlDataDefinition2->getDataDefinitions())) {
            return true;
        }
        foreach ($controlDataDefinition1->getDataDefinitions() as $dataDefinition) {
            if (!in_array($dataDefinition, $controlDataDefinition2->getDataDefinitions())) {
                return true;
            }
        }
        return false;
    }
}
