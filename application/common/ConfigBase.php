<?php

namespace app\common;

/**
 * Description of ConfigBase
 * @author zhangbin 2018-11-6
 */
class ConfigBase
{

    // 业务线
    static private $_serviceline = array(
        1 => '国内仓',
        2 => '亚马逊',
        3 => '海外仓'
    );
    // 国内仓
    static private $_domesticwarehouse = array(
        101 => '国内1号仓',
//        2 => '2号仓',
        103 => '国内3号仓',
//        4 => '4号仓',
//        5 => '5号仓'
    );
    // 计划单状态
    static private $_plan_state = array(
        1 => '执行完毕',
        2 => '部分执行',
        3 => '未执行',
        4 => '已撤销'
    );
    // 计划单异常类型
    static private $_plan_errtype = array(
        1 => 'SKU对应的订货员没录入',
        2 => 'SKU对应的供应商没录入',
        3 => '供应商的跟单员没录入',
        4 => '5号仓不允许下单',
        5 => '下单数不能为0',
        6 => '供应商银行账号不存在或者未审核'
    );
    // 采购单状态
    static private $_order_state = array(
//        1 => '空',
        0 => '全部',
        2 => '待采购',
        3 => '审核中',
        4 => '可打印',
        5 => '已打印',
        11 => '完全入库',
        12 => '手动完结',
        13 => '取消'
    );
    // 新采购单状态与旧状态映射
    static private $_order_state_mapping = array(
        1 => 10,
        2 => 10,
        3 => 10,
        4 => 10,
        5 => 20,
        11 => 70,
        12 => 90,
        13 => 100
    );
    // 采购单明细状态
    static private $_orderdetail_state = array(
//        1 => '空',
        2 => '待采购',
        3 => '审核中',
        4 => '可打印',
        5 => '已打印',
        6 => '未完全到货',
        7 => '完全到货',
        9 => '已质检',
        10 => '未完全入库',
        11 => '完全入库',
        12 => '手动完结',
        13 => '取消'
    );
    // 新采购单明细状态与旧状态映射
    static private $_orderdetail_state_mapping = array(
        1 => 10,
        2 => 10,
        3 => 10,
        4 => 10,
        5 => 20,
        6 => 40,
        7 => 50,
        9 => 60,
        10 => 65,
        11 => 70,
        12 => 90,
        13 => 100
    );
    // 付款方式
    static private $_payment_method = array(
        0 => '全部',
        1 => '货到付款',
        2 => '周结',
        3 => '网上支付',
        4 => '款到发货',
        8 => '半月结',
        11 => '月结30天',
        12 => '月结45天',
        13 => '月结60天',
        14 => '阿里账期支付',
        15 => '预付定金-货到付全款',
        16 => '预付订金-付全款后发货',
        17 => '银行审核付款',
        24 => '银行承兑汇票'
    );
    // 核价类型
    static private $_pricecheck_type = array(
        1 => '大金额优化',
        2 => '供应商联系不上',
        3 => '供应商产品停止、下架、无货',
        4 => '无效价格',
        5 => '链接错误',
        6 => '供应商禁用核查',
        7 => '其它异常',
        8 => '自主优化'
    );
    // 核价状态
    static private $_pricecheck_state = array(
        1 => '待处理',
        2 => '销售代表确认中',
        3 => '销售代表已确认',
        4 => '已处理'
    );
    // 物流方式
    static private $_logistics_state = array(
        0 => '其他',
        1 => '供应商包邮',
        2 => 'YKS指定长风物流(深圳/广州/东莞)'
    );
    // 转运仓ID（1东莞仓#2杭州仓#3深圳仓）
    static private $_transportwarehouse_state = array(
        1 => '东莞仓',
        2 => '杭州仓',
        3 => '深圳仓',
    );
    static private $_enterprise_dominant = array(
        1 => '有棵树电子商务有限公司'
    );
    //中转仓收货地址
    static private $_transfer_hopper_addr = array(
        1 => '东莞市常平镇还珠沥工业区高隆大道3号（导航地址：有棵树电商产业园）。注：周六周日不收货',
        2 => '浙江省杭州市萧山区靖江街道保税路西侧保税大厦641室',
        3 => '深圳市龙岗区平湖镇平安大道华南城海关保税仓5楼后门'
    );
    //国内仓收货地址
    static private $_warehouse_hopper_addr = array(
        101 => '深圳市龙岗区平湖镇平安大道乾龙物流园海关保税仓6楼（前门）',
        102 => '东莞市企石镇东山村木棉工业区西横一路（有棵树跨境电商产业园） 国内2号仓',
        103 => '东莞市企石镇东山村木棉工业区一横西路（有棵树跨境电商产业园） 国内3号仓',
        105 => '苏州市吴中经济开发区郭巷街道官浦路1号（玄通集团现代电商产业园内2楼）有棵树集团',
    );
    // 采购角色类型
    static private $_purchaserole_type = array(
        1 => '采购开发',
        2 => '采购订货',
        3 => '采购主管',
        4 => '采购核价',
        5 => '采购计划'
    );
    // SKU下架原因
    static private $_down_reason = array(
        1 => '低销量',
        2 => '无货源'
    );
    // 销售代表处理结果
    static private $_pricecheck_sale_result = array(
        1 => '同意',
        2 => '不同意'
    );
    // 旧仓库编码映射
    static private $_old_warehouse = array(
        1 => 101,
        2 => 102,
        3 => 103,
        4 => 104,
        5 => 105
    );
    // 新仓库编码
    static private $_warehouse = array(
        101 => '国内1号仓',
        103 => '国内3号仓',
        106 => '国内6号仓',
        107 => '国内7号仓',
        201 => '海外中转仓',
        202 => 'FBA中转仓',
        301 => '美国1号仓',
        302 => '美国2号仓',
        303 => '美国3号仓',
        311 => '英国1号仓',
        312 => '英国2号仓',
        321 => '波兰仓',
        331 => '澳洲仓',
        341 => '俄罗斯仓',
        351 => '法国仓',
        361 => '意大利仓',
        371 => '西班牙仓',
        381 => '澳洲仓',
        391 => 'FBC仓',
        401 => 'jumia尼日仓',
        411 => '虾皮泰国仓',
        421 => '伊朗仓',
        431 => '虾皮马来仓',
        441 => 'jumia科特仓',
        451 => '虾皮菲律宾',
        461 => 'noon平台迪',
        471 => 'Mymall拉脱仓',
        481 => 'linio巴拿',
        491 => 'jumia埃及',
        701 => 'US:美国',
        711 => 'GB:英国',
        721 => 'CA:加拿大',
        731 => 'FR:法国',
        741 => 'DE:德国',
        751 => 'IT:意大利',
        761 => 'JP:日本',
        771 => 'ES:西班牙',
        781 => 'AU:澳洲仓',
        791 => 'MX:墨西哥'
    );
    // 上架类型映射
    static private $_shelve_type_mapping = array(
        10 => 10,
        20 => 100,
        30 => 30,
        40 => 70,
        50 => 50,
        60 => 20,
        70 => 70,
        80 => 70
    );
    // 异常状态有：待采购决策，待实物处理，实物处理中，已完结
    static private $_special_status = array(
        1 => '待采购决策',
        10 => '待实物处理',
        15 => '实物处理中',
        20 => '已完结',
        100 => '取消'
    );
    // 采购单操作日志类型
    static private $_operatelog_status = array(
        1 => '创建采购单',
        2 => '修改订单',
        3 => '提交订单',
        4 => '审核通过',
        5 => '转移审核',
        6 => '审核驳回',
        7 => '取消订单',
        8 => '打印订单',
        9 => '修改采购单跟单员',
        50 => '解绑阿里订单',
        51 => '批量创建付款计划单',
        52 => '删除已存在请款计划单',
        53 => '阿里订单检测不通过',
        55 => '阿里订单付款成功',
        56 => '付款计划单被驳回',
    );
    // SKU状态
    static private $_sku_state = array(
        1 => '正常销售',
        2 => '清库存',
        3 => '缺货',
        4 => '下架',
        5 => '清库存（侵权/违禁）',
        6 => '包材',
        7 => '断货',
        8 => '菜鸟模型产品',
        9 => '海外仓',
        10 => '亚马逊',
        11 => '下架（侵权/违禁）',
        12 => 'IT冻结'
    );
    // 异常决策处理类型（普通产品）
    static private $_thread_choice = array(
        1 => 'BACK', // 退回供应商
        2 => 'SCRAP', // 报废
        3 => 'ORIGINAL', // 不良转良/原单入库
        4 => 'GIFT', // 不良转良/赠品
        5 => 'ORDER' // 不良转良/补发其他采购单
    );
    // 异常决策处理类型（三无产品）
    static private $_thread_choice_none = array(
        1 => 'ORIGINALBACK', // 原路退回
        2 => 'INPO' // 入采购单
    );
    //阿里订单检测状态
    static private $aliDetectionStatus = array(
        0 => '全部',
        10 => '待检测',
        20 => '已通过',
        30 => '未通过',
        40 => '禁止通过',
    );
    //阿里订单状态
    static private $aliStatus = array(
        'all' => '全部',
        'cancel' => '交易取消',
        'confirm_goods' => '已收货',
        'signinsuccess' => '买家已签收',
        'success' => '交易成功',
        'terminated' => '交易终止',
        'waitbuyerpay' => '等待买家付款',
        'waitbuyerreceive' => '等待买家收货',
        'waitbuyersign' => '等待买家签收',
        'waitlogisticstakein' => '等待物流公司揽件',
        'waitsellersend' => '等待卖家发货',
    );
    // 用户角色类型
    static private $userRoleType = array(
        "供应链中心总监" => 1,
        "采购经理" => 2,
        "采购主管" => 3,
        "订货员" => 4,
        "跟单员" => 5
    );
    // 运输方式
    static private $_transfer_type = array(
        1 => '空运',
        2 => '海运',
        3 => '快递',
        4 => '铁运',
    );
    // 币种
    static private $_currency = array(
        1 => '人民币',
        2 => '美元',
        3 => '港币',
    );
    // 汇率类型
    static private $_rateType = array(
        10 => '卖出价',
        20 => '中间价',
        30 => '买入价',
    );

