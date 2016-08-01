<?php

namespace Wame\ChameleonComponents\Definition;

use Nette\Application\UI\Control;
use Nette\Object;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class ControlDataDefinition extends Object
{

    use TreeDefinitionTrait;

    /** @var Control */
    private $control;

    /** @var DataDefinition[] */
    private $definitions;

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
}
