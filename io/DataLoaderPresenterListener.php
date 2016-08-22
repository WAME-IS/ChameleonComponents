<?php

namespace Wame\ChameleonComponents\IO;

use App\Core\Presenters\BasePresenter;
use Nette\Application\Application;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\ComponentModel\Container as Container2;
use Nette\DI\Container;
use Nette\InvalidArgumentException;
use Nette\Object;
use RecursiveIteratorIterator;
use Wame\ChameleonComponents\DataLoader;
use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponents\Definition\RecursiveTreeDefinitionIterator;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class DataLoaderPresenterListener extends Object
{

    /** @var Container */
    private $container;

    /** @var DataDefinition[] */
    private $dataDefinitions;
    
    /** @var DataSpace[] */
    private $dataSpaces;

    /** @var Control[] */
    private $toRead = [];

    public function __construct(Application $application, Container $container)
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
        $this->toRead[] = ['control' => $presenter, 'parent' => NULL];

        while ($this->toRead) {
            $toRead = array_shift($this->toRead);
            $dataDefinitions = $this->readDataDefinitions($toRead['control']);
            if ($toRead['parent']) {
                foreach ($dataDefinitions as $dataDefinition) {
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
            $this->dataSpaces = $dataLoader->processDataDefinitions($this->dataDefinitions, $this->dataSpaces);
        }
    }

    /**
     * Reads DataDefinitions from control and its childs
     * 
     * @param Control $control
     * @return array
     */
    protected function readDataDefinitions($control)
    {

        $dataDefinition = $this->readControlDataDefinition($control);

        $childDataDefinitions = $this->readChildDataDefinitions($control);

        if ($dataDefinition) {
            $dataDefinition->setChildren($childDataDefinitions);

            if ($dataDefinition->isTriggersProcessing()) {
                $this->toRead[] = ['control' => $dataDefinition->getControl(), 'parent' => $dataDefinition];
            }

            return [$dataDefinition];
        } else {
            return $childDataDefinitions;
        }
    }

    private function readControlDataDefinition($control)
    {
        
        $dataDefinition = null;

        if ($control instanceof DataLoaderControl && !$this->isProcessed($control)) {
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

    private function readChildDataDefinitions($control)
    {
        $childDataDefinitions = [];
        if ($control instanceof Container2) {
            foreach ($control->getComponents() as $subcontrol) {
                $childDataDefinitions = array_merge($childDataDefinitions, $this->readDataDefinitions($subcontrol));
            }
        }
        return $childDataDefinitions;
    }

    private function isProcessed($control)
    {
        if ($this->dataDefinitions) {
            $iterator = new RecursiveIteratorIterator(new RecursiveTreeDefinitionIterator($this->dataDefinitions), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($iterator as $controlDataDefinition) {
                if ($controlDataDefinition->getControl() === $control) {
                    return true;
                }
            }
        }
        return false;
    }
    
    function getDataDefinitions()
    {
        return $this->dataDefinitions;
    }
    
    function getDataSpaces()
    {
        return $this->dataSpaces;
    }
}
