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
return [
    // 是否启用数据权限
    'data_scope_enabled' => true,
    /*
     * excel 导入、导出驱动类型 auto, xlsWriter, phpOffice
     * auto 优先使用xlsWriter，若环境没有安装xlsWriter扩展则使用phpOffice
     */
    'excel_drive' => 'auto',
    // 是否启用 远程通用列表查询 功能
    'remote_api_enabled' => true,

    'config_encryption' => false,
    'config_encryption_key' => 'oqye5o39exzj47LDFMT2oxRJUmy18Fwo0LB006Uo6fk=',
    'config_encryption_iv' => 'bQEvWfcM6xlt3ZtYgBoK/A==',
];
