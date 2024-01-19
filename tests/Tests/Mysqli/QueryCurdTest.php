<?php

declare(strict_types=1);

namespace Imi\Db\Test\Tests\Mysqli;

use Imi\Db\Test\Tests\QueryCurdBaseTestCase;

/**
 * @testdox Mysqli MySQL QueryCurd
 */
class QueryCurdTest extends QueryCurdBaseTestCase
{
    /**
     * 连接池名.
     */
    protected ?string $poolName = 'mysqli';

    /**
     * 测试 whereEx 的 SQL.
     */
    protected string $expectedTestWhereExSql = 'select * from `tb_article` where (`id` = :p1 and (`id` in (:p2)))';

    /**
     * 测试 JSON 查询的 SQL.
     */
    protected string $expectedTestJsonSelectSql = 'select * from `tb_test_json` where `json_data`->\'$.uid\' = :p1';
}
