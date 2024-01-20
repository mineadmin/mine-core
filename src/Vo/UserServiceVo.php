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

namespace Mine\Vo;

class UserServiceVo
{
    /**
     * 用户名.
     */
    protected string $username;

    /**
     * 密码
     */
    protected string $password;

    /**
     * 手机.
     */
    protected string $phone;

    /**
     * 邮箱.
     */
    protected string $email;

    /**
     * 验证码
     */
    protected string $verifyCode;

    /**
     * 其他数据.
     */
    protected array $other;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getVerifyCode(): string
    {
        return $this->verifyCode;
    }

    public function setVerifyCode(string $verifyCode): void
    {
        $this->verifyCode = $verifyCode;
    }

    public function getOther(): array
    {
        return $this->other;
    }

    public function setOther(array $other): void
    {
        $this->other = $other;
    }
}
