<?php

namespace Wame\ChameleonComponents\IO;

use Wame\ChameleonComponents\Definition\ControlDataDefinition;

/**
 * Controls which want to use automatic DataLoader should implement this interface
 * 
 * @author Ienze
 */
interface DataLoaderControl
{

    /**
     * Get definition of data that should be loaded by DataLoader
     * 
     * @return ControlDataDefinition Definition
     */
    public function getDataDefinition();
}
