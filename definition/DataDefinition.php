<?php

namespace Wame\ChameleonComponents\Definition;

use Doctrine\Common\Collections\Criteria;
use Nette\Object;

class DataDefinition extends Object
{

    /** @var DataDefinitionTarget */
    public $target;

    /** @var Criteria */
    public $knownProperties;

    /**
     * @param DataDefinitionTarget $target
     * @param Criteria $knownProperties
     */
    public function __construct($target = null, $knownProperties = null)
    {
        $this->target = $target;
        $this->knownProperties = $knownProperties;
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
}
