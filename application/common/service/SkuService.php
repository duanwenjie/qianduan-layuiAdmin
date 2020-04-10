<?php

namespace app\common\service;

/**
 * SKU
 * @author zhangbin 2018-11-2
 */
class SkuService
{

    /**
     * 获取SKU用户
     * @author zhangbin
     * @param int $serviceline 业务线 1-国内仓 2-亚马逊 3-海外仓
     * @param int $buyertype 采购员类型 1-采购开发 2-订货员 3-订货主管 4-核价员 5-计划员
     * @param array $sku SKU数组
     * @return array
     */
    static public function getSkuUser($serviceline, $buyertype, $sku)
    {
        $url = config('skuapi') . 'index.php?s=skubase/sku/getSkuUser';
        $param['serviceline'] = $serviceline;
        $param['buyertype'] = $buyertype;
        $param['sku'] = $sku;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('class=' . __CLASS__ . ',function=' . __FUNCTION__ . ',line=' . __LINE__, 'error');
            trace('url=' . $url . ',param=' . json_encode($param), 'error');
            trace('response=' . $response, 'error');
            return [];
        }
        return $json['data'];
//        return \app\api\model\UserSkus::dbGetSkuUser($serviceline, $buyertype, $sku);
    }

    /**
     * 根据SKU模糊查询中文名称
     * @author zhangbin
     * @param int $page 页码
     * @param int $limit 查询条数
     * @param string $label SKU
     * @return array
     */
    static public function getSkuByName($page, $limit, $label = '')
    {
        $url = config('skuapi') . 'index.php?s=skubase/sku/getSkuByName';
        $param['pageNumber'] = $page;
        $param['pageData'] = $limit;
        $param['label'] = $label;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('class=' . __CLASS__ . ',function=' . __FUNCTION__ . ',line=' . __LINE__, 'error');
            trace('url=' . $url . ',param=' . json_encode($param), 'error');
            trace('response=' . $response, 'error');
            return ['total' => '0', 'list' => []];
        }
        return $json['data'];
    }

    /**
     * 根据SKU查询SKU（中文名称、状态）
     * @author zhangbin
     * @param array $sku SKU数组
     * @return array
     */
    static public function getSkuBySku($sku)
    {
        if(empty($sku)){
            return [];
        }
        $url = config('skuapi') . 'index.php?s=skubase/sku/getSkuBySku';
        $param['sku'] = $sku;
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

    /**
     * 根据SKU查询SKU参考价
     * @author zhangbin
     * @param array $sku SKU数组
     * @return array
     */
    static public function getReferencePriceBySku($sku)
    {
        $url = config('skuapi') . 'index.php?s=skubase/sku/getReferencePriceBySku';
        $param['sku'] = $sku;
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

    /**
     * 根据SKU查询SKU日均销量
     * @author zhangbin
     * @param array $sku SKU数组
     * @return array
     */
    static public function getAverageDailySalesBySku($sku)
    {
        $url = config('skuapi') . 'index.php?s=skubase/sku/getAverageDailySalesBySku';
        $param['sku'] = $sku;
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

    /**
     * 根据SKU查询SKU图片
     * @author zhangbin
     * @param array $sku SKU数组
     * @return array
     */
    static public function getImageBySku($sku)
    {
        if (empty($sku)) {
            return [];
        }
        $url = config('skuimagemanage') . 'api/?c=api_pictureurl_urlservers&a=BatchQuery';
        $param = [];
        foreach ($sku as $s) {
            $param[] = [
                'sku' => $s,
                'tuku' => 1,
                'type' => 'ALL'
            ];
        }
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || isset($json['msg'])) {
            //trace('class=' . __CLASS__ . ',function=' . __FUNCTION__ . ',line=' . __LINE__, 'error');
            //trace('url=' . $url . ',param=' . json_encode($param), 'error');
            //trace('response=' . $response, 'error');
            return [];
        }
        $data = [];
        foreach ($json as $itm) {
            $data[$itm['sku']][] = $itm['url'];
        }
        return $data;
    }

    /**
     * 下架SKU
     * @author zhangbin
     * @param string $operator 操作人
     * @param string $sku 下架的SKU
     * @param string $reason 下架的原因
     * @param string $remark 下架的备注
     * @return bool
     */
    static public function downSku($operator, $sku, $reason, $remark = '')
    {
        $url = config('skuapi') . 'index.php?s=skubase/sku/downSku';
        $param = [];
        $param['operator'] = $operator;
        $param['sku'] = $sku;
        $param['reason'] = $reason;
        $param['remark'] = $remark;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('class=' . __CLASS__ . ',function=' . __FUNCTION__ . ',line=' . __LINE__, 'error');
            trace('url=' . $url . ',param=' . json_encode($param), 'error');
            trace('response=' . $response, 'error');
            return false;
        }
        return true;
    }

    /**
     * 根据SKU查询SKU税率
     * @author zhangbin
     * @param array $sku SKU数组
     * @return array
     */
    static public function getTaxrateBySku($sku)
    {
        $url = config('skuapi') . 'index.php?s=skubase/sku/getTaxrateBySku';
        $param['sku'] = $sku;
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

}
