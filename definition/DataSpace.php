<?php

namespace Wame\ChameleonComponents\Definition;

use Nette\Application\UI\Control;
use Nette\Object;

/**
 * Components are splited into DataSpaces by DataSpacesBuilder. Each DataSpace
 * represents components with shared properties.
 *
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class DataSpace extends Object
{

    use TreeDefinitionTrait;

    /** @var Control */
    private $control;

    /** @var DataDefinition */
    private $dataDefinition;

    /**
     * @param Control $control
     * @param DataDefinition $dataDefinition
     */
    public function __construct(Control $control = null, DataDefinition $dataDefinition = null)
    {
        $this->control = $control;
        $this->dataDefinition = $dataDefinition;
    }

    /**
     * @return Control
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * @param Control $control
     */
    function setControl(Control $control)
    {
        $this->control = $control;
        return $this;
    }

    /**
     * @return DataDefinition
     */
    public function getDataDefinition()
    {
        return $this->dataDefinition;
    }

    /**
     * @param DataDefinition $dataDefinition
     */
    function setDataDefinition(DataDefinition $dataDefinition)
    {
        $this->dataDefinition = $dataDefinition;
        return $this;
    }
}
