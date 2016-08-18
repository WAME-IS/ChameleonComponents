<?php

namespace Wame\ChameleonComponents\IO;

use App\Core\Presenters\BasePresenter;
use Nette\Application\Application;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\ComponentModel\Container;
use Nette\InvalidArgumentException;
use Nette\Object;
use Wame\ChameleonComponents\DataLoader;
use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinition;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class DataLoaderPresenterListener extends Object
{

    /** @var \Nette\DI\Container */
    private $container;
    
    /** @var DataDefinition[] */
    private $dataDefinitions;
    
    /** @var Control[] */
    private $toRead = [];

    public function __construct(Application $application, \Nette\DI\Container $container)
    {
        $this->container = $container;
        $application->onPresenter[] = function($application, $presenter) {
            if ($presenter instanceof BasePresenter) {
                $presenter->onBeforeRender[] = function() use ($presenter) {
                    $this->load($presenter);
                };
            }
        };
    }

    public function load(Presenter $presenter)
    {
        $this->toRead[] = ['control' => $presenter, 'parent' => NULL, 'exclude' => []];
        
        while($this->toRead) {
            $toRead = array_shift($this->toRead);
            $dataDefinitions = $this->readDataDefinitions($toRead['control'], $toRead['exclude']);
            if($toRead['parent']) {
                foreach($dataDefinitions as $dataDefinition) {
                    $dataDefinition->setParent($toRead['parent']);
                }
            } else {
                $this->dataDefinitions = $dataDefinitions;
            }
            
            $this->processDefinitions();
        }
    }
    
    protected function processDefinitions()
    {
        if ($this->dataDefinitions) {
            //Optimalization of loading DataLoader, its loaded only if some definitions are found
            $dataLoader = $this->container->getByType(DataLoader::class);
            $dataLoader->processDataDefinitions($this->dataDefinitions);
        }
    }

    /**
     * Reads DataDefinitions from control and its childs
     * 
     * @param Control $control
     * @param Control[] $exclude Child components to exclude
     * @return array
     */
    protected function readDataDefinitions($control, $exclude)
    {

        $dataDefinition = $this->readControlDataDefinition($control);

        $childDataDefinitions = $this->readChildDataDefinitions($control, $exclude);

        if ($dataDefinition) {
            $dataDefinition->setChildren($childDataDefinitions);
            
            if($dataDefinition->isTriggersProcessing()) {
                
                $childControls = array_unique(array_map(function($childDataDefinition){
                    return $childDataDefinition->getControl();
                }, $childDataDefinitions));
                
                $this->toRead[] = ['control' => $dataDefinition->getControl(), 'parent' => $dataDefinition, 'exclude' => $childControls];
            }
            
            return [$dataDefinition];
        } else {
            return $childDataDefinitions;
        }
    }
    
    private function readControlDataDefinition($control)
    {
        $dataDefinition = null;
        
        if ($control instanceof DataLoaderControl) {
            $dataDefinition = $control->getDataDefinition($this);
            if ($dataDefinition instanceof DataDefinition || is_array($dataDefinition)) {
                $dataDefinition = new ControlDataDefinition($control, $dataDefinition);
            }
        }

        if ($dataDefinition && !$dataDefinition instanceof ControlDataDefinition) {
            $e = new InvalidArgumentException("getDataDefinition function has to return ControlDataDefinition or DataDefinition(s)");
            $e->dataDefinition = $dataDefinition;
            throw $e;
        }
        
        return $dataDefinition;
    }
    
    private function readChildDataDefinitions($control, $exclude)
    {
        $childDataDefinitions = [];
        if($control instanceof Container) {
            foreach ($control->getComponents() as $subcontrol) {
                if(in_array($subcontrol, $exclude)) {
                    continue;
                }
                $childDataDefinitions = array_merge($childDataDefinitions, $this->readDataDefinitions($subcontrol, []));
            }
        }
        return $childDataDefinitions;
    }
}
