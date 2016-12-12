<?php

namespace Wame\ChameleonComponents\Registers\Types;

use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponents\Registers\Types\IQueryType;

class QueryTypeCountList implements IQueryType
{

    const STATUS_SUFFIX = 'count';

    /**
     * @param DataSpace $dataSpace
     */
    public function getStatusName($dataSpace)
    {
//        return $dataSpace->getDataDefinition()->getTarget()->getType() . '-' . self::STATUS_SUFFIX;
        
        /******************************************************************r.g*/
        $control = $dataSpace->getParent()->getControl();
        return \Wame\Utils\Strings::plural($control->getListType()) . '-' . self::STATUS_SUFFIX;
        /**************************************************************end*r.g*/
    }
}
