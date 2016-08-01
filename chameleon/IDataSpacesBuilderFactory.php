<?php

namespace Wame\ChameleonComponents;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
interface IDataSpacesBuilderFactory
{

    /** @return Wame\ChameleonComponents\DataSpacesBuilder */
    public function create($dataDefinitions);
}
