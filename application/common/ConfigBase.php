<?php

namespace app\common;

/**
 * Description of ConfigBase
 * @author zhangbin 2018-11-6
 */
class ConfigBase
{
    // 运输方式
    static private $_transfer_type = array(
        1 => '空运',
        2 => '海运',
        3 => '快递',
        4 => '铁运',
    );

    /**
     * 获取采购单操作日志类型
     * @return array
     */
    static public function getTransferType()
    {
        return self::$_transfer_type;
    }
}
