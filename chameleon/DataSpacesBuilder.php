<?php

namespace Wame\ChameleonComponents;

use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataSpace;

class DataSpacesBuilder
{

    /**
     * 
     * @param Definition\ControlDataDefinition[] $controlDataDefinition
     * @return DataSpace[]
     */
    public function buildDataSpaces($dataDefinition)
    {
        // 1. Create DataSpaces
        $this->splitToDataSpaces($dataDefinition);

        // 2.
        $this->unifyTargets($dataDefinition);
    }

    /**
     * 
     * @param DataDefinition $dataDefinition
     */
    private function unifyTargets($dataDefinition)
    {
        if (is_array($dataDefinition)) {
            foreach ($dataDefinition as $dd) {
                $this->unifyTargets($dd);
            }
        }

        $target = $dataDefinition->getTarget();
        if (is_array($target->getType())) {
            
        }
    }

    /**
     * 
     * @param DataDefinition $dataDefinition
     */
    private function splitToDataSpaces($dataDefinition, $parentDataSpace = null)
    {   
        foreach ($dataDefinition->getTargets() as $target => $knownProperties) {
            $target->
        }
    }

}
