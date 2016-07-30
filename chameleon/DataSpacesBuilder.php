<?php
use Doctrine\Common\Collections\Criteria;
use Nette\Application\UI\Control;
use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponents\Definition\RecursiveTreeDefinitionIterator;

namespace Wame\ChameleonComponents;

class DataSpacesBuilder
{

    const ANY_TYPE_CHAR = '*';

    /** @var ControlDataDefinition[] */
    private $controlDataDefinitions;

    /** @var DataSpace[] */
    private $dataSpaces;

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

        $controlDataDefinition = null;
        foreach ($iterator as $controlDataDefinition) {
            $this->processControlDefinition($controlDataDefinition);
        }

//        $this->validateDataSpaces();

        return $this->dataSpaces;
    }

    /**
     * @param ControlDataDefinition $controlDataDefinition
     */
    private function processControlDefinition($controlDataDefinition)
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
                $dataSpace->setDataDefinition($this->mergeDataDefinitions($dataSpace->getDataDefinition(), $dataDefinition));
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
        return $this->intersectTargets(true, ...$targets);
    }

    /**
     * Starts new dataspace, add it to list of dataSpaces and updates currentDataSpace
     */
    private function addDataSpace(DataSpace $dataSpace)
    {
        $this->dataSpaces[] = $dataSpace;
    }

    /**
     * @param DataDefinition[] $dataDefinitions
     */
    private function mergeDataDefinitions(...$dataDefinitions)
    {
        if (count($dataDefinitions) < 2) {
            throw new InvalidArgumentException("At least two DataDefinitions have to be specified.");
        }

        $target = new DataDefinitionTarget("*");
        $knownProperties = null;

        foreach ($dataDefinitions as $dataDefinition) {
            $target = $this->intersectTargets(false, $target, $dataDefinition->getTarget());
            $knownProperties = $this->mergeCriteria($knownProperties, $dataDefinition->getKnownProperties());
        }

        if (!is_string($target->getType()) || $target->getType() == self::ANY_TYPE_CHAR) {
            throw new InvalidArgumentException("Unable to find single target for DataSpace.");
        }

        return new DataDefinition($target, $knownProperties);
    }

    /**
     * @param boolean $dry
     * @param DataDefinitionTarget[] $targets
     */
    private function intersectTargets($dry, ...$targets)
    {
        if (count($targets) < 2) {
            throw new InvalidArgumentException("At least two tarets have to be specified.");
        }

        $target = array_shift($targets);
        $type = $target->getType();
        $list = $target->isList();
        $multiple = $target->isMultiple();

        if (!is_string($type) && !is_array($type)) {
            if ($dry) {
                return false;
            } else {
                throw new InvalidArgumentException("Invalid target type");
            }
        }

        while ($target = array_shift($targets)) {

            /*
             * Type
             */
            $otype = $target->getType();
            if (is_string($otype)) {
                if ($otype != self::ANY_TYPE_CHAR) {
                    if (is_string($type)) {
                        if ($type != self::ANY_TYPE_CHAR) {
                            var_dump($type, $otype);
                            if ($type != $otype) {
                                if ($dry) {
                                    return false;
                                } else {
                                    var_dump("throw");
                                    throw new InvalidArgumentException("Cannot use two different specificc types $type and $otype");
                                }
                            }
                        } else {
                            $type = $otype;
                        }
                    } elseif (is_array($type)) {
                        if (in_array($otype, $type)) {
                            $type = $otype;
                        } else {
                            if ($dry) {
                                return false;
                            } else {
                                throw new InvalidArgumentException("Cannot use two different types [" . implode(", ", $type) . "] and $otype");
                            }
                        }
                    }
                }
            } elseif (is_array($otype)) {
                if (is_string($type)) {
                    if ($type == self::ANY_TYPE_CHAR) {
                        $type = $otype;
                    } else {
                        if (!in_array($type, $otype)) {
                            if ($dry) {
                                return false;
                            } else {
                                throw new InvalidArgumentException("Cannot use two different types $type and [" . implode(", ", $otype) . "]");
                            }
                        }
                    }
                } elseif (is_array($type)) {
                    $intersectType = array_intersect($type, $otype);
                    if (!$intersectType) {
                        if ($dry) {
                            return false;
                        } else {
                            throw new InvalidArgumentException("Cannot use two different types [" . implode(", ", $type) . "] and [" . implode(", ", $otype) . "]");
                        }
                    }
                    $type = $intersectType;
                }
            } else {
                throw new InvalidArgumentException("Invalid target type");
            }

            /*
             * List
             */
            if ($list == null) {
                $list = $target->isList();
            } else {
                if ($list != $target->isList()) {
                    if ($dry) {
                        return false;
                    } else {
                        throw new InvalidArgumentException("Cannot use two different values for property 'list' $list and {$target->isList()}");
                    }
                }
            }

            /*
             * Multiple
             */
            if ($multiple == null) {
                $multiple = $target->isMultiple();
            } else {
                if ($multiple != $target->isMultiple()) {
                    if ($dry) {
                        return false;
                    } else {
                        throw new InvalidArgumentException("Cannot use two different values for property 'multiple' $multiple and {$target->isMultiple()}");
                    }
                }
            }
        }

        if ($dry) {
            return true;
        } else {
            return new DataDefinitionTarget($type, $list, $multiple);
        }
    }

    /**
     * @param Criteria[] $criteria
     */
    private function mergeCriteria(...$criteria)
    {
        if (count($criteria) < 2) {
            throw new InvalidArgumentException("At least two criteria objects have to be specified.");
        }

        $newCriteria = null;

        foreach ($criteria as $c) {
            if (!$c) {
                continue;
            }
            if (!$newCriteria) {
                $newCriteria = $c;
                continue;
            }
            if ($c->getWhereExpression()) {
                $newCriteria->andWhere($c->getWhereExpression());
            }
            if ($c->getOrderings()) {
                $newCriteria->orderBy($c->getOrderings());
            }
//            if($->get);
            if ($newCriteria->getFirstResult()) {
                $newCriteria->setFirstResult($firstResult);
            }
        }

        return $newCriteria;
    }
//    private function validateDataSpaces()
//    {
//        foreach ($this->dataSpaces as $dataSpace) {
//            $this->validateDataSpace($dataSpace);
//        }
//    }

    /**
     * 
     * @param DataSpace $dataSpace
     */
//    private function validateDataSpace($dataSpace)
//    {
//        $target = $dataSpace->getDataDefinition()->getTarget();
//        if ($target->isMultiple()) {
//            $parentDataSpace = $dataSpace->getParent();
//            if (!$parentDataSpace || !$parentDataSpace->getDataDefinition()->getTarget()->isList()) {
//                throw new InvalidArgumentException("Controls used for displaying list has to have some underlaying list!");
//            }
//        }
//    }
}
