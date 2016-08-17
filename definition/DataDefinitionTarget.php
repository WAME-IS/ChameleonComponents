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

    /** @var string */
    private $queryType;

    /**
     * Constructs new DataDefinitionTarget
     * 
     * @param string|string[] $type
     * @param boolean $list
     * @param string $queryType
     */
    public function __construct($type, $list = null, $queryType = null)
    {
        $this->type = $type;
        $this->list = $list;
        $this->queryType = $queryType;
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
     * @return string
     */
    function getQueryType()
    {
        return $this->queryType;
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

    /**
     * @param string $queryType
     */
    public function setQueryType($queryType)
    {
        $this->queryType = $queryType;
        return $this;
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
