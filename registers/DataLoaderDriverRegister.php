<?php

namespace Wame\ChameleonComponents\Registers;

use Wame\ChameleonComponents\IDataLoaderDriver;
use Wame\Core\Registers\PriorityRegister;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class DataLoaderDriverRegister extends PriorityRegister
{

    public function __construct()
    {
        parent::__construct(IDataLoaderDriver::class);
    }
}
