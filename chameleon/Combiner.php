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

        $target = new DataDefinitionTarget(DataSpacesBuilder::ANY_TYPE_CHAR);
        $knownProperties = null;

        foreach ($dataDefinitions as $dataDefinition) {
            $target = self::combineTargets(false, $target, $dataDefinition->getTarget());
            $knownProperties = self::combineCriteria($knownProperties, $dataDefinition->getKnownProperties());
        }

        if (!is_string($target->getType()) || $target->getType() == DataSpacesBuilder::ANY_TYPE_CHAR) {
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
        $queryType = null;

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
            
            /*
             * QueryType
             */
            if ($queryType == null) {
                $queryType = $target->getQueryType();
            } else {
                if ($queryType != $target->getQueryType()) {
                    if ($dry) {
                        return false;
                    } else {
                        throw new InvalidArgumentException("Cannot use two different values for property 'queryType' $queryType and {$target->getQueryType()}");
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
     * @param Criteria[] $criteria
     */
    public static function combineCriteria(...$criteria)
    {

        if (count($criteria) < 2) {
            throw new InvalidArgumentException("At least two criteria objects have to be specified.");
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
                        throw new \Nette\InvalidArgumentException("Order can be set only by one known properties.");
                    }
                    $newCriteria->orderBy([$by => $order]);
                }
            }

            //limit
            if ($c->getFirstResult() != null) {
                if ($newCriteria->getFirstResult() != null) {
                    throw new \Nette\InvalidArgumentException("Limit can be set only by one known properties.");
                }
                $newCriteria->setFirstResult($c->getFirstResult());
            }
            if ($c->getMaxResults() != null) {
                if ($newCriteria->getMaxResults() != null) {
                    throw new \Nette\InvalidArgumentException("Limit can be set only by one known properties.");
                }
                $newCriteria->setMaxResults($c->getMaxResults());
            }
        }

        return $newCriteria;
    }
}
