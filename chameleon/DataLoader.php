<?php

namespace Wame\ChameleonComponents;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Object;
use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\ChameleonComponents\Definition\DataSpace;

class DataLoader extends Object
{

    /** @var IDataSpacesBuilderFactory */
    private $dataSpacesBuilderFactory;

    /** @var IDataLoaderDriver */
    private $dataLoaderDriver;

    /** @var Cache */
    private $cache;

    public function __construct(IDataSpacesBuilderFactory $dataSpacesBuilderFactory, IDataLoaderDriver $dataLoaderDriver, IStorage $cacheStorage)
    {
        $this->dataSpacesBuilderFactory = $dataSpacesBuilderFactory;
        $this->dataLoaderDriver = $dataLoaderDriver;
        $this->cache = new Cache($cacheStorage, "DataLoader");
    }

    /**
     * 
     * @param ControlDataDefinition[] $controlDataDefinitions
     * @return DataSpace[]
     */
    public function processDataDefinitions($controlDataDefinitions)
    {
//TODO      $this->cache

        $dataSpaceBuilder = $this->dataSpacesBuilderFactory->create($controlDataDefinitions);
        $dataSpaces = $dataSpaceBuilder->buildDataSpaces();

        $prepared = $this->dataLoaderDriver->prepare($dataSpaces);

//TODO      $this->cache

        $this->dataLoaderDriver->execute($dataSpaces, $prepared);

        return $dataSpaces;
    }
}
