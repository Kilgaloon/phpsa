<?php

namespace Tests\PHPSA\Compiler\Expression\Operators\Logical;

use PhpParser\Node;
use PHPSA\CompiledExpression;
use PHPSA\Compiler\Expression;

/**
 * Class BooleanNotTest
 * @package Tests\PHPSA\Expression\Operators\Logical
 */
class BooleanNotTest extends \Tests\PHPSA\TestCase
{
    /**
     * @return array
     */
    public function getDataProvider()
    {
        return [
            [true, false],
            [false, true],
            [1, false],
            [-1, false],
            [1.4, false],
            [null, true],
            ["a", false],
            [[], true],
        ];
    }

    /**
     * Tests !{expr}
     *
     * @see \PHPSA\Compiler\Expression\Operators\Logical\BooleanNot
     * @dataProvider getDataProvider
     */
    public function testSimpleSuccessCompile($a, $b)
    {
        $baseExpression = new Node\Expr\BooleanNot(
            $this->newScalarExpr($a)
        );
        $compiledExpression = $this->compileExpression($baseExpression);

        $this->assertInstanceOfCompiledExpression($compiledExpression);
        $this->assertSame(CompiledExpression::BOOLEAN, $compiledExpression->getType());
        $this->assertSame($b, $compiledExpression->getValue());
    }

    public function testUnexpectedType()
    {
        $baseExpression = new Node\Expr\BooleanNot(
            $this->newFakeScalarExpr()
        );
        $compiledExpression = $this->compileExpression($baseExpression);

        $this->assertInstanceOfCompiledExpression($compiledExpression);
        $this->assertSame(CompiledExpression::UNKNOWN, $compiledExpression->getType());
        $this->assertSame(null, $compiledExpression->getValue());
    }
}
