<?php

namespace Wame\ChameleonComponents\Definition;

use Nette\Object;

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
     * @param boolean $list
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return boolean
     */
    public function isList()
    {
        return $this->list;
    }

    /**
     * @return boolean
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param string $type Class name
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param boolean $list
     */
    public function setList($list)
    {
        $this->list = $list;
        return $this;
    }
}
