<?php

namespace Wame\ChameleonComponents\Definition;

use Nette\Object;
use Wame\Utils\Strings;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class DataDefinitionTarget extends Object
{

    /** @var string|string[] */
    private $type;

    /** @var boolean */
    private $list;

    /**
     * Constructs new DataDefinitionTarget
     * 
     * @param string|string[] $type
     */
    public function __construct($type, $list = null)
    {
        $this->type = $type;
        $this->list = $list;
    }

    /**
     * Returns known type or array of possible known types
     * 
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
    function getList()
    {
        return $this->list;
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
     * @return string
     */
    public function getStatusName()
    {
        $name = $this->type;
        if ($this->isList()) {
            $name = Strings::plural($name);
        }
        return $name;
    }
}
