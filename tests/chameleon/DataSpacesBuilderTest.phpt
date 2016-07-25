<?php

namespace Wame\Core\Tests\Registers;

require_once '../bootstrap.php';
use A;
use Doctrine\Common\Collections\Criteria;
use Tester\Assert;
use Tester\TestCase;
use Wame\ChameleonComponents\DataSpacesBuilder;
use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\ChameleonComponents\Definition\DataSpace;

class DataSpacesBuilderTest extends TestCase
{

    /** @var DataSpacesBuilder */
    private $dataSpacesBuilder;

    protected function setUp()
    {
        $this->dataSpacesBuilder = new DataSpacesBuilder();
    }

    protected function tearDown()
    {
        $this->dataSpacesBuilder = null;
    }

    public function testSingleDefinition()
    {
        $dataDefinition = new ControlDataDefinition(new DataDefinitionTarget(A::class, false));

        $dataSpaces = $this->dataSpacesBuilder->buildDataSpaces($dataDefinition);

        Assert::same([new DataSpace(new DataDefinition(new DataDefinitionTarget(A::class, false)))], $dataSpaces);
    }

    public function testChildDefinitionMergeKnown()
    {
        $c1 = Criteria::create()->where(Criteria::expr()->gt('value1', 3));
        $c2 = Criteria::create()->where(Criteria::expr()->lt('value2', 5));
        $dataDefinition = new ControlDataDefinition(new DataDefinitionTarget(A::class, true), $c1);
        $dataDefinition->setChilds([
            new ControlDataDefinition(new DataDefinitionTarget(A::class, true), $c2)
        ]);

        $dataSpaces = $this->dataSpacesBuilder->buildDataSpaces($dataDefinition);

        $rc = Criteria::create()->where(Criteria::expr()->gt('value1', 3)->lt('value2', 5));
        Assert::same([new DataSpace(new DataDefinition(new DataDefinitionTarget(A::class, true)), $rc)], $dataSpaces);
    }

    public function testConflictDefinitionSameValue()
    {
        $c1 = Criteria::create()->where(Criteria::expr()->eq('value', 3));
        $c2 = Criteria::create()->where(Criteria::expr()->eq('value', 5));
        $dataDefinition = new ControlDataDefinition(new DataDefinitionTarget(A::class, true), $c1);
        $dataDefinition->setChilds([
            new ControlDataDefinition(new DataDefinitionTarget(A::class, true), $c2)
        ]);

        Assert::exception(function() use($dataDefinition) {
            $this->dataSpacesBuilder->buildDataSpaces($dataDefinition);
        });
    }

    public function testTwoDefinitions()
    {
        $dataDefinition = new ControlDataDefinition(new DataDefinitionTarget(A::class, false));
        $dataDefinition->setChilds([
            new ControlDataDefinition(new DataDefinitionTarget(B::class, false))
        ]);

        $dataSpaces = $this->dataSpacesBuilder->buildDataSpaces($dataDefinition);

        Assert::same([
            new DataSpace(new DataDefinition(new DataDefinitionTarget(A::class, false))),
            new DataSpace(new DataDefinition(new DataDefinitionTarget(B::class, false)))
            ], $dataSpaces);
    }
    
    public function testListDefinitions()
    {
        $dataDefinition = new ControlDataDefinition(new DataDefinitionTarget(A::class, true));
        $dataDefinition->setChilds([
            new ControlDataDefinition(new DataDefinitionTarget(A::class, false, true))
        ]);

        $dataSpaces = $this->dataSpacesBuilder->buildDataSpaces($dataDefinition);

        $d1 = new DataSpace(new DataDefinition(new DataDefinitionTarget(A::class, true)));
        $d2 = new DataSpace(new DataDefinition(new DataDefinitionTarget(A::class, false, true)));
        $d2->setParent($d1);
        Assert::same([$d1, $d2], $dataSpaces);
    }
    
    public function testNoListDefinitions()
    {
        $dataDefinition = new ControlDataDefinition(new DataDefinitionTarget(A::class, false));
        $dataDefinition->setChilds([
            new ControlDataDefinition(new DataDefinitionTarget(A::class, false, true))
        ]);

        Assert::exception(function(){
             $this->dataSpacesBuilder->buildDataSpaces($dataDefinition);
        });
    }
    
    public function testComplexDefinitions()
    {
        $control1 = new \TestChameleonControlA(null, '1');
        $control2 = new \TestChameleonControlA($control1, '1');
        $control3 = new \TestChameleonControlA($control1, '1');
        
        $c1 = Criteria::create()->where(Criteria::expr()->eq('value', 3));
        $c2 = Criteria::create()->where(Criteria::expr()->eq('value', 5));
        $dataDefinition = new ControlDataDefinition();
        $dataDefinition->addTarget(new DataDefinitionTarget(C::class, false));
        $dataDefinition->addTarget(new DataDefinitionTarget(B::class, false));
        $dataDefinition->setChilds([
            new ControlDataDefinition(new DataDefinitionTarget(A::class, false), $c1),
            new ControlDataDefinition(new DataDefinitionTarget(A::class, false), $c2)
        ]);

        $dataSpaces = $this->dataSpacesBuilder->buildDataSpaces($dataDefinition);

        $c1 = Criteria::create()->where(Criteria::expr()->eq('value', 3));
        $c2 = Criteria::create()->where(Criteria::expr()->eq('value', 5));
        $dataDefinition1 = new DataDefinition();
        $dataDefinition1->addTarget(new DataDefinitionTarget(C::class, false));
        $dataDefinition1->addTarget(new DataDefinitionTarget(B::class, false));
        $dataDefinition2 = new DataDefinition(new DataDefinitionTarget(A::class, false), $c1);
        $dataDefinition3 = new DataDefinition(new DataDefinitionTarget(A::class, false), $c2);
        Assert::same([new DataSpace($dataDefinition1), new DataSpace($dataDefinition2), new DataSpace($dataDefinition3)], $dataSpaces);
    }
}

$test = new BaseRegisterTest();
$test->run();
