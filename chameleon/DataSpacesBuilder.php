<?php

namespace Wame\ChameleonComponents;

use Nette\Application\UI\Control;
use RecursiveIteratorIterator;
use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponents\Definition\RecursiveTreeDefinitionIterator;

/**
 * Class used to split website into "DataSpaces" and combine known paramters.
 *
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class DataSpacesBuilder
{

    const ANY_TYPE_CHAR = '*';

    /** @var ControlDataDefinition[] */
    private $controlDataDefinitions;

    /** @var DataSpace[] */
    private $dataSpaces = [];

    /**
     * 
     * @param ControlDataDefinition[]|ControlDataDefinition $controlDataDefinitions
     * @return DataSpace[]
     */
    public function __construct($controlDataDefinitions)
    {
        if (is_array($controlDataDefinitions)) {
            $this->controlDataDefinitions = $controlDataDefinitions;
        } else {
            $this->controlDataDefinitions = [$controlDataDefinitions];
        }
    }

    public function buildDataSpaces()
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveTreeDefinitionIterator($this->controlDataDefinitions), RecursiveIteratorIterator::SELF_FIRST);

        /* @var $controlDataDefinition \Wame\ChameleonComponents\Definition\ControlDataDefinition */
        $controlDataDefinition = null;
        foreach ($iterator as $controlDataDefinition) {
            $this->processControlDefinition($controlDataDefinition, $iterator->getDepth());
        }

//        $this->validateDataSpaces();

        return $this->dataSpaces;
    }

    /**
     * @param ControlDataDefinition $controlDataDefinition
     */
    private function processControlDefinition($controlDataDefinition, $depth)
    {
        foreach ($controlDataDefinition->getDataDefinitions() as $dataDefinition) {
            $this->processDefinition($dataDefinition, $controlDataDefinition->getControl());
        }
    }

    /**
     * 
     * @param DataDefinition $dataDefinition
     * @param Control $control
     */
    private function processDefinition($dataDefinition, $control)
    {
        $dsgen = $this->parentDataSpaceGenerator($control);

        foreach ($dsgen as $dataSpace) {
            if ($this->canBeSameTarget($dataSpace->getDataDefinition()->getTarget(), $dataDefinition->getTarget())) {
                $dataSpace->setDataDefinition(Combiner::combineDataDefinitions($dataSpace->getDataDefinition(), $dataDefinition));
                return;
            }
        }

        //create new dataSpace
        $this->addDataSpace(new DataSpace($control, $dataDefinition));
    }

    /**
     * 
     * @param Control $control
     */
    private function parentDataSpaceGenerator($control)
    {

        $gen = function() use ($control) {
            //TOOD improve performance?
            $parent = $control;
            while ($parent = $parent->getParent()) {
                foreach ($this->dataSpaces as $dataSpace) {
                    if ($dataSpace->getControl() === $parent) {
                        yield $dataSpace;
                    }
                }
            }
        };

        return $gen();
    }

    /**
     * @param DataDefinitionTarget[] $targets
     */
    private function canBeSameTarget(...$targets)
    {
        return Combiner::combineTargets(true, ...$targets);
    }

    /**
     * Starts new dataspace, add it to list of dataSpaces and updates currentDataSpace
     */
    private function addDataSpace(DataSpace $dataSpace)
    {
        $this->dataSpaces[] = $dataSpace;
    }
}
