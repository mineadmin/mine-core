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

namespace Mine;

use Hyperf\Validation\Request\FormRequest;

class MineApiFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 公共规则.
     */
    public function commonRules(): array
    {
        return [];
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $operation = $this->getOperation();
        $operation = explode('.', $operation);
        $method = end($operation) . 'Rules';
        $rules = ($operation && method_exists($this, $method)) ? $this->{$method}() : [];
        return array_merge($rules, $this->commonRules());
    }

    protected function getOperation(): ?string
    {
        $path = explode('/', $this->getFixPath());
        do {
            $operation = array_pop($path);
        } while (is_numeric($operation));

        return $operation;
    }

    /**
     * request->path在单元测试中拿不到，导致MineFormRequest验证失败
     * 取uri中的path, fix.
     * @return null|string
     */
    protected function getFixPath(): string
    {
        return ltrim($this->getUri()->getPath(), '/');
    }
}
