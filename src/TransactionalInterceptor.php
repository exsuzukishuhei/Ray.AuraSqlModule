<?php
/**
 * This file is part of the Ray.AuraSqlModule package
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\AuraSqlModule;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

class TransactionalInterceptor implements MethodInterceptor
{
    const PROP = 'pdo';

    /**
     * {@inheritdoc}
     */
    public function invoke(MethodInvocation $invocation)
    {
        $object = $invocation->getThis();
        $ref = new \ReflectionProperty($object, self::PROP);
        $ref->setAccessible(true);
        $db = $ref->getValue($object);
        $db->beginTransaction();
        try {
            $invocation->proceed();
            $db->commit();
        } catch (\Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
}