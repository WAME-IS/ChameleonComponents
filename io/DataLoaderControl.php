<?php

namespace Wame\ChameleonComponents\IO;

use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\Core\Cache\TemplatingCache;
use Wame\Core\Status\ControlStatus;

/**
 * Controls which want to use automatic DataLoader should implement this interface
 *
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
interface DataLoaderControl
{

    /**
     * Get definition of data that should be loaded by DataLoader
     * 
     * @return ControlDataDefinition|DataDefinition|DataDefinition[] Definition
     */
    public function getDataDefinition();
    
    /**
     * @return ControlStatus
     */
    public function getStatus();
    
    /**
     * @return TemplatingCache
     */
    public function getComponentCache();
}
