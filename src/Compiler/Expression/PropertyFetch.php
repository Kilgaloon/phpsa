<?php

namespace PHPSA\Compiler\Expression;

use PHPSA\Check;
use PHPSA\CompiledExpression;
use PHPSA\Context;
use PHPSA\Definition\ClassDefinition;

class PropertyFetch extends AbstractExpressionCompiler
{
    protected $name = 'PhpParser\Node\Expr\PropertyFetch';

    /**
     * classname->property
     *
     * @param \PhpParser\Node\Expr\PropertyFetch $expr
     * @param Context $context
     * @return CompiledExpression
     */
    protected function compile($expr, Context $context)
    {
        $compiler = $context->getExpressionCompiler();

        $propertNameCE = $compiler->compile($expr->name);

        $scopeExpression = $compiler->compile($expr->var);
        if ($scopeExpression->isObject()) {
            $scopeExpressionValue = $scopeExpression->getValue();
            if ($scopeExpressionValue instanceof ClassDefinition) {
                $propertyName = $propertNameCE->isString() ? $propertNameCE->getValue() : false;
                if ($propertyName) {
                    if ($scopeExpressionValue->hasProperty($propertyName, true)) {
                        $property = $scopeExpressionValue->getProperty($propertyName, true);
                        return $compiler->compile($property);
                    } else {
                        $context->notice(
                            'language_error',
                            sprintf(
                                'Property %s does not exist in %s scope',
                                $propertyName,
                                $scopeExpressionValue->getName()
                            ),
                            $expr
                        );
                    }
                }
            }

            return new CompiledExpression();
        } elseif ($scopeExpression->canBeObject()) {
            return new CompiledExpression();
        }

        $context->notice(
            'language_error',
            "It's not possible to fetch a property on a non-object",
            $expr,
            Check::CHECK_BETA
        );

        return new CompiledExpression();
    }
}
