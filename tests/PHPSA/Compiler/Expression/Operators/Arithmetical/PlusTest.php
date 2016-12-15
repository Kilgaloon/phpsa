<?php

namespace Tests\PHPSA\Compiler\Expression\Operators\Arithmetical;

use PhpParser\Node;
use PHPSA\CompiledExpression;
use PHPSA\Compiler\Expression;

class PlusTest extends \Tests\PHPSA\TestCase
{
    /**
     * Data provider for Plus {int} + {int} = {int}
     *
     * @return array
     */
    public function intToIntDataProvider()
    {
        return [
            [-1, -1, -2],
            [-1, 0, -1],
            [0, -1, -1],
            [-1, 2, 1],
            [2, -1, 1],
            [0, 0, 0],
            [0, 1, 1],
            [1, 0, 1],
            [1, 2, 3],
            [2, 1, 3],
            [25, 25, 50],
            [50, 50, 100],
        ];
    }

    /**
     * Data provider for Plus {int} + {float} = {int}
     *
     * @return array
     */
    public function intToFloatDataProvider()
    {
        return [
            [1, -1.5, -0.5],
            [1, -1.0, 0.0],
            [-1, -1.0, -2.0],
            [-1, -2.55, -3.55],
            [1, 1.5, 2.5],
            [1, 2.5, 3.5],
            [1, 4.5, 5.5],
            [1, 4.75, 5.75],
            [25, 24.75, 49.75]
        ];
    }

    /**
     * Tests {int} + {int} = {int}
     *
     * @dataProvider intToIntDataProvider
     */
    public function testPlusIntToInt($a, $b, $c)
    {
        $this->assertInternalType('int', $a);
        $this->assertInternalType('int', $b);
        $this->assertInternalType('int', $c);

        $baseExpression = new Node\Expr\BinaryOp\Plus(
            new Node\Scalar\LNumber($a),
            new Node\Scalar\LNumber($b)
        );
        $compiledExpression = $this->compileExpression($baseExpression);

        $this->assertInstanceOfCompiledExpression($compiledExpression);
        $this->assertSame(CompiledExpression::INTEGER, $compiledExpression->getType());
        $this->assertSame($c, $compiledExpression->getValue());
    }

    /**
     * Tests {int} + {float} = {float}
     *
     * @dataProvider intToFloatDataProvider
     */
    public function testPlusIntToFloat($a, $b, $c)
    {
        $this->assertInternalType('int', $a);
        $this->assertInternalType('double', $b);
        $this->assertInternalType('double', $c);

        $baseExpression = new Node\Expr\BinaryOp\Plus(
            new Node\Scalar\LNumber($a),
            new Node\Scalar\DNumber($b)
        );
        $compiledExpression = $this->compileExpression($baseExpression);

        $this->assertInstanceOfCompiledExpression($compiledExpression);
        $this->assertSame(CompiledExpression::DOUBLE, $compiledExpression->getType());
        $this->assertSame($c, $compiledExpression->getValue());
    }

    /**
     * Tests {float} + {int} = {float}
     *
     * testPlusFloatToInt($b, $a - it's special to use already defined fixtures
     *
     * @dataProvider intToFloatDataProvider
     */
    public function testPlusFloatToInt($b, $a, $c)
    {
        $this->assertInternalType('double', $a);
        $this->assertInternalType('int', $b);
        $this->assertInternalType('double', $c);

        $baseExpression = new Node\Expr\BinaryOp\Plus(
            new Node\Scalar\DNumber($a),
            new Node\Scalar\LNumber($b)
        );
        $compiledExpression = $this->compileExpression($baseExpression);

        $this->assertInstanceOfCompiledExpression($compiledExpression);
        $this->assertSame(CompiledExpression::DOUBLE, $compiledExpression->getType());
        $this->assertSame($c, $compiledExpression->getValue());
    }

    /**
     * Tests {float} + {float} = {float}
     *
     * @dataProvider intToFloatDataProvider
     */
    public function testPlusFloatToFloat($a, $b, $c)
    {
        $a = (float) $a;

        $this->assertInternalType('double', $a);
        $this->assertInternalType('double', $b);
        $this->assertInternalType('double', $c);

        $baseExpression = new Node\Expr\BinaryOp\Plus(
            /**
             * float casting to use already defined fixtures (float) {int} + {float} = {float}
             */
            new Node\Scalar\DNumber($a),
            new Node\Scalar\DNumber($b)
        );
        $compiledExpression = $this->compileExpression($baseExpression);

        $this->assertInstanceOfCompiledExpression($compiledExpression);
        $this->assertSame(CompiledExpression::DOUBLE, $compiledExpression->getType());
        $this->assertSame($c, $compiledExpression->getValue());
    }

    /**
     * Tests {left-expr::UNKNOWN} + {right-expr}
     */
    public function testFirstUnexpectedTypes()
    {
        $baseExpression = new Node\Expr\BinaryOp\Plus(
            $this->newFakeScalarExpr(),
            $this->newScalarExpr(1)
        );
        $compiledExpression = $this->compileExpression($baseExpression);

        $this->assertInstanceOfCompiledExpression($compiledExpression);
        $this->assertSame(CompiledExpression::UNKNOWN, $compiledExpression->getType());
        $this->assertSame(null, $compiledExpression->getValue());
    }

    /**
     * Tests {left-expr} + {right-expr::UNKNOWN}
     */
    public function testSecondUnexpectedTypes()
    {
        $baseExpression = new Node\Expr\BinaryOp\Plus(
            $this->newScalarExpr(1),
            $this->newFakeScalarExpr()
        );
        $compiledExpression = $this->compileExpression($baseExpression);

        $this->assertInstanceOfCompiledExpression($compiledExpression);
        $this->assertSame(CompiledExpression::UNKNOWN, $compiledExpression->getType());
        $this->assertSame(null, $compiledExpression->getValue());
    }
}
