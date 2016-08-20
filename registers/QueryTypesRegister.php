<?php

namespace Wame\ChameleonComponents\Registers;

use Wame\ChameleonComponents\Registers\Types\IQueryType;
use Wame\Core\Registers\BaseRegister;

class QueryTypesRegister extends BaseRegister
{

    public function __construct()
    {
        parent::__construct(IQueryType::class);
    }
}
