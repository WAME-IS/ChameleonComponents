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
        return $dataSpace->getDataDefinition()->getTarget()->getType() . '-' . self::STATUS_SUFFIX;
    }
}
