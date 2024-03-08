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
use Hyperf\Config\Config;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Mine\Aspect\ConfigCryptAspect;

beforeEach(function () {
    $proceedingJoinPoint = Mockery::mock(ProceedingJoinPoint::class);
    $this->configEnable = new Config([
        'mineadmin' => [
            'config_encryption' => true,
            'config_encryption_key' => '1234567890',
            'config_encryption_iv' => '1234567890',
        ],
        'test' => [
            'key1' => 'ENC(6SX9TNnNO7KH+WFTOmVOFZ2jGsbR4K/0M0BwXvxtu34=)',
        ],
    ]);
    $this->configDisable = new Config([
        'mineadmin' => [
            'config_encryption' => false,
        ],
        'test' => [
            'key1' => 'ENC(6SX9TNnNO7KH+WFTOmVOFZ2jGsbR4K/0M0BwXvxtu34=)',
        ],
    ]);
    $proceedingJoinPoint->allows('process')->andReturn($this->configEnable, $this->configDisable);
    $this->proceedingJoinPoint = $proceedingJoinPoint;
});

test(ConfigCryptAspect::class . ' testing', function () {
    // enable
    $configCryptAspect = new ConfigCryptAspect();
    $config = $configCryptAspect->process($this->proceedingJoinPoint);
    //    var_dump($config , $this->configEnable);
    expect($config !== $this->configEnable)->toBeFalse();

    // disable

    $configCryptAspect = new ConfigCryptAspect();
    $config = $configCryptAspect->process($this->proceedingJoinPoint);
    expect($config !== $this->configEnable)->toBeTrue();
});
