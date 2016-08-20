<?php

namespace Wame\ChameleonComponents\Registers\Types;

use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\Utils\Strings;

class QueryTypeSelect implements IQueryType
{

    /**
     * @param DataSpace $dataSpace
     */
    public function getStatusName($dataSpace)
    {
        $target = $dataSpace->getDataDefinition()->getTarget();
        $name = $target->getType();
        if ($target->isList()) {
            $name = Strings::plural($name);
        }
        return $name;
    }
}