    /**
     * 获取采购单操作日志类型
     * @return array
     */
    static public function getOperatelogStatus()
    {
        return self::$_operatelog_status;
    }

    /**
     * 获取国内收货地址
     * @return array
     */
    static public function getWarehouseHopperAddr()
    {
        return self::$_warehouse_hopper_addr;
    }

    /**
     * 获取收货地址
     * @return array
     */
    static public function getTransferHopperAddr()
    {
        return self::$_transfer_hopper_addr;
    }

    /**
     * 获取国内仓库
     * @return array
     */
    static public function getDomesticWarehouse()
    {
        return self::$_domesticwarehouse;
    }

    /**
     * 获取公司主体
     * @return array
     */
    static public function getEnterpriseDominant()
    {
        return self::$_enterprise_dominant;
    }

    /**
     * 获取业务线
     * @return array
     */
    static public function getServiceline()
    {
        return self::$_serviceline;
    }

    /**
     * 获取计划单状态
     * @return array
     */
    static public function getPlanState()
    {
        return self::$_plan_state;
    }

    /**
     * 获取计划单异常类型
     * @return array
     */
    static public function getPlanErrtype()
    {
        return self::$_plan_errtype;
    }

    /**
     * 获取采购单状态
     * @return array
     */
    static public function getOrderState()
    {
        return self::$_order_state;
    }

