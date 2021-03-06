<?php

namespace PHPSA\Compiler\Expression\Operators\Logical;

use PHPSA\CompiledExpression;
use PHPSA\Context;
use PHPSA\Compiler\Expression;
use PHPSA\Compiler\Expression\AbstractExpressionCompiler;

class LogicalAnd extends AbstractExpressionCompiler
{
    protected $name = 'PhpParser\Node\Expr\BinaryOp\LogicalAnd';

    /**
     * {expr} and {expr}
     *
     * @param \PhpParser\Node\Expr\BinaryOp\LogicalAnd $expr
     * @param Context $context
     * @return CompiledExpression
     */
    protected function compile($expr, Context $context)
    {
        $left = $context->getExpressionCompiler()->compile($expr->left);
        $right = $context->getExpressionCompiler()->compile($expr->right);

        if ($left->isTypeKnown() && $right->isTypeKnown()) {
            return CompiledExpression::fromZvalValue(
                $left->getValue() and $right->getValue()
            );
        }

        return new CompiledExpression();
    }
}
