<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace PHPSA\Compiler\Statement;

use PHPSA\CompiledExpression;
use PHPSA\Context;
use PHPSA\Definition\ClassMethod;

class ReturnSt extends AbstractCompiler
{
    protected $name = '\PhpParser\Node\Stmt\Return_';

    /**
     * @param \PhpParser\Node\Stmt\Return_ $stmt
     * @param Context $context
     * @return CompiledExpression
     */
    public function compile($stmt, Context $context)
    {
        $compiledExpression = $context->getExpressionCompiler()->compile($stmt->expr);

        if ($context->scopePointer) {
            /**
             * If it is a Class's method we need to work on return types, return possible values
             */
            if ($context->scopePointer->isClassMethod()) {
                /** @var ClassMethod $classMethod */
                $classMethod = $context->scopePointer->getObject();
                $classMethod->addNewType($compiledExpression->getType());

                if ($compiledExpression->isCorrectValue()) {
                    $classMethod->addReturnPossibleValue($compiledExpression->getValue());
                }
            }
        }
    }
}
