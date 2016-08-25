<?php

namespace Wame\ChameleonComponents\Definition;

use Doctrine\Common\Collections\Criteria;
use Nette\InvalidArgumentException;
use Nette\Object;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class DataDefinition extends Object
{

    const DEFAULT_QUERY_TYPE = 'select';

    /** @var DataDefinitionTarget */
    private $target;

    /** @var string */
    private $queryType;

    /** @var Criteria */
    private $knownProperties;

    /** @var array [string => mixed] */
    private $hints;
    
    /** @var callable[] */
    public $onProcess;

    /**
     * @param DataDefinitionTarget $target
     * @param Criteria $knownProperties
     * @param string $queryType
     */
    public function __construct($target = null, $knownProperties = null, $queryType = null)
    {
        $this->target = $target;
        $this->knownProperties = $knownProperties;
        $this->hints = [];
        $this->queryType = $queryType;
    }

    /**
     * @return DataDefinitionTarget
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return Criteria
     */
    public function getKnownProperties()
    {
        return $this->knownProperties;
    }

    /**
     * @return array [string => mixed]
     */
    public function getHints()
    {
        return $this->hints;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getHint($name, $checkType = null)
    {
        if (isset($this->hints[$name])) {
            $hint = $this->hints[$name];
            if ($checkType && !is_a($hint, $checkType)) {
                throw new InvalidArgumentException("Invalid hint type! It has to be $checkType");
            }
            return $hint;
        }
    }

    /**
     * @return string
     */
    function getQueryType()
    {
        return $this->queryType;
    }

    /**
     * @param DataDefinitionTarget $target
     */
    public function setTarget(DataDefinitionTarget $target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @param Criteria $knownProperties
     */
    public function setKnownProperties($knownProperties)
    {
        $this->knownProperties = $knownProperties;
        return $this;
    }

    /**
     * @param array $hints
     */
    public function setHints($hints)
    {
        $this->hints = $hints;
        return $this;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     */
    public function setHint($name, $value)
    {
        $this->hints[$name] = $value;
        return $this;
    }

    /**
     * @param string $queryType
     */
    public function setQueryType($queryType)
    {
        if ($queryType == self::DEFAULT_QUERY_TYPE) {
            $queryType = null;
        }
        $this->queryType = $queryType;
        return $this;
    }
}
