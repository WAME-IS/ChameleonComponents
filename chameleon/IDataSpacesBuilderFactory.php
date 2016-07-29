<?php

namespace Wame\ChameleonComponents;

interface IDataSpacesBuilderFactory
{

    /** @return Wame\ChameleonComponents\DataSpacesBuilder */
    public function create($dataDefinitions);
}
