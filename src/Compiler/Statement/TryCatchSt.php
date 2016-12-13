<?php
/**
 * @author Patsura Dmitry https://github.com/ovr <talk@dmtry.me>
 */

namespace PHPSA\Compiler\Statement;

use PHPSA\CompiledExpression;
use PHPSA\Context;

class TryCatchSt extends AbstractCompiler
{
    protected $name = '\PhpParser\Node\Stmt\TryCatch';

    /**
     * @param \PhpParser\Node\Stmt\TryCatch $statement
     * @param Context $context
     * @return CompiledExpression
     */
    public function compile($statement, Context $context)
    {
        foreach ($statement->stmts as $stmt) {
            \PHPSA\nodeVisitorFactory($stmt, $context);
        }

        foreach ($statement->catches as $stmt) {
            \PHPSA\nodeVisitorFactory($stmt, $context);
        }

        if ($statement->finally !== null) {
            \PHPSA\nodeVisitorFactory($statement->finally, $context);
        }
    }
}
