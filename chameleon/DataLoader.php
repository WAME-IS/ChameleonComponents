<?php

namespace Wame\ChameleonComponents;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\InvalidArgumentException;
use Nette\Object;
use RecursiveIteratorIterator;
use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponents\Definition\RecursiveTreeDefinitionIterator;
use Wame\ChameleonComponents\Registers\DataLoaderDriverRegister;

/**
 * Heart of ChameleonComponents. (Powerful tool for making components that can 
 * adapt to its surroundings like chameleon)
 *
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class DataLoader extends Object
{

    /** @var IDataSpacesBuilderFactory */
    private $dataSpacesBuilderFactory;

    /** @var DataLoaderDriverRegister */
    private $dataLoaderDriverRegister;

    /** @var Cache */
    private $cache;

    public function __construct(IDataSpacesBuilderFactory $dataSpacesBuilderFactory, DataLoaderDriverRegister $dataLoaderDriverRegister, IStorage $cacheStorage)
    {
        $this->dataSpacesBuilderFactory = $dataSpacesBuilderFactory;
        $this->dataLoaderDriverRegister = $dataLoaderDriverRegister;
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

        $prepared = $this->prepareDataSpaces($dataSpaces);

//TODO      $this->cache

        $this->dataLoaderDriver->execute($dataSpaces, $prepared);

        return $dataSpaces;
    }

    /**
     * @param DataSpace[] $dataSpaces
     */
    private function prepareDataSpaces($dataSpaces)
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveTreeDefinitionIterator($dataSpaces), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $dataSpace) {
            $driver = $this->selectDataDriver($dataSpace);

            $name = $dataSpace->getDataDefinition()->getTarget()->getStatusName();
            $callback = $driver->prepareCallbacks($dataSpace);

            $dataSpace->getControl()->getState()->set($name, $callback);
        }
    }

    /**
     * @param DataSpace $dataSpace
     * @return IDataLoaderDriver
     */
    private function selectDataDriver($dataSpace)
    {
        $driver = $dataSpace->getDataDefinition()->getHint('dataLoaderDriver', IDataLoaderDriver::class);
        if ($driver) {
            return $driver;
        }

        foreach ($this->dataLoaderDriverRegister as $driver) {
            if ($driver->canPrepare($dataSpace)) {
                return $driver;
            }
        }

        $e = new InvalidArgumentException("Cannot find driver that can be used to load this DataSpace");
        $e->dataSpace = $dataSpace;
        throw $e;
    }
}
