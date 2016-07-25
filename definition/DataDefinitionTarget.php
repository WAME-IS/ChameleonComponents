<?php

namespace Wame\ChameleonComponents\Definition;

class DataDefinitionTarget
{

    /** @var string|string[] Class name */
    public $type;

    /** @var boolean */
    public $list;
    
    /** @var boolean */
    public $multiple;

    /**
     * Constructs new DataDefinitionTarget
     * 
     * @param string|string[] $type
     * @param boolean $multiple
     */
    public function __construct($type, $list = false, $multiple = false)
    {
        $this->type = $type;
        $this->list = $list;
        $this->multiple = $multiple;
    }

    /**
     * Returns known type or array of possible known types
     * @return string|string[] Class name
     */
    function getType()
    {
        return $this->type;
    }

    /**
     * @return boolean
     */
    function isList()
    {
        return $this->list;
    }
    
    /**
     * @return boolean
     */
    function isMultiple()
    {
        return $this->multiple;
    }

    
    /**
     * @return boolean
     */
    function getList()
    {
        return $this->list;
    }
    
    /**
     * @return boolean
     */
    function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * @param string $type Class name
     */
    function setType($type)
    {
        $this->type = $type;
    }
    
    /**
     * @param boolean $list
     */
    function setList($list)
    {
        $this->list = $list;
    }

    /**
     * @param boolean $multiple
     */
    function setMultiple($multiple)
    {
        $this->multiple = $multiple;
    }
}
