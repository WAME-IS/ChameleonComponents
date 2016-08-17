<?php

namespace Wame\ChameleonComponents\Tests\Chameleon;

require_once '../bootstrap.php';

use Doctrine\Common\Collections\Criteria;
use Nette\Application\UI\Control;
use Nette\InvalidArgumentException;
use Tester\Assert;
use Tester\TestCase;
use Wame\ChameleonComponents\DataSpacesBuilder;
use Wame\ChameleonComponents\Definition\ControlDataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinition;
use Wame\ChameleonComponents\Definition\DataDefinitionTarget;
use Wame\ChameleonComponents\Definition\DataSpace;
use Wame\ChameleonComponents\IO\DataLoaderControl;

class A
{
    
}

class B
{
    
}

class TestChameleonControlA extends Control implements DataLoaderControl
{

    public function getDataDefinition()
    {
        return new DataDefinition(new DataDefinitionTarget(A::class, false));
    }
}

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class DataSpacesBuilderTest extends TestCase
{

    public function testSingleDefinition()
    {
        $control1 = new TestChameleonControlA(null, '1');
        $controlDataDefinitions = new ControlDataDefinition($control1, new DataDefinition(new DataDefinitionTarget(A::class, false)));

        $dataSpaces = $this->build($controlDataDefinitions);

        $expectedDataSpaces = [new DataSpace($control1, new DataDefinition(new DataDefinitionTarget(A::class, false)))];
        $this->compareDataSpaces($dataSpaces, $expectedDataSpaces);
    }

    public function testChildDefinitionMergeKnown()
    {
        $control1 = new TestChameleonControlA(null, '1');
        $control2 = new TestChameleonControlA($control1, '2');
        $c1 = Criteria::create()->where(Criteria::expr()->gt('value1', 3));
        $c2 = Criteria::create()->where(Criteria::expr()->lt('value2', 5));
        $controlDataDefinitions = new ControlDataDefinition($control1, new DataDefinition(new DataDefinitionTarget(A::class, false), $c1));
        $controlDataDefinitions->setChildren([
            new ControlDataDefinition($control2, new DataDefinition(new DataDefinitionTarget(A::class, false), $c2))
        ]);

        $dataSpaces = $this->build($controlDataDefinitions);

        $rc = Criteria::create()->where(Criteria::expr()->gt('value1', 3))->andWhere(Criteria::expr()->lt('value2', 5));
        $expectedDataSpaces = [new DataSpace($control1, new DataDefinition(new DataDefinitionTarget(A::class, false), $rc))];
        $this->compareDataSpaces($dataSpaces, $expectedDataSpaces);
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
        $control1 = new TestChameleonControlA(null, '1');
        $control2 = new TestChameleonControlA($control1, '2');
        $controlDataDefinition = new ControlDataDefinition($control1, new DataDefinition(new DataDefinitionTarget(A::class, true)));
        $controlDataDefinition->setChildren([
            new ControlDataDefinition($control2, new DataDefinition(new DataDefinitionTarget(A::class, false, true)))
        ]);

        $dataSpaces = $this->build($controlDataDefinition);

        $d1 = new DataSpace($control1, new DataDefinition(new DataDefinitionTarget(A::class, true)));
        $d2 = new DataSpace($control2, new DataDefinition(new DataDefinitionTarget(A::class, false, true)));
        $expectedDataSpaces = [$d1, $d2];
        $this->compareDataSpaces($dataSpaces, $expectedDataSpaces);
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
        $control1 = new TestChameleonControlA(null, '1');
        $control2 = new TestChameleonControlA($control1, '2');
        $controlDataDefinitions = new ControlDataDefinition($control1, new DataDefinition($type1));
        $controlDataDefinitions->setChildren([
            new ControlDataDefinition($control2, new DataDefinition($type2))
        ]);

        $dataSpaces = $this->build($controlDataDefinitions);

        $expectedDataSpaces = [new DataSpace($control1, new DataDefinition($result))];
        $this->compareDataSpaces($dataSpaces, $expectedDataSpaces);
    }

    private function doTestTypeMergeException($type1, $type2)
    {
        $control1 = new TestChameleonControlA(null, '1');
        $control2 = new TestChameleonControlA($control1, '2');
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

    private function compareDataSpaces($actual, $expected)
    {
        if (is_array($actual) && is_array($expected)) {
            if (count($actual) != count($expected)) {
                Assert::fail("Arrays dont have same length!", $actual, $expected);
            }
            foreach ($actual as $key => $actualval) {
                if (!isset($expected[$key])) {
                    Assert::fail("Arrays dont have same keys!", $actual, $expected);
                }
                $this->compareDataSpaces($actualval, $expected[$key]);
            }
        } elseif ($actual instanceof DataSpace && $expected instanceof DataSpace) {
            Assert::same($expected->getControl(), $actual->getControl(), "Controls should be same");
            $this->compareDataSpaces($actual->getDataDefinition(), $expected->getDataDefinition());
        } elseif ($actual instanceof DataDefinition && $expected instanceof DataDefinition) {
            Assert::equal($expected->getKnownProperties(), $actual->getKnownProperties());
            $this->compareDataSpaces($actual->getTarget(), $expected->getTarget());
        } elseif ($actual instanceof DataDefinitionTarget && $expected instanceof DataDefinitionTarget) {
            Assert::equal($expected->getType(), $actual->getType(), "Types has to be equal");
            Assert::equal($expected->getList(), $actual->getList(), "'list' values has to be equal");
        } else {
            Assert::fail("Types does not match!", gettype($actual), gettype($expected));
        }
    }
}

$test = new DataSpacesBuilderTest();
//TODO !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
$test->testChildDefinitionMergeKnown();
//$test->run();
