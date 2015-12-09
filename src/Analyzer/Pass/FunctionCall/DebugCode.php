<?php

namespace PHPSA\Analyzer\Pass\FunctionCall;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPSA\Context;

class DebugCode
{
    protected $map = array(
        'var_dump' => 'var_dump',
        'var_export' => 'var_export',
        'debug_zval_dump' => 'debug_zval_dump'
    );

    public function visitPhpFunctionCall(FuncCall $funcCall, Context $context)
    {
        $name = false;

        if ($funcCall->name instanceof Name && !$funcCall->name->isFullyQualified()) {
            $name = $funcCall->name->getFirst();
        }

        if ($name && isset($this->map[$name])) {
            if ($funcCall->getDocComment()) {
                $phpdoc = new \phpDocumentor\Reflection\DocBlock($funcCall->getDocComment()->getText());
                if ($phpdoc->hasTag('expected')) {
                    return true;
                }
            }

            $context->notice(
                'debug.code',
                sprintf('Function %s() is a debug code, please don`t use it in production.', $name),
                $funcCall
            );
        }
    }
}
