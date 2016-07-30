<?php
namespace Wame\ChameleonComponents;

use Wame\ChameleonComponents\Definition\DataSpace;

/**
 * Interface for driver that can be used in DataLoader
 */
interface IDataLoaderDriver
{
    /**
     * Prepare callback to load data into DataSpace
     * 
     * @param DataSpace $dataSpace
     * @return mixed
     */
    public function prepareCallbacks($dataSpace);
}
