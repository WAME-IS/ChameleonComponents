<?php

namespace Wame\ChameleonComponents\IO;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
trait DataLoaderPresenterTrait
{
    /** @var DataLoaderPresenterIO */
    private $dataLoaderPresenterIO;
    
    public function injectDataLoaderPresenterIO(DataLoaderPresenterIO $dataLoaderPresenterIO) {
        $this->dataLoaderPresenterIO = $dataLoaderPresenterIO;
    }
    
    protected function beforeRender()
    {
        $this->dataLoaderPresenterIO->load($this);
        parent::beforeRender();
    }
}
