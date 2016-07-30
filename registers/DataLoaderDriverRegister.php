<?php

namespace Wame\ChameleonComponents\Registers;

use Wame\ChameleonComponents\IDataLoaderDriver;
use Wame\Core\Registers\BaseRegister;

class DataLoaderDriverRegister extends BaseRegister
{

    public function __construct()
    {
        parent::__construct(IDataLoaderDriver::class);
    }
}
