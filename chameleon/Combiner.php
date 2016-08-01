<?php

namespace Wame\ChameleonComponents;

use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use WebLoader\InvalidArgumentException;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class Combiner
{

    /**
     * @param DataDefinition[] $dataDefinitions
     */
    public static function combineDataDefinitions(...$dataDefinitions)
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
     * Function combines DataDefinitionTargets.
     * When $dry parameter is set to TRUE no exceptions will be thrown but succesfulnes result is returned insted (TRUE/FALSE).
     * 
     * @param boolean $dry
     * @param DataDefinitionTarget[] $targets
     */
    public static function combineTargets($dry, ...$targets)
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
    public static function combineCriteria(...$criteria)
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
}
