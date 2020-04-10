<?php

namespace app\common\service;

/**
 * Description of WMS
 * @author zhangbin 2019-3-16
 */
class WmsService
{

    /**
     * 从WMS系统获取库存数据
     * @param array $sku 需要查询的SKU
     * @param string $warehouse_id 所求库存来源仓库编码，无仓库传递空
     * @param int $enterprise_dominant 请求库存所属采购主体
     * @return array 
     */
    static public function getStock($sku, $warehouse_id = '', $enterprise_dominant = '')
    {
        if (empty($sku)) {
            return [];
        }
        $param = [];
        $param['data'] = [
            'sku' => $sku,
            'warehouseCode' => $warehouse_id,
            'enterpriseDominant' => $enterprise_dominant
        ];
        $url = config("wmsUrl") . 'api/CommonApi/stock';
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('class=' . __CLASS__ . ',function=' . __FUNCTION__ . ',line=' . __LINE__, 'error');
            trace('url=' . $url . ',param=' . json_encode($param), 'error');
            trace('response=' . $response, 'error');
            return [];
        }
        return $json['data'];
    }
    //多线程从WMS系统获取库存数据
    static public function getStockMulti($sku, $warehouseCode = '', $enterpriseDominant = '')
    {
        $data=[];
        $chunkResult = array_chunk($sku, 50);
        foreach ($chunkResult as $v){
            $data[]['data']=[
                'warehouseCode'=>$warehouseCode,
                'enterpriseDominant'=>$enterpriseDominant,
                'sku'=>$v
            ];
        }
        $url = config("wmsUrl") . 'api/CommonApi/stock';
        return curlMulti($url,$data);
    }
    /**
     * 获取仓库编码
     * @param string $type 空字符串或者不传，获取所有的仓库，‘send’ 国内订单执行仓 ‘transfer’ 中转仓 ‘outSend’ 海外仓
     * @param string $warehouse_id 仓库编码 选填项 不传获取所有的仓库编码 如果type和warehouse_id都传，获取交集
     * @return array
     */
    static public function getWarehouse($type = '', $warehouse_id = '')
    {
        $param = [];
        $param['data'] = [
            'type' => $type,
            'warehouseCode' => $warehouse_id
        ];
        $url = config("wmsUrl") . 'api/CommonApi/getWarehouse';
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('class=' . __CLASS__ . ',function=' . __FUNCTION__ . ',line=' . __LINE__, 'error');
            trace('url=' . $url . ',param=' . json_encode($param), 'error');
            trace('response=' . $response, 'error');
            return [];
        }
        $data = [];
        foreach ($json['data'] as $itm) {
            $data[$itm['code']] = $itm['name'];
        }
        unset($json);
        return $data;
    }

}
