<?php

namespace Wame\ChameleonComponents\Drivers\Repository;

use Nette\InvalidArgumentException;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\IDataLoaderDriver;
use Wame\Core\Registers\RepositoryRegister;

class RepositoryDataLoaderDriver implements IDataLoaderDriver
{
    /** @var RepositoryRegister */
    private $repositoryRegister;
    
    public function __construct(RepositoryRegister $repositoryRegister)
    {
        $this->repositoryRegister = $repositoryRegister;
    }
    
    public function execute($dataSpaces, $prepared)
    {
        foreach ($dataSpaces as $dataSpace) {
            
            $data = [];
            
            foreach ($dataSpace->getDataDefinitions() as $dataDefinition) {
                $result = $this->executeDefinition($dataDefinition);
                $data[$dataDefinition->getTarget()->getType()] = $result;
            }
            
            $dataSpace->setData($data);
        }
    }
    
    private function executeDefinition(DataDefinition $dataDefinition)
    {
        $entityName = $dataDefinition->getTarget()->getType();
        $repository = $this->repositoryRegister->getByName($entityName);
        if($repository) {
            
            if($dataDefinition->getTarget()->isMultiple()) {
                return $repository->find($dataDefinition->getKnownProperties());
            } else {
                return $repository->get($dataDefinition->getKnownProperties());
            }
        } else {
            throw new InvalidArgumentException("Couldn't find repository for entity named $entityName");
        }
    }

    public function prepare($dataSpaces)
    {
        
    }
}
