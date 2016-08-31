<?php

namespace Wame\ChameleonComponents\Components;

use Doctrine\Common\Collections\Criteria;
use Nette\Application\BadRequestException;
use Nette\DI\Container;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\ChameleonComponents\IO\DataLoaderControl;
use Wame\Core\Components\BaseControl;
use Wame\Core\Entities\BaseEntity;
use Wame\ReportModule\Vendor\Wame\AdminModule\Grids\Columns\Report\Type;

abstract class SingleEntityControl extends BaseControl implements DataLoaderControl
{

    /** @var int */
    private $entityId;

    /** @var BaseEntity */
    private $entity;

    public function __construct(Container $container, $entity = null)
    {
        parent::__construct($container);

        $this->getStatus()->get($this->getEntityType(), function($entity) {
            if (!$entity) {
                throw new BadRequestException("Entity with this id doesn't exist");
            }
            $this->entity = $entity;
        });

        if ($entity) {
            $this->getStatus()->set($this->getEntityType(), $entity);
        }
    }

    /**
     * @param int $entityId
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    }

    public function getDataDefinition()
    {
        $criteria = null;
        if ($this->entityId) {
            $criteria = Criteria::create()->where(Criteria::expr()->eq('id', $this->entityId));
        }

        return new DataDefinition(new DataDefinitionTarget($this->getEntityType(), false), $criteria);
    }

    /**
     * @return BaseEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    public function render()
    {
        $this->template->entity = $this->entity;
    }

    /**
     * @return Type of entity used
     */
    protected abstract function getEntityType();
}
