<?php

namespace Wame\ChameleonComponents\Tests\Chameleon;

require_once '../bootstrap.php';
use A;
use B;
use Doctrine\Common\Collections\Criteria;
use Nette\InvalidArgumentException;
use TestChameleonControlA;
use Tester\Assert;
use Tester\TestCase;
use Wame\ChameleonComponents\DataSpacesBuilder;
use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\ChameleonComponents\Definition\DataSpace;

class DataSpacesBuilderTest extends TestCase
{

    public function testSingleDefinition()
    {
        $control1 = new \TestChameleonControlA(null, '1');
        $controlDataDefinitions = new ControlDataDefinition($control1, new DataDefinition(new DataDefinitionTarget(A::class, false)));

        $dataSpaces = $this->build($controlDataDefinitions);

        $this->compareDataSpaces([new DataSpace($control1, new DataDefinition(new DataDefinitionTarget(A::class, false)))], $dataSpaces);
    }

    public function testChildDefinitionMergeKnown()
    {
        $control1 = new \TestChameleonControlA(null, '1');
        $control2 = new \TestChameleonControlA($control1, '2');
        $c1 = Criteria::create()->where(Criteria::expr()->gt('value1', 3));
        $c2 = Criteria::create()->where(Criteria::expr()->lt('value2', 5));
        $controlDataDefinitions = new ControlDataDefinition($control1, new DataDefinition(new DataDefinitionTarget(A::class, false), $c1));
        $controlDataDefinitions->setChildren([
            new ControlDataDefinition($control2, new DataDefinition(new DataDefinitionTarget(A::class, false), $c2))
        ]);

        $dataSpaces = $this->build($controlDataDefinitions);

        $rc = Criteria::create()->where(Criteria::expr()->gt('value1', 3))->andWhere(Criteria::expr()->lt('value2', 5));
        $this->compareDataSpaces([new DataSpace($control1, new DataDefinition(new DataDefinitionTarget(A::class, false), $rc))], $dataSpaces);
    }

//    public function testConflictDefinitionSameValue()
//    {
//        $c1 = Criteria::create()->where(Criteria::expr()->eq('value', 3));
//        $c2 = Criteria::create()->where(Criteria::expr()->eq('value', 5));
//        $dataDefinition = new ControlDataDefinition(new DataDefinitionTarget(A::class, true), $c1);
//        $dataDefinition->setChildren([
//            new ControlDataDefinition(new DataDefinitionTarget(A::class, true), $c2)
//        ]);
//
//        Assert::exception(function() use($dataDefinition) {
//            $dataSpacesBuilder = new DataSpacesBuilder($dataDefinition);
//            $dataSpacesBuilder->buildDataSpaces();
//        });
//    }

    public function testListDefinitions()
    {
        $control1 = new \TestChameleonControlA(null, '1');
        $control2 = new \TestChameleonControlA($control1, '2');
        $controlDataDefinition = new ControlDataDefinition($control1, new DataDefinition(new DataDefinitionTarget(A::class, true)));
        $controlDataDefinition->setChildren([
            new ControlDataDefinition($control2, new DataDefinition(new DataDefinitionTarget(A::class, false, true)))
        ]);

        $dataSpaces = $this->build($controlDataDefinition);

        $d1 = new DataSpace($control1, new DataDefinition(new DataDefinitionTarget(A::class, true)));
        $d2 = new DataSpace($control2, new DataDefinition(new DataDefinitionTarget(A::class, false, true)));
        $this->compareDataSpaces([$d1, $d2], $dataSpaces);
    }

    public function testTypeMerges()
    {
        $this->doTestTypeMerge(new DataDefinitionTarget(A::class), new DataDefinitionTarget(A::class), new DataDefinitionTarget(A::class));
        $this->doTestTypeMerge(new DataDefinitionTarget(A::class), new DataDefinitionTarget([A::class, B::class]), new DataDefinitionTarget(A::class));
        $this->doTestTypeMerge(new DataDefinitionTarget("*"), new DataDefinitionTarget(A::class), new DataDefinitionTarget(A::class));
    }

