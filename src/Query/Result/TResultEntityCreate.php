<?php

declare(strict_types=1);

namespace Imi\Db\Query\Result;

use Imi\Bean\BeanFactory;
use Imi\Model\Event\Param\AfterQueryEventParam;
use Imi\Model\Model;

trait TResultEntityCreate
{
    /**
     * @template T of object
     *
     * @param class-string<T> $className
     *
     * @return Model|T
     */
    public function createEntity(string $className, array $record): object
    {
        if (is_subclass_of($className, Model::class))
        {
            $object = $className::createFromRecord($record);
        }
        else
        {
            $object = BeanFactory::newInstance($className);
            foreach ($record as $k => $v)
            {
                $object->{$k} = $v;
            }
        }
        $object->dispatch(new AfterQueryEventParam($object));

        return $object;
    }
}
