<?php

namespace app\common\service;

/**
 * Description of WorkbenchService
 * @author zhangbin 2018-12-8
 */
class WorkbenchService
{

    /**
     * 待核价任务推送
     * @author zhangbin
     * @param string $operator 操作人
     * @param int $operationTime 操作时间
     * @param array $data 核价数据
     * @return boolean
     */
    static public function todoPushPricecheck($operator, $operationTime, $data)
    {
        if (empty($data)) {
            return false;
        }
        $pdata = [];
        $pdata['operator'] = $operator;
        $pdata['operationTime'] = $operationTime;
        $pdata['systemType'] = 'pms';
        $pdata['data'] = [];
        $ptype = \app\common\ConfigBase::getPricecheckType();
        foreach ($data as $itm) {
            $type_name = isset($ptype[$itm['type']]) ? $ptype[$itm['type']] : '';
            $description = '核价类型：' . $type_name . '；SKU：' . $itm['sku'];
            $tmp = [];
            $tmp['arrivalTime'] = $pdata['operationTime'];
            $tmp['billCode'] = $itm['id'];
            $tmp['billType'] = 200;
            $tmp['description'] = $description;
            $tmp['handlers'] = $itm['handlers'];
            $tmp['submitter'] = $operator;
            $tmp['targetUrl'] = '/pms/purchasemanage/checkpricemanage/skucheckpric/?id=' . $itm['id'];
            $tmp['taskType'] = 2;
            $pdata['data'][] = $tmp;
        }
        return self::todoPush($pdata);
    }

    /**
     * 待审批订单推送
     * @author zhangbin
     * @param string $operator 操作人
     * @param int $operationTime 操作时间
     * @param array $data 订单数据
     * @return boolean
     */
    static public function todoPushPurchaseorder($operator, $operationTime, $data)
    {
        if (empty($data)) {
            return false;
        }
        $pdata = [];
        $pdata['operator'] = $operator;
        $pdata['operationTime'] = $operationTime;
        $pdata['systemType'] = 'pms';
        $pdata['data'] = [];
        foreach ($data as $itm) {
            $description = '采购金额：' . $itm['money'] . '；供应商：' . $itm['supplier_name'];
            $tmp = [];
            $tmp['arrivalTime'] = $pdata['operationTime'];
            $tmp['billCode'] = $itm['id'];
            $tmp['billType'] = 100;
            $tmp['description'] = $description;
            $tmp['handlers'] = $itm['handlers'];
            $tmp['submitter'] = $operator;
            $tmp['targetUrl'] = '/pms/purchasemanage/orderquery/detail/?orderNumber=' . $itm['id'];
            $tmp['taskType'] = 1;
            $pdata['data'][] = $tmp;
        }
        return self::todoPush($pdata);
    }

    /**
     * 待办任务推送
     * @author zhangbin
     * @param object $data 数据
     * @param string $data.operator 操作人
     * @param int $data.operationTime 操作时间
     * @param array $data.data 数据
     * @param string $data.data.billCode 单据编号
     * @param int $data.data.billType 单据类型【100-采购订单 101-XX订单 200-核价任务 201-XX任务】
     * @param string $data.data.description 描述
     * @param array $data.data.handler 当前处理人(流程结束传空)
     * @param string $data.data.submitter 提交人
     * @param string $data.data.targetUrl 子系统操作url
     * @param int $data.data.taskType 任务类型【1-待审批的订单 2-待审查的任务】
     * @return bool
     */
    static private function todoPush($data)
    {
        $url = \think\Config::get('motan.eco_url');
        if (empty($url)) {
            trace('motan.eco_url is empty,line=' . __LINE__, 'error');
            return false;
        }
        $result = MotanService::execute($url, $data, 'todoPull');
        if (!$result || !isset($result['state']) || $result['state'] !== '000001') {
            trace('class=' . __CLASS__ . ',func=' . __FUNCTION__ . ',line=' . __LINE__, 'error');
            return false;
        }
        return true;
    }

    /**
     * 消息推送
     * @author zhangbin
     * @param string $billCode 单据编号
     * @param int $messageType 消息内容类型【1-站内消息 2-下架申请】
     * @param string $content 内容
     * @param array $receivers 接收人
     * @param string $sender 发送人
     * @param string $targetUrl 子系统操作url
     * @param string $operator 操作人
     * @return bool
     */
    static public function messagePush($billCode, $messageType, $content, $receivers
    , $sender, $targetUrl, $operator)
    {
        $url = \think\Config::get('motan.eco_url');
        if (empty($url)) {
            trace('motan.eco_url is empty,line=' . __LINE__, 'error');
            return false;
        }
        $data = [];
        $data['operator'] = $operator;
        // 操作时间
        $data['operationTime'] = time() * 1000;
        $data['systemType'] = 'pms';
        // 发送时间
        $data['data']['sendTime'] = $data['operationTime'];
        $data['data']['billCode'] = $billCode;
        $data['data']['messageType'] = $messageType;
        $data['data']['content'] = $content;
        $data['data']['receivers'] = $receivers;
        $data['data']['sender'] = $sender;
        $data['data']['targetUrl'] = $targetUrl;
        $result = MotanService::execute($url, $data, 'messagePull');
        if (!$result || !isset($result['state']) || $result['state'] !== '000001') {
            trace('class=' . __CLASS__ . ',func=' . __FUNCTION__ . ',line=' . __LINE__, 'error');
            return false;
        }
        return true;
    }

}
