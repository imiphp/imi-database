<?php

declare(strict_types=1);

namespace Imi\Db\Query\Where;

use Imi\Db\Mysql\Consts\LogicalOperator;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IWhere;
use Imi\Db\Query\Raw;
use Imi\Db\Query\Traits\TRaw;

class Where extends BaseWhere implements IWhere
{
    use TRaw;

    public function __construct(
        /**
         * 字段名.
         */
        protected ?string $fieldName = null,
        /**
         * 比较符.
         */
        protected ?string $operation = null,
        /**
         * 值
         */
        protected mixed $value = null, string $logicalOperator = LogicalOperator::AND)
    {
        $this->logicalOperator = $logicalOperator;
    }

    public static function raw(string $rawSql, string $logicalOperator = LogicalOperator::AND, array $binds = []): self
    {
        $where = new self();
        $where->useRaw(true);
        $where->setRawSQL($rawSql, $binds);
        $where->setLogicalOperator($logicalOperator);

        return $where;
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    /**
     * {@inheritDoc}
     */
    public function getOperation(): ?string
    {
        return $this->operation;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function setFieldName(?string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    /**
     * {@inheritDoc}
     */
    public function setOperation(?string $operation): void
    {
        $this->operation = $operation;
    }

    /**
     * {@inheritDoc}
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function toStringWithoutLogic(IQuery $query): string
    {
        if ($this->isRaw)
        {
            return $this->rawSQL;
        }
        $binds = &$this->binds;
        $binds = [];
        $thisValues = &$this->value;
        $operation = $this->operation;
        $result = $query->fieldQuote($this->fieldName) . ' ' . $operation . ' ';
        switch (strtolower((string) $operation))
        {
            case 'between':
            case 'not between':
                if (!\is_array($thisValues) || !isset($thisValues[0], $thisValues[1]))
                {
                    throw new \InvalidArgumentException(sprintf('where %s value must be [beginValue, endValue]', $operation));
                }
                $begin = $query->getAutoParamName();
                $end = $query->getAutoParamName();
                $result .= "{$begin} and {$end}";
                $binds[$begin] = $thisValues[0];
                $binds[$end] = $thisValues[1];
                break;
            case 'in':
            case 'not in':
                $valueNames = [];
                if (\is_array($thisValues))
                {
                    if ($thisValues)
                    {
                        foreach ($thisValues as $value)
                        {
                            $paramName = $query->getAutoParamName();
                            $valueNames[] = $paramName;
                            $binds[$paramName] = $value;
                        }
                        $result .= '(' . implode(',', $valueNames) . ')';
                    }
                    else
                    {
                        $result .= '(' . ('in' === $operation ? '0 = 1' : '1 = 1') . ')';
                    }
                }
                elseif ($thisValues instanceof Raw)
                {
                    $result .= '(' . $thisValues->toString($query) . ')';
                }
                else
                {
                    throw new \InvalidArgumentException(sprintf('Invalid value type %s of where %s', \gettype($thisValues), $operation));
                }
                break;
            default:
                if ($thisValues instanceof Raw)
                {
                    $result .= $thisValues->toString($query);
                }
                else
                {
                    $value = $query->getAutoParamName();
                    $result .= $value;
                    $binds[$value] = $thisValues;
                }
                break;
        }

        return $result;
    }
}
