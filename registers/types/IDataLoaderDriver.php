<?php
namespace Wame\ChameleonComponents;

use Wame\ChameleonComponents\Definition\DataSpace;

/**
 * Interface for driver that can be used in DataLoader
 *
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
interface IDataLoaderDriver
{
    
    /**
     * Prepare callback for loading data
     * 
     * @param DataSpace $dataSpace
     * @return callable
     */
    public function prepareCallback($dataSpace);
    
    /**
     * Returns whenever this driver can prepare callback to load data
     * 
     * @param DataSpace $dataSpace
     * @return boolean
     */
    public function canPrepare($dataSpace);
    
}
