<?php

namespace Wame\ChameleonComponents\Drivers\DoctrineRepository;

use Nette\InvalidArgumentException;
use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponents\IDataLoaderDriver;
use Wame\Core\Registers\RepositoryRegister;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class RepositoryDataLoaderDriver implements IDataLoaderDriver
{

    /** @var RepositoryRegister */
    private $repositoryRegister;

    public function __construct(RepositoryRegister $repositoryRegister)
    {
        $this->repositoryRegister = $repositoryRegister;
    }

    /**
     * Prepare callback for loading data
     * 
     * @param DataSpace $dataSpace
     * @return callable
     */
    public function prepareCallback($dataSpace)
    {
        $target = $dataSpace->getDataDefinition()->getTarget();
        $entityName = $target->getType();
        $repository = $this->repositoryRegister->getByName($entityName);
        if ($repository) {
            if ($target->isList()) {
                return function() use ($repository, $dataSpace) {
                    return $repository->find($dataSpace->getDataDefinition()->getKnownProperties());
                };
            } else {
                return function() use ($repository, $dataSpace) {
                    $repository->get($dataSpace->getDataDefinition()->getKnownProperties());
                };
            }
        } else {
            throw new InvalidArgumentException("Couldn't find repository for entity named $entityName");
        }
    }

    /**
     * Returns whenever this driver can prepare callback to load data
     * 
     * @param DataSpace $dataSpace
     * @return boolean
     */
    public function canPrepare($dataSpace)
    {
        $target = $dataSpace->getDataDefinition()->getTarget();
        return boolval($this->repositoryRegister->getByName($target->getType()));
    }
}
