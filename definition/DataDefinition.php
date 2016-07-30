<?php

namespace Wame\ChameleonComponents\Definition;

use Doctrine\Common\Collections\Criteria;
use Nette\Object;

class DataDefinition extends Object
{

    /** @var DataDefinitionTarget */
    private $target;

    /** @var Criteria */
    private $knownProperties;

    /** @var array [string => mixed] */
    private $hints;

    /**
     * @param DataDefinitionTarget $target
     * @param Criteria $knownProperties
     */
    public function __construct($target = null, $knownProperties = null)
    {
        $this->target = $target;
        $this->knownProperties = $knownProperties;
        $this->hints = [];
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
            if($checkType && !is_a($hint, $checkType)) {
                throw new \Nette\InvalidArgumentException("Invalid hint type! It has to be $checkType");
            }
            return $hint;
        }
    }

    /**
     * @param DataDefinitionTarget $target
     */
    public function setTarget(DataDefinitionTarget $target)
    {
        $this->target = $target;
    }

    /**
     * @param Criteria $knownProperties
     */
    public function setKnownProperties($knownProperties)
    {
        $this->knownProperties = $knownProperties;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setHint($name, $value)
    {
        $this->hints[$name] = $value;
    }
}
