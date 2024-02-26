<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace Mine\Abstracts;

use Hyperf\Collection\Collection;
use Hyperf\Database\Model\Builder;
use Mine\Interfaces\ServiceInterface\QueryResourceServiceInterface;

abstract class AbstractQueryResource implements QueryResourceServiceInterface
{
    /**
     * query 字段名 名称.
     */
    protected string $field = 'field';

    /**
     * query 字段值 名称.
     */
    protected string $value = 'value';

    /**
     * @var string 传入的搜索字段
     */
    protected string $keyword = 'keywords';

    /**
     * @var string 搜索字段
     */
    protected string $searchKeywords = 'keywords';

    public function getSearchKeywords(): string
    {
        return $this->searchKeywords;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function handleSearch(Builder $query, array $params = [], array $extras = []): Builder
    {
        $keywordKey = $this->getKeyword();
        $searchKeywords = $this->getSearchKeywords();
        if (! empty($params[$keywordKey])) {
            $query->orWhere(function ($query) use ($params, $searchKeywords, $keywordKey) {
                $searchbars = explode(',', $searchKeywords);
                foreach ($searchbars as $searchbar) {
                    $query->orWhere($searchbar, 'like', '%' . $params[$keywordKey] . '%');
                }
            });
        }

        return $query;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function resource(array $params = [], array $extras = []): array
    {
        $query = $this->handleSearch(
            $this->getQuery(),
            $params,
            $extras
        );
        return $this->formatResource(
            $query->get(),
        );
    }

    protected function formatResource(Collection $list): array
    {
        $field = $this->getField();
        $value = $this->getValue();
        $resource = [];
        foreach ($list as $k => $v) {
            $resource[] = [
                'field' => $v[$field],
                'value' => $v[$value],
            ];
        }
        return $resource;
    }
}
