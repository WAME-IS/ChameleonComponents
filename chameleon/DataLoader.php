<?php

namespace Wame\ChameleonComponents;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Object;
use RecursiveIteratorIterator;
use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponents\Definition\RecursiveTreeDefinitionIterator;
use Wame\ChameleonComponents\IDataLoaderDriver;
use Wame\ChameleonComponents\IDataSpacesBuilderFactory;
use Wame\ChameleonComponents\Registers\DataLoaderDriverRegister;
use Wame\ChameleonComponents\Registers\QueryTypesRegister;
use WebLoader\InvalidArgumentException;

/**
 * Heart of ChameleonComponents. (Powerful tool for making components that can 
 * adapt to its surroundings like chameleon)
 *
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class DataLoader extends Object
{

    const DEFAULT_QUERY_TYPE = 'select';

    /** @var IDataSpacesBuilderFactory */
    private $dataSpacesBuilderFactory;

    /** @var DataLoaderDriverRegister */
    private $dataLoaderDriverRegister;

    /** @var QueryTypesRegister */
    private $queryTypesRegister;

    /** @var Cache */
    private $cache;

    public function __construct(IDataSpacesBuilderFactory $dataSpacesBuilderFactory, DataLoaderDriverRegister $dataLoaderDriverRegister, QueryTypesRegister $queryTypesRegister, IStorage $cacheStorage)
    {
        $this->dataSpacesBuilderFactory = $dataSpacesBuilderFactory;
        $this->dataLoaderDriverRegister = $dataLoaderDriverRegister;
        $this->queryTypesRegister = $queryTypesRegister;
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

        $this->prepareDataSpaces($dataSpaces);
//TODO      $this->cache

        return $dataSpaces;
    }

    /**
     * @param DataSpace[] $dataSpaces
     */
    private function prepareDataSpaces($dataSpaces)
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveTreeDefinitionIterator($dataSpaces), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $dataSpace) {
            /* @var $dataSpace DataSpace */

            $this->putDefaultQueryType($dataSpace);

            $driver = $this->selectDataDriver($dataSpace);

            $callback = $driver->prepareCallback($dataSpace);

            if ($callback) {
                $statusName = $this->getStatusName($dataSpace);
                $dataSpace->getControl()->getStatus()->set($statusName, $callback);
            }
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

    /**
     * @param DataSpace $dataSpace
     */
    private function putDefaultQueryType($dataSpace)
    {
        $target = $dataSpace->getDataDefinition()->getTarget();
        if (!$target->getQueryType()) {
            $target->setQueryType(self::DEFAULT_QUERY_TYPE);
        }
    }

    /**
     * @param DataSpace $dataSpace
     * @return string
     * @throws InvalidArgumentException
     */
    private function getStatusName($dataSpace)
    {
        $qtn = $dataSpace->getDataDefinition()->getTarget()->getQueryType();
        $qt = $this->queryTypesRegister->getByName($qtn);
        if ($qt) {
            return $qt->getStatusName($dataSpace);
        } else {
            throw new InvalidArgumentException("Invalid queryType $qtn.");
        }
    }
}
