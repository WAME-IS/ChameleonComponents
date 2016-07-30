<?php

namespace Wame\ChameleonComponents\Definition;

class RecursiveTreeDefinitionIterator extends \RecursiveArrayIterator implements \Countable
{

    /**
     * Has the current element has children?
     * @return bool
     */
    public function hasChildren()
    {
        return boolval($this->current()->getChildren());
    }

    /**
     * The sub-iterator for the current element.
     * @return \RecursiveIterator
     */
    public function getChildren()
    {
        return new RecursiveControlDefinitionIterator($this->current()->getChildren());
    }

    /**
     * Returns the count of elements.
     * @return int
     */
    public function count()
    {
        return iterator_count($this);
    }
}
