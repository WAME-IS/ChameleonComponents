<?php
namespace Wame\ChameleonComponents;

use Wame\ChameleonComponents\Definition\DataSpace;

/**
 * Interface for driver that can be used in DataLoader
 */
interface IDataLoaderDriver
{
    /**
     * Prepare statement to load data into DataSpace
     * 
     * @param DataSpace[] $dataSpaces
     * @return mixed
     */
    public function prepare($dataSpaces);
    
    /**
     * Execute statement to load data into DataSpace
     * 
     * @param DataSpace[] $dataSpaces
     * @param mixed $prepared
     */
    public function execute($dataSpaces, $prepared);
}
