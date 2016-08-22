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
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
interface IDataSpacesBuilderFactory
{

    /**
     * @return DataSpacesBuilder
     * @param ControlDataDefinition[] $controlDataDefinitions
     * @param DataSpace[] $dataSpaces
     */
    public function create($controlDataDefinitions, $dataSpaces = null);
}

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
     * @param DataSpace[] $dataSpaces
     * @return DataSpace[]
     */
    public function __construct($controlDataDefinitions, $dataSpaces = null)
    {
        if (is_array($controlDataDefinitions)) {
            $this->controlDataDefinitions = $controlDataDefinitions;
        } else {
            $this->controlDataDefinitions = [$controlDataDefinitions];
        }
        if ($dataSpaces) {
            $this->dataSpaces = $dataSpaces;
        }
    }

    public function buildDataSpaces()
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveTreeDefinitionIterator($this->controlDataDefinitions), RecursiveIteratorIterator::SELF_FIRST);

        /* @var $controlDataDefinition ControlDataDefinition */
        $controlDataDefinition = null;
        foreach ($iterator as $controlDataDefinition) {
            if ($controlDataDefinition->isProcessed()) {
                continue;
            }

            $this->processControlDefinition($controlDataDefinition, $iterator->getDepth());

            $controlDataDefinition->setProcessed(true);
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
                
                if($dataSpace->getDataDefinition()->getQueryType() != $dataDefinition->getQueryType()) {
                    continue;
                }
                
                $dataSpace->setDataDefinition(Combiner::combineDataDefinitions($dataSpace->getDataDefinition(), $dataDefinition));
                return;
            }
        }

        // no same target found, create new DataSpace
        $parentDataSpace = $this->findParentDataSpace($dataDefinition, $control);
        $dataSpace = new DataSpace($control, $dataDefinition);
        if ($parentDataSpace) {
            $dataSpace->setParent($parentDataSpace);
        }

        $this->addDataSpace($dataSpace);
    }

    private function findParentDataSpace($dataDefinition, $control)
    {
        $dsgen = $this->parentDataSpaceGenerator($control);
        foreach ($dsgen as $dataSpace) {
            if ($this->canBeSameTarget($dataSpace->getDataDefinition()->getTarget(), $dataDefinition->getTarget())) {
                return $dataSpace;
            }
        }

        $dsgen = $this->parentDataSpaceGenerator($control);

        return $dsgen->current();
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
            while ($parent) {
                foreach ($this->dataSpaces as $dataSpace) {
                    if ($dataSpace->getControl() === $parent) {
                        yield $dataSpace;
                    }
                }
                $parent = $parent->getParent();
            }
        };

        return $gen();
    }

    /**
     * @param DataDefinitionTarget[] $targets
     */
    private function canBeSameTarget($target1, $target2)
    {
        return Combiner::combineTargets(true, $target1, $target2);
    }

    /**
     * Starts new dataspace, add it to list of dataSpaces and updates currentDataSpace
     */
    private function addDataSpace(DataSpace $dataSpace)
    {
        $this->dataSpaces[] = $dataSpace;
    }
}
