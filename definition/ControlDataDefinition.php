<?php

namespace Wame\ChameleonComponents\Definition;

use Nette\Application\UI\Control;
use Nette\Object;

class ControlDataDefinition extends Object
{

    /** @var Control */
    public $control;

    /** @var DataDefinition[] */
    private $definitions;

    /** @var ControlDataDefinition[] */
    public $children = [];

    /**
     * @param Control $control
     * @param DataDefinition[] $definitions
     */
    public function __construct(Control $control, $definitions = null)
    {
        $this->control = $control;
        if ($definitions) {
            if (is_array($definitions)) {
                $this->definitions = $definitions;
            } else {
                $this->definitions[] = $definitions;
            }
        }
    }

    /**
     * @return Control
     */
    function getControl()
    {
        return $this->control;
    }

    /**
     * @param Control $control
     */
    function setControl(Control $control)
    {
        $this->control = $control;
    }

    /**
     * @return DataDefinition[]
     */
    function getChildren()
    {
        return $this->children;
    }

    /**
     * @param DataDefinition[] $definitions
     */
    function setDataDefinitions($definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * @return DataDefinition[]
     */
    function getDataDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @param DataDefinition[] $children
     */
    function setChildren($children)
    {
        $this->children = $children;
    }
}
