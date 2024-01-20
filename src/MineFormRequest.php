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

use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Validation\Request\FormRequest;

class MineFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return array_merge(
            $this->callNextFunction('common', __FUNCTION__),
            $this->callNextFunction($this->getAction(), __FUNCTION__)
        );
    }

    public function attributes(): array
    {
        return array_merge(
            $this->callNextFunction('common', __FUNCTION__),
            $this->callNextFunction($this->getAction(), __FUNCTION__)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge(
            $this->callNextFunction('common', __FUNCTION__),
            $this->callNextFunction($this->getAction(), __FUNCTION__)
        );
    }

    protected function callNextFunction(?string $prefix, string $function): array
    {
        if (is_null($prefix)) {
            return [];
        }
        $callName = $prefix . ucfirst($function);
        return method_exists($this, $callName) ? \Hyperf\Support\call([$this, $callName]) : [];
    }

    protected function getAction(): ?string
    {
        /**
         * @var Dispatched $dispatch
         */
        $dispatch = $this->getAttribute(Dispatched::class);
        $callback = $dispatch?->handler?->callback;
        if (is_array($callback) && count($callback) === 2) {
            return $callback[1];
        }
        if (is_string($callback)) {
            if (str_contains($callback, '@')) {
                return explode('@', $callback)[1] ?? null;
            }
            if (str_contains($callback, '::')) {
                return explode('::', $callback)[1] ?? null;
            }
        }
        return null;
    }

    /**
     * @deprecated >v1.5.0
     */
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
