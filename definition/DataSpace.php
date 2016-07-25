<?php

namespace Wame\ChameleonComponents\Definition;

use Nette\Application\UI\Control;

/**
 * Components are splited into DataSpaces by DataSpacesBuilder. Each DataSpace
 * represents components with shared variables.
 */
class DataSpace
{

    /** @var Control[] */
    private $controls;

    /** @var DataDefinition[] */
    private $dataDefinitions;

    /** @var array */
    private $data;

    /**
     * @param DataDefinition[] $dataDefinition
     * @param Control[] $controls
     */
    public function __construct($dataDefinition = null, $controls = null)
    {
        if ($dataDefinition) {
            $this->dataDefinition = $dataDefinition;
        } else {
            $this->dataDefinition = [];
        }
        if ($controls) {
            $this->controls = $controls;
        } else {
            $this->controls = [];
        }
    }

    /**
     * @return Control[]
     */
    public function getControls()
    {
        return $this->controls;
    }
    
    public function getTopControls()
    {
        $topControls = [];
        foreach($this->controls as $control) {
            $parent = $control->getParent();
            while($parent) {
                if(in_array($parent, $this->controls)) {
                    continue;
                }
                $control->getParent();
            }
        }
        return $topControls;
    }

    /**
     * @return DataDefinition[]
     */
    public function getDataDefinitions()
    {
        return $this->dataDefinitions;
    }

    /**
     * @return array
     */
    function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    function setData($data)
    {
        $this->data = $data;
    }
}
