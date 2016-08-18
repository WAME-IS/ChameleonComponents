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

    /** @var boolean */
    private $triggersProcessing = false;

    /** @var boolean */
    private $processed = false;

    /**
     * @param Control $control
     * @param DataDefinition[] $definitions
     */
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
    function setControl($control)
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

    /**
     * @return boolean
     */
    function isTriggersProcessing()
    {
        return $this->triggersProcessing;
    }

    /**
     * @param boolean $triggersProcessing
     */
    function setTriggersProcessing($triggersProcessing)
    {
        $this->triggersProcessing = $triggersProcessing;
        return $this;
    }

    /**
     * @return boolean
     */
    function isProcessed()
    {
        return $this->processed;
    }

    /**
     * @param type $processed
     */
    function setProcessed($processed)
    {
        $this->processed = $processed;
        return $this;
    }
}
