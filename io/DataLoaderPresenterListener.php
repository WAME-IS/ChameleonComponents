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
        $dataDefinitions = $this->readDataDefinitions($presenter);

        if ($dataDefinitions) {
            //Optimize loading of DataLoader, load it only if some definitions are found
            $dataLoader = $this->container->getByType(DataLoader::class);
            $dataLoader->processDataDefinitions($dataDefinitions);
        }
    }

    /**
     * Reads DataDefinitions from control and its childs
     * 
     * @param Control $control
     * @return array
     */
    private function readDataDefinitions($control)
    {

        $dataDefinition = null;
        if ($control instanceof DataLoaderControl) {
            $dataDefinition = $control->getDataDefinition();
            if ($dataDefinition instanceof DataDefinition || is_array($dataDefinition)) {
                $dataDefinition = new ControlDataDefinition($control, $dataDefinition);
            }
        }

        if ($dataDefinition && !$dataDefinition instanceof ControlDataDefinition) {
            $e = new InvalidArgumentException("getDataDefinition function has to return ControlDataDefinition or DataDefinition(s)");
            $e->dataDefinition = $dataDefinition;
            throw $e;
        }

        $childDataDefinitions = [];
        if($control instanceof Container) {
            foreach ($control->getComponents() as $subcontrol) {
                $childDataDefinitions = array_merge($childDataDefinitions, $this->readDataDefinitions($subcontrol));
            }
        }

        if ($dataDefinition) {
            $dataDefinition->setChildren($childDataDefinitions);
            return [$dataDefinition];
        } else {
            return $childDataDefinitions;
        }
    }
}