    /**
     * 获取采购单详情状态
     * @return array
     */
    static public function getOrderDetailState()
    {
        return self::$_orderdetail_state;
    }

    /**
     * 获取付款方式
     * @return array
     */
    static public function getPaymentMethod()
    {
        return self::$_payment_method;
    }

    /**
     * 获取核价类型
     * @return array
     */
    static public function getPricecheckType()
    {
        return self::$_pricecheck_type;
    }

    /**
     * 获取核价状态
     * @return array
     */
    static public function getPricecheckState()
    {
        return self::$_pricecheck_state;
    }

    /**
     * 获取物流方式
     * @return array
     */
    static public function getLogisticsState()
    {
        return self::$_logistics_state;
    }

    /**
     * 获取转运仓ID
     * @return array
     */
    static public function getTransportWarehouse()
    {
        return self::$_transportwarehouse_state;
    }

    /**
     * 获取采购角色类型
     * @return array
     */
    static public function getPurchaseroleType()
    {
        return self::$_purchaserole_type;
    }

    /**
     * 获取SKU下架原因
     * @return array
     */
    static public function getDownReason()
    {
        return self::$_down_reason;
    }

    /**
     * 销售代表处理结果
     * @return array
     */
    static public function getPricecheckSaleResult()
    {
        return self::$_pricecheck_sale_result;
    }

