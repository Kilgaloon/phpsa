<?php

namespace Tests\PHPSA\Defintion;

use PhpParser\Node\Const_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassConst;
use PHPSA\Definition\ClassDefinition;
use Tests\PHPSA\TestCase;

class ClassDefintionTest extends TestCase
{
    /**
     * @return ClassDefinition
     */
    protected function getSimpleInstance()
    {
        return new ClassDefinition('MyTestClass', null, 0);
    }

    public function testSimpleInstance()
    {
        $classDefinition = $this->getSimpleInstance();
        self::assertSame('MyTestClass', $classDefinition->getName());
        self::assertFalse($classDefinition->isCompiled());
    }

    public function testScopePointer()
    {
        $classDefinition = $this->getSimpleInstance();

        $pointer = $classDefinition->getPointer();
        self::assertInstanceOf('PHPSA\ScopePointer', $pointer);
        self::assertEquals($classDefinition, $pointer->getObject());
    }

    public function testSetGetHasForClassProperty()
    {
        $classDefinition = $this->getSimpleInstance();
        self::assertFalse($classDefinition->hasProperty('test1'));
        self::assertFalse($classDefinition->hasProperty('test2'));

        $property = new \PhpParser\Node\Stmt\Property(
            0,
            array(
                new \PhpParser\Node\Stmt\PropertyProperty(
                    'test1',
                    new \PhpParser\Node\Scalar\String_(
                        'test string'
                    )
                )
            )
        );
        $classDefinition->addProperty($property);

        self::assertTrue($classDefinition->hasProperty('test1'));
        self::assertFalse($classDefinition->hasProperty('test2'));

        $property = new \PhpParser\Node\Stmt\Property(
            0,
            array(
                new \PhpParser\Node\Stmt\PropertyProperty(
                    'test2',
                    new \PhpParser\Node\Scalar\String_(
                        'test string'
                    )
                )
            )
        );
        $classDefinition->addProperty($property);

        self::assertTrue($classDefinition->hasProperty('test1'));
        self::assertTrue($classDefinition->hasProperty('test2'));

        $property = new \PhpParser\Node\Stmt\Property(0, [
            new \PhpParser\Node\Stmt\PropertyProperty(
                'foo',
                new \PhpParser\Node\Scalar\String_('test string')
            ),
            new \PhpParser\Node\Stmt\PropertyProperty(
                'bar',
                new \PhpParser\Node\Scalar\String_('test string')
            )
        ]);
        $classDefinition->addProperty($property);

        self::assertTrue($classDefinition->hasProperty('foo'));
        self::assertTrue($classDefinition->hasProperty('bar'));
    }

    public function testMethodSetGet()
    {
        $classDefinition = $this->getSimpleInstance();
        $methodName = 'method1';
        $nonExistsMethodName = 'method2';

        self::assertFalse($classDefinition->hasMethod($methodName));
        self::assertFalse($classDefinition->hasMethod($nonExistsMethodName));

        $classDefinition->addMethod(
            new \PHPSA\Definition\ClassMethod(
                $methodName,
                new \PhpParser\Node\Stmt\ClassMethod(
                    $methodName
                ),
                0
            )
        );

        self::assertTrue($classDefinition->hasMethod($methodName));
        self::assertFalse($classDefinition->hasMethod($nonExistsMethodName));

        $method = $classDefinition->getMethod($methodName);
        self::assertInstanceOf('PHPSA\Definition\ClassMethod', $method);
        self::assertSame($methodName, $method->getName());

        return $classDefinition;
    }

    public function testHasConstWithNoParent()
    {
        $classDefinition = $this->getSimpleInstance();
        $classDefinition->addConst(new ClassConst([
            new Const_('FOO', new String_('bar'))
        ]));

        self::assertTrue($classDefinition->hasConst('FOO'));
        self::assertFalse($classDefinition->hasConst('BAR'));
    }

    public function testHasConstWithParent()
    {
        $parentClassDefinition = $this->getSimpleInstance();
        $classDefinition = $this->getSimpleInstance();

        $parentClassDefinition->addConst(new ClassConst([
            new Const_('BAR', new String_('baz'))
        ]));

        $classDefinition->setExtendsClassDefinition($parentClassDefinition);
        $classDefinition->addConst(new ClassConst([
            new Const_('FOO', new String_('bar'))
        ]));

        self::assertTrue($classDefinition->hasConst('FOO'));
        self::assertFalse($classDefinition->hasConst('BAR'));
        self::assertTrue($classDefinition->hasConst('BAR', true));
    }
}
