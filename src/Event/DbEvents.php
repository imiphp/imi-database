<?php

declare(strict_types=1);

namespace Imi\Db\Event;

use Imi\Util\Traits\TStaticClass;

final class DbEvents
{
    use TStaticClass;

    /**
     * 数据库执行 SQL 语句.
     */
    public const EXECUTE = 'imi.db.execute';

    /**
     * 数据库执行准备语句.
     */
    public const PREPARE = 'imi.db.prepare';
}