    /**
     * 旧仓库编码
     * @return array
     */
    static public function getOldWarehouse()
    {
        return self::$_old_warehouse;
    }

    /**
     * 新仓库编码
     * @return array
     */
    static public function getWarehouse()
    {
        return self::$_warehouse;
    }

    /**
     * 新采购单状态与旧状态映射
     * @return array
     */
    static public function getOrderStateMapping()
    {
        return self::$_order_state_mapping;
    }

    /**
     * 新采购单明细状态与旧状态映射
     * @return array
     */
    static public function getOrderDetailStateMapping()
    {
        return self::$_orderdetail_state_mapping;
    }

    /**
     * 上架类型映射
     * @return array
     */
    static public function getShelveTypeMapping()
    {
        return self::$_shelve_type_mapping;
    }

    /**
     * 异常类型映射
     * @return array
     */
    static public function getSpecialStatus()
    {
        return self::$_special_status;
    }

    /**
     * 异常决策处置类型映射
     * @return array
     */
    static public function getThreadChoice()
    {
        return self::$_thread_choice;
    }

    /**
     * 异常决策处置类型映射（三无产品）
     * @return array
     */
    static public function getThreadChoiceNone()
    {
        return self::$_thread_choice_none;
    }

    /**
     * 获取SKU状态
     * @return array
     */
    static public function getSkuState()
    {
        return self::$_sku_state;
    }

    /**
     * 阿里订单状态
     * @return array
     */
    static public function getAliStatus()
    {
        return self::$aliStatus;
    }

    /**
     * 阿里订单检测状态
     * @return array
     */
    static public function getAliDetectionStatus()
    {
        return self::$aliDetectionStatus;
    }

    /**
     * 获取用户角色类型
     * @return array
     */
    static public function getUserRoleType()
    {
        return self::$userRoleType;
    }

    /**
     * 获取运输方式
     * @return array
     */
    static public function getTransferType()
    {
        return self::$_transfer_type;
    }

    /**
     * 获取币种
     * @return array
     */
    static public function getCurrency()
    {
        return self::$_currency;
    }

    /**
     * 获取汇率类型
     * @return array
     */
    static public function getRateType()
    {
        return self::$_rateType;
    }
     /**
     * 获取不良原因
     * @author xjy
     */
    static public function getUnqualifiedType()
    {
        $data = [];$list=[];
        $url = config("wmsUrl") . "warehouse/BasicConfig/unqualifiedType";
        $resultJ = curl($url, $data);
        $result=json_decode($resultJ,true);
        if($result['state']=='000001'){
            $list=array_column($result['data']['list'],'name','code');
        }
        return $list; 
    }
}
