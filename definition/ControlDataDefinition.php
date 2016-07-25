<?php

namespace Wame\ChameleonComponents\Definition;

use Nette\Application\UI\Control;

class ControlDataDefinition
{

    /** @var Control */
    public $control;

    /** @var DataDefinition[] */
    private $definitions;

    /** @var ControlDataDefinition[] */
    public $childs = [];

    public function __construct($control, $definitions = null)
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
    function getChilds()
    {
        return $this->childs;
    }

    /**
     * @param DataDefinition[] $childs
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
     * @param DataDefinition[] $childs
     */
    function setChilds($childs)
    {
        $this->childs = $childs;
    }
}
