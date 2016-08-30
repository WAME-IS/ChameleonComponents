<?php

namespace Wame\ChameleonComponents\IO;

use Nette\Application\UI\Control;
use Nette\Object;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataSpace;

class AutomaticCache extends Object
{

    public function __construct()
    {
        
    }

    /**
     * @param \Wame\ChameleonComponents\Definition\ControlDataDefinition[] $controlDataDefinitions
     */
    public function bindCacheNames($controlDataDefinitions)
    {
        if ($controlDataDefinitions) {
            foreach ($controlDataDefinitions as $controlDataDefinition) {
                $this->bindControlCacheName($controlDataDefinition->getControl(), $controlDataDefinition->getDataDefinitions());
            }
        }
    }

    /**
     * @param Control $control
     * @param DataDefinition[] $dataDefinitions
     */
    private function bindControlCacheName($control, $dataDefinitions)
    {
        $controlState = [];
        $controlState[] = [$dataDefinitions->getTarget(), $dataDefinitions->getKnownProperties(), $dataDefinitions->getHints(), $dataDefinitions->getQueryType()];
        $controlState[] = $control->getParameters();
        $controlStateHash = md5(serialize($controlState));
        
        $cache = $control->getComponentCache();
        $cache->setName($cache->getName() . '-' . $controlStateHash);
    }
    
    /**
     * @param DataSpace[] $dataSpaces
     */
    public function bindCacheTags($dataSpaces)
    {
        if ($dataSpaces) {
            foreach ($dataSpaces as $dataSpace) {
                $this->bindControlCacheTags($dataSpace->getControl(), $dataSpace->getDataDefinition());
            }
        }
    }

    /**
     * @param Control $control
     * @param DataDefinition $dataDefinition
     */
    private function bindControlCacheTags($control, $dataDefinition)
    {
        $control->getComponentCache()->addTag($dataDefinition->getTarget()->getType());
    }
}