    public function testTypeMergesTooManyResults()
    {
        $this->doTestTypeMergeException(new DataDefinitionTarget("*"), new DataDefinitionTarget([A::class, B::class]));
    }

    private function doTestTypeMerge($type1, $type2, $result)
    {
        $control1 = new \TestChameleonControlA(null, '1');
        $control2 = new \TestChameleonControlA($control1, '2');
        $controlDataDefinitions = new ControlDataDefinition($control1, new DataDefinition($type1));
        $controlDataDefinitions->setChildren([
            new ControlDataDefinition($control2, new DataDefinition($type2))
        ]);

        $dataSpaces = $this->build($controlDataDefinitions);

        $this->compareDataSpaces([new DataSpace($control1, new DataDefinition($result))], $dataSpaces);
    }

    private function doTestTypeMergeException($type1, $type2)
    {
        $control1 = new \TestChameleonControlA(null, '1');
        $control2 = new \TestChameleonControlA($control1, '2');
        $controlDataDefinitions = new ControlDataDefinition($control1, new DataDefinition($type1));
        $controlDataDefinitions->setChildren([
            new ControlDataDefinition($control2, new DataDefinition($type2))
        ]);

        Assert::exception(function() use($controlDataDefinitions) {
            $this->build($controlDataDefinitions);
        }, InvalidArgumentException::class);
    }

//    public function testNoListDefinitions()
//    {
//        $control1 = new TestChameleonControlA(null, '1');
//        $control2 = new TestChameleonControlA($control1, '1');
//        $controlDataDefinition = new ControlDataDefinition($control1, new DataDefinition(new DataDefinitionTarget(A::class, false)));
//        $controlDataDefinition->setChildren([
//            new ControlDataDefinition($control2, new DataDefinition(new DataDefinitionTarget(A::class, false, true)))
//        ]);
//
//        Assert::exception(function() use($controlDataDefinition) {
//            $this->build($controlDataDefinition);
//        }, InvalidArgumentException::class);
//    }

    /**
     * 
     * @param ControlDataDefinition[] $controlDataDefinitions
     * @return DataSpace[]
     */
    private function build($controlDataDefinitions)
    {
        $dataSpacesBuilder = new DataSpacesBuilder($controlDataDefinitions);
        return $dataSpacesBuilder->buildDataSpaces();
    }

    private function compareDataSpaces($o1, $o2)
    {
        if (is_array($o1) && is_array($o2)) {
            if (count($o1) != count($o2)) {
                Assert::fail("Arrays dont have same length!", count($o1), count($o2));
            }
            foreach ($o1 as $key => $o1val) {
                if (!isset($o2[$key])) {
                    Assert::fail("Arrays dont have same keys!", $o1, $o2);
                }
                $this->compareDataSpaces($o1val, $o2[$key]);
            }
        } elseif ($o1 instanceof DataSpace && $o2 instanceof DataSpace) {
            Assert::same($o1->getControl(), $o2->getControl(), "Controls should be same");
            $this->compareDataSpaces($o1->getDataDefinition(), $o2->getDataDefinition());
        } elseif ($o1 instanceof DataDefinition && $o2 instanceof DataDefinition) {
            Assert::equal($o1->getKnownProperties(), $o2->getKnownProperties());
            $this->compareDataSpaces($o1->getTarget(), $o2->getTarget());
        } elseif ($o1 instanceof DataDefinitionTarget && $o2 instanceof DataDefinitionTarget) {
            Assert::equal($o1->getType(), $o2->getType(), "Types has to be equal");
            Assert::equal($o1->getList(), $o2->getList(), "'list' values has to be equal");
            Assert::equal($o1->getMultiple(), $o2->getMultiple(), "'multiple' values has to be equal");
        } else {
            Assert::fail("Types does not match!", gettype($o1), gettype($o2));
        }
    }
}

$test = new DataSpacesBuilderTest();
$test->run();
