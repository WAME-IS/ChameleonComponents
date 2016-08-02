<?php

namespace Wame\ChameleonComponents;

use Wame\ChameleonComponents\Definition\ControlDataDefinition;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
interface IDataSpacesBuilderFactory
{

    /**
     * @return DataSpacesBuilder
     * @param ControlDataDefinition[] $controlDataDefinitions
     */
    public function create($controlDataDefinitions);
}
