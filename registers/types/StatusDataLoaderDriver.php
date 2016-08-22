<?php

namespace Wame\ChameleonComponents\Registers\Types;

use Nette\InvalidArgumentException;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponents\IDataLoaderDriver;
use Wame\ChameleonComponents\Registers\QueryTypesRegister;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class StatusDataLoaderDriver implements IDataLoaderDriver
{

    /** @var QueryTypesRegister */
    private $queryTypesRegister;

    public function __construct(QueryTypesRegister $queryTypesRegister)
    {
        $this->queryTypesRegister = $queryTypesRegister;
    }

    /**
     * Prepare callback for loading data
     * 
     * @param DataSpace $dataSpace
     * @return callable
     */
    public function prepareCallback($dataSpace)
    {
        // do noting, its already there
    }

    /**
     * Returns whenever this driver can prepare callback to load data
     * 
     * @param DataSpace $dataSpace
     * @return boolean
     */
    public function canPrepare($dataSpace)
    {
        $value = $dataSpace->getControl()->getStatus()->get($this->getStatusName($dataSpace));
        return boolval($value);
    }

    /**
     * @param DataSpace $dataSpace
     * @return string
     * @throws InvalidArgumentException
     */
    private function getStatusName($dataSpace)
    {
        $qtn = $dataSpace->getDataDefinition()->getQueryType();
        if(!$qtn) {
            $qtn = DataDefinition::DEFAULT_QUERY_TYPE;
        }
        $qt = $this->queryTypesRegister->getByName($qtn);
        if ($qt) {
            return $qt->getStatusName($dataSpace);
        } else {
            throw new InvalidArgumentException("Invalid queryType $qtn.");
        }
    }
}
