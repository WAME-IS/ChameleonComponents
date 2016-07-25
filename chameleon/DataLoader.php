<?php

namespace Wame\ChameleonComponents;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Object;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataSpace;

class DataLoader extends Object
{

    /** @var DataSpacesBuilder */
    private $dataSpacesBuilder;

    /** @var IDataLoaderDriver */
    private $dataLoaderDriver;

    /** @var Cache */
    private $cache;

    public function __construct(DataSpacesBuilder $dataSpacesBuilder, IDataLoaderDriver $dataLoaderDriver, IStorage $cacheStorage)
    {
        $this->dataSpacesBuilder = $dataSpacesBuilder;
        $this->dataLoaderDriver = $dataLoaderDriver;
        $this->cache = new Cache($cacheStorage, "DataLoader");
    }

    /**
     * 
     * @param DataDefinition[] $dataDefinitions
     * @return DataSpace[]
     */
    public function processDataDefinitions($dataDefinitions)
    {
//TODO      $this->cache

        $dataSpaces = $this->dataSpacesBuilder->buildDataSpaces($dataDefinitions);

        $prepared = $this->dataLoaderDriver->prepare($dataSpaces);

//TODO      $this->cache

        $this->dataLoaderDriver->execute($dataSpaces, $prepared);

        return $dataSpaces;
    }
}
