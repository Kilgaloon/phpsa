<?php

namespace Tests\PHPSA\Expression;

use PhpParser\Node;
use PHPSA\CompiledExpression;
use PHPSA\Compiler\Expression;

class TernaryTest extends \Tests\PHPSA\TestCase
{
    public function testTernaryTrue()
    {
        // (2 == 2) ? "if" : "else"
        $baseExpression = new Node\Expr\Ternary(
            new Node\Expr\BinaryOp\Equal($this->newScalarExpr(2), $this->newScalarExpr(2)),
            $this->newScalarExpr("if"),
            $this->newScalarExpr("else")
        );
        $compiledExpression = $this->compileExpression($baseExpression);

        self::assertInstanceOfCompiledExpression($compiledExpression);
        self::assertSame(CompiledExpression::STRING, $compiledExpression->getType());
        self::assertSame("if", $compiledExpression->getValue());
    }

    public function testTernaryFalse()
    {
        // (2 == 3) ? "if" : "else"
        $baseExpression = new Node\Expr\Ternary(
            new Node\Expr\BinaryOp\Equal($this->newScalarExpr(2), $this->newScalarExpr(3)),
            $this->newScalarExpr("if"),
            $this->newScalarExpr("else")
        );
        $compiledExpression = $this->compileExpression($baseExpression);

        self::assertInstanceOfCompiledExpression($compiledExpression);
        self::assertSame(CompiledExpression::STRING, $compiledExpression->getType());
        self::assertSame("else", $compiledExpression->getValue());
    }
}
