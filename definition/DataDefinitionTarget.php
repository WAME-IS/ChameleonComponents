<?php

namespace Wame\ChameleonComponents\Definition;

use Nette\Object;
use Wame\Utils\Strings;

class DataDefinitionTarget extends Object
{

    /** @var string|string[] Class name */
    private $type;

    /** @var boolean */
    private $list;
    
    /** @var boolean */
    private $multiple;

    /**
     * Constructs new DataDefinitionTarget
     * 
     * @param string|string[] $type
     * @param boolean $multiple
     */
    public function __construct($type, $list = null, $multiple = null)
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
    
    /**
     * @return string
     */
    public function getStatusName()
    {
        $name = $this->type;
        if($this->isList()) {
            $name = Strings::plural($name);
        }
        return $name;
    }
}
