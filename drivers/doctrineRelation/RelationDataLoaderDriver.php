<?php

namespace Wame\ChameleonComponents\Drivers\DoctrineRelation;

use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponents\IDataLoaderDriver;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class RelationDataLoaderDriver implements IDataLoaderDriver
{

    /**
     * Prepare callback for loading data
     * 
     * @param DataSpace $dataSpace
     * @return callable
     */
    public function prepareCallback($dataSpace)
    {
        
    }

    /**
     * Returns whenever this driver can prepare callback to load data
     * 
     * @param DataSpace $dataSpace
     * @return boolean
     */
    public function canPrepare($dataSpace)
    {
        
    }
}
