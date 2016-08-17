<?php

namespace Wame\ChameleonComponents\IO;

use App\Core\Presenters\BasePresenter;
use Nette\Application\Application;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
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

    /** @var Container */
    private $container;

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
    private function readDataDefinitions(Control $control)
    {

        $dataDefinition = null;
        if ($control instanceof DataLoaderControl) {
            $dataDefinition = $control->getDataDefinition();
            if ($dataDefinition instanceof \Wame\ChameleonComponents\Definition\DataDefinition || is_array($dataDefinition)) {
                $dataDefinition = new ControlDataDefinition($control, $dataDefinition);
            }
        }

        if ($dataDefinition && !$dataDefinition instanceof ControlDataDefinition) {
            $e = new \Nette\InvalidArgumentException("getDataDefinition function has to return ControlDataDefinition or DataDefinition(s)");
            $e->dataDefinition = $dataDefinition;
            throw $e;
        }

        $childDataDefinitions = [];
        foreach ($control->getComponents() as $subcontrol) {
            $childDataDefinitions = array_merge($childDataDefinitions, $this->readDataDefinitions($subcontrol));
        }

        if ($dataDefinition) {
            $dataDefinition->setChildren($childDataDefinitions);
            return [$dataDefinition];
        } else {
            return $childDataDefinitions;
        }
    }
}
