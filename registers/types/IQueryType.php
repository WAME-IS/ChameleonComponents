<?php

namespace Wame\ChameleonComponents\Registers\Types;

use Wame\ChameleonComponents\Definition\DataSpace;

interface IQueryType
{

    /**
     * Returns name of status used to store returned value
     * 
     * @param DataSpace $dataSpace
     * @return string
     */
    public function getStatusName($dataSpace);
}
