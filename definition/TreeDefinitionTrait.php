<?php

namespace Wame\ChameleonComponents\Definition;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
trait TreeDefinitionTrait
{

    /** @var static[] */
    private $children = [];
    
    /** @var static */
    private $parent;

    /**
     * @return static[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param static[] $children
     */
    public function setChildren($children)
    {
        $this->children = [];
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param static $child
     * @throws \Nette\InvalidArgumentException
     */
    public function addChild($child)
    {
        if (!is_a($child, get_class($this))) {
            throw new \Nette\InvalidArgumentException("Only same types of objects can be added as children.");
        }
        if (in_array($child, $this->children)) {
            $this->children[] = $child;
            $child->setParent($this);
        }
    }

    /**
     * @param static $parent
     * @throws \Nette\InvalidArgumentException
     */
    public function setParent($parent)
    {
        if (!is_a($parent, get_class($this))) {
            throw new \Nette\InvalidArgumentException("Only same type of object can be set as parent.");
        }
        if ($this->parent != $parent) {
            $this->parent = $parent;
            $parent->addChild($this);
        }
    }
    
    /**
     * @return static
     */
    public function getParent() {
        return $this->parent;
    }
}
