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

        $target = self::combineTargets(false, ...array_map(
                    function($dataDefinition) {
                    return $dataDefinition->getTarget();
                }, $dataDefinitions));
        $knownProperties = self::combineCriteria(...array_map(
                    function($dataDefinition) {
                    return $dataDefinition->getKnownProperties();
                }, $dataDefinitions));
        $queryType = self::combineQueryType(...array_map(
                    function($dataDefinition) {
                    return $dataDefinition->getQueryType();
                }, $dataDefinitions));
        $hints = self::combineHints(...array_map(
                    function($dataDefinition) {
                    return $dataDefinition->getHints();
                }, $dataDefinitions));


        if (!is_string($target->getType()) || $target->getType() == DataSpacesBuilder::ANY_TYPE_CHAR) {
            throw new InvalidArgumentException("Unable to find single target for DataSpace.");
        }

        $dataDefinition = new DataDefinition($target, $knownProperties, $queryType);
        $dataDefinition->setHints($hints);
        return $dataDefinition;
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
                if ($otype != DataSpacesBuilder::ANY_TYPE_CHAR) {
                    if (is_string($type)) {
                        if ($type != DataSpacesBuilder::ANY_TYPE_CHAR) {
                            if ($type != $otype) {
                                if ($dry) {
                                    return false;
                                } else {
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
                    if ($type == DataSpacesBuilder::ANY_TYPE_CHAR) {
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
        }

        if ($dry) {
            return true;
        } else {
            return new DataDefinitionTarget($type, $list);
        }
    }

    /**
     * Function combines query types.
     * 
     * @param string[] $queryTypes
     */
    public static function combineQueryType(...$queryTypes)
    {
        if (count($queryTypes) < 2) {
            throw new InvalidArgumentException("At least two queryTypes have to be specified.");
        }

        $queryType = array_shift($queryTypes);
        while ($q = array_shift($queryTypes)) {
            if (!$q) {
                continue;
            }
            if (!$queryType && $q) {
                $queryType = $q;
            } else if ($q != $queryType) {
                throw new InvalidArgumentException("Cannot use two different values for property 'queryType' $q and $queryType");
            }
        }

        return $queryType;
    }

    /**
     * Function combines hints.
     * 
     * @param array[] $hints
     */
    public static function combineHints(...$hints)
    {
        if (count($hints) < 2) {
            throw new InvalidArgumentException("At least two hints have to be specified.");
        }

        $newHints = array_shift($hints);
        while ($hint = array_shift($hints)) {
            $newHints = array_merge_recursive($newHints, $hint);
        }

        return $newHints;
    }

    /**
     * @param Criteria[] $criteria
     */
    public static function combineCriteria(...$criteria)
    {

        if (count($criteria) < 2) {
            self::throwCombineException("At least two criteria objects have to be specified.", $criteria);
        }

        /* @var $c \Doctrine\Common\Collections\Criteria */
        /* @var $newCriteria \Doctrine\Common\Collections\Criteria */
        $newCriteria = null;

        foreach ($criteria as $c) {
            if (!$c) {
                continue;
            }
            if (!$newCriteria) {
                $newCriteria = $c;
                continue;
            }

            //where
            if ($c->getWhereExpression()) {
                $newCriteria->andWhere($c->getWhereExpression());
            }

            //order
            if ($c->getOrderings()) {
                foreach ($c->getOrderings() as $by => $order) {
                    if (array_key_exists($by, $newCriteria->getOrderings())) {
                        self::throwCombineException("Order can be set only by one known properties.", $criteria);
                    }
                    $newCriteria->orderBy([$by => $order]);
                }
            }

            //limit
            if ($c->getFirstResult() != null && $c->getFirstResult() != $newCriteria->getFirstResult()) {
                if ($newCriteria->getFirstResult() != null) {
                    self::throwCombineException("Limit can be only set by one controls known properties.", $criteria);
                }
                $newCriteria->setFirstResult($c->getFirstResult());
            }
            if ($c->getMaxResults() != null && $c->getMaxResults() != $newCriteria->getMaxResults()) {
                if ($newCriteria->getMaxResults() != null) {
                    self::throwCombineException("Limit can be only set by one controls known properties.", $criteria);
                }
                $newCriteria->setMaxResults($c->getMaxResults());
            }
        }

        return $newCriteria;
    }

    private static function throwCombineException($message, $criteria)
    {
        $e = new \Nette\InvalidArgumentException($message);
        $e->criteria = $criteria;
        throw $e;
    }
}
