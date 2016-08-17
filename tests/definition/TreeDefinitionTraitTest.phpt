<?php

namespace Wame\ChameleonComponents\Tests\Chameleon;

require_once '../bootstrap.php';

use Nette\InvalidArgumentException;
use Tester\Assert;
use Tester\TestCase;

class TreeDefinitionTraitTestLeaf {
    
    use \Wame\ChameleonComponents\Definition\TreeDefinitionTrait;
    
    private $value;
    
    public function __construct($value)
    {
        $this->value = $value;
    }
    
    public function getValue()
    {
        return $this->value;
    }
}

class TreeDefinitionTraitTestSecondLeaf {
    
    use \Wame\ChameleonComponents\Definition\TreeDefinitionTrait;
    
}

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class TreeDefinitionTraitTest extends TestCase
{

    public function testEmpty()
    {
        $leaf = new TreeDefinitionTraitTestLeaf("A");
        Assert::equal($leaf->getChildren(), []);
        Assert::equal($leaf->getParent(), null);
    }

    public function testAddChild()
    {
        $leafA = new TreeDefinitionTraitTestLeaf("A");
        $leafB = new TreeDefinitionTraitTestLeaf("B");
        
        $leafA->addChild($leafB);
        
        Assert::same($leafA->getChildren(), [$leafB]);
        Assert::equal($leafA->getParent(), null);
        Assert::equal($leafB->getChildren(), []);
        Assert::equal($leafB->getParent(), $leafA);
    }
    
    public function testSetChildren()
    {
        $leafA = new TreeDefinitionTraitTestLeaf("A");
        $leafB = new TreeDefinitionTraitTestLeaf("B");
        $leafC = new TreeDefinitionTraitTestLeaf("C");
        
        $leafA->setChildren([$leafB, $leafC]);
        
        Assert::same($leafA->getChildren(), [$leafB, $leafC]);
        Assert::equal($leafA->getParent(), null);
        Assert::equal($leafB->getChildren(), []);
        Assert::equal($leafB->getParent(), $leafA);
        Assert::equal($leafC->getChildren(), []);
        Assert::equal($leafC->getParent(), $leafA);
    }
    
    public function testSetParent()
    {
        $leafA = new TreeDefinitionTraitTestLeaf("A");
        $leafB = new TreeDefinitionTraitTestLeaf("B");
        $leafC = new TreeDefinitionTraitTestLeaf("C");
        
        $leafA->addChild($leafB);
        $leafC->setParent($leafA);
        
        Assert::same($leafA->getChildren(), [$leafB, $leafC]);
        Assert::equal($leafA->getParent(), null);
        Assert::equal($leafB->getChildren(), []);
        Assert::equal($leafB->getParent(), $leafA);
        Assert::equal($leafC->getChildren(), []);
        Assert::equal($leafC->getParent(), $leafA);
    }
    
    public function testWrongTypeChild()
    {
        $leafA = new TreeDefinitionTraitTestLeaf("A");
        $leafB = new TreeDefinitionTraitTestSecondLeaf();
        
        Assert::exception(function() use ($leafA, $leafB){
            $leafA->addChild($leafB);
        }, InvalidArgumentException::class);
    }
    
    public function testWrongTypeParent()
    {
        $leafA = new TreeDefinitionTraitTestLeaf("A");
        $leafB = new TreeDefinitionTraitTestSecondLeaf();
        
        Assert::exception(function() use ($leafA, $leafB){
            $leafB->setParent($leafA);
        }, InvalidArgumentException::class);
    }
    
}

$test = new TreeDefinitionTraitTest();
$test->run();
