<?php

namespace app\common\service;

use app\common\ConfigBase;

/**
 * 供应商
 * @author zhangbin 2018-11-6
 */
class SupplierService
{

    /**
     * 根据供应商ID获取供应商跟单用户
     * @author zhangbin
     * @param int $serviceline 业务线 1-国内仓 2-亚马逊 3-海外仓
     * @param array $supplier_id 供应商ID
     * @return array
     */
    static public function getSupplierUserById($serviceline, $supplier_id)
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getSupplierUserById';
        $param['serviceline'] = $serviceline;
        $param['supplier_id'] = $supplier_id;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).'response=' . $response, 'error');
            return [];
        }
        return $json['data'];
    }

    /**
     * 根据SKU获取最低价供应商
     * @author zhangbin
     * @param array $sku SKU数组
     * @return array
     */
    static public function getLowestPriceSupplierBySku($sku)
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getLowestPriceSupplierBySku';
        $param['sku'] = $sku;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).'response=' . $response, 'error');
            return [];
        }
        return $json['data'];
    }

    /**
     * 根据供应商ID获取供应商银行账号
     * @author zhangbin
     * @param array $supplier_id 供应商ID
     * @param int $export_tax_rebate 出口退税类型 0-非出口退税 1-出口退税
     * @return array
     */
    static public function getSupplierBankById($supplier_id, $export_tax_rebate = 0)
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getSupplierBankById';
        $param['export_tax_rebate'] = $export_tax_rebate;
        $param['supplier_id'] = $supplier_id;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).'response=' . $response, 'error');
            return [];
        }
        return $json['data'];
    }

    /**
     * 根据供应商付款帐号ID获取供应商银行账号
     * @author zhangbin
     * @param int $payaccountinfo_id 供应商付款帐号ID
     * @return array
     */
    static public function getSupplierBankByPid($payaccountinfo_id)
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getSupplierBankByPid';
        $param['id'] = $payaccountinfo_id;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).'response=' . $response, 'error');
            return [];
        }
        return $json['data'];
    }

    /**
     * 根据供应商ID获取供应商名称
     * @author zhangbin
     * @param array $supplier_id 供应商ID
     * @return array
     */
    static public function getSupplierNameById($supplier_id)
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getSupplierNameById';
        $param['supplier_id'] = $supplier_id;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).'response=' . $response, 'error');
            return [];
        }
        return $json['data'];
    }

    /**
     * 根据供应商ID获取供应商信息
     * @author zhangbin
     * @param array $supplier_id 供应商ID
     * @return array
     */
    static public function getSupplierInfoById($supplier_id)
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getSupplierInfoById';
        $param['supplier_id'] = $supplier_id;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).'response=' . $response, 'error');
            return [];
        }
        return $json['data'];
    }
    //根据sku和供应商获取 对应的sku信息
    static public function getFieldBySkuSupplier($supplierIds,$sku)
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getFieldSkuSupplier';
        $param['supplierIds'] = $supplierIds;
        $param['sku'] = $sku;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            //trace('url=' . $url . ',param=' . json_encode($param), 'error');
            trace('url=' . $url . ',param=' . json_encode($param).'response=' . $response, 'error');
            return [];
        }
        return $json['data'];
    }
    //根据付款方式获取供应商
    static public function getSupplierByPayWay($payWayIds,$exportTaxRebate=0)
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getSupplierByPayWay';
        $param['exportTaxRebate'] = $exportTaxRebate;
        $param['payWayIds'] = (array)$payWayIds;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).'response=' . $response, 'error');
            return [];
        }
        return $json['data'];
    }
    /**
     * 根据名称模糊查询供应商
     * @author zhangbin
     * @param int $page 页码
     * @param int $limit 查询条数
     * @param string $name 名称
     * @return array
     */
    static public function getSupplierByName($page, $limit, $name = '')
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getSupplierByName';
        $param['pageNumber'] = $page;
        $param['pageData'] = $limit;
        $param['name'] = $name;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).'response=' . $response, 'error');
            return ['total' => '0', 'list' => []];
        }
        return $json['data'];
    }

    /**
     * 查询供应商跟单员配置
     * @author zhangbin
     * @param array $param 参数
     * @return array
     */
    static public function getSupplierMerchandiserList($param = [])
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getSupplierMerchandiserList';
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).'response=' . $response, 'error');
            return ['total' => '0', 'list' => []];
        }
        return $json['data'];
    }

    /**
     * 获取供应商缺货SKU数量
     * @author zhangbin
     * @param array $id 供应商ID
     * @param array $sku SKU
     * @return array
     */
    static public function getSupplierOutofstockCount($supplier_id, $sku)
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getSupplierOutofstockCount';
        $param['supplier_id'] = $supplier_id;
        $param['sku'] = $sku;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).'response=' . $response, 'error');
            return [];
        }
        return $json['data'];
    }

    /**
     * 根据SKU获取供应商SKU
     * @author zhangbin
     * @param array $sku SKU
     * @return array
     */
    static public function getSupplierSkuBySku($sku)
    {
        if(empty($sku)){
            return [];
        }
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getSupplierSkuBySku';
        $param['sku'] = $sku;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).'response=' . $response, 'error');
            return [];
        }
        return $json['data'];
    }

    /**
     * 根据SKU获取供应商、供应商SKU列表
     * @author zhangbin
     * @param array $sku SKU
     * @return array
     */
    static public function getSupplierSkuBySkuList($param = [])
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getSupplierSkuBySkuList';
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).'response=' . $response, 'error');
            return ['total' => '0', 'list' => []];
        }
        return $json['data'];
    }

    /**
     * 根据账号查询供应商跟单员
     * @author zhangbin
     * @param array $param 参数
     * @return array
     */
    static public function getSupplierMerchandiserByName($param = [])
    {
        $default = ['key' => '0', 'label' => '全部'];
        $url = config('skuapi') . 'index.php?s=skubase/supplier/getSupplierMerchandiserByName';
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).';response=' . $response, 'error');
            return ['total' => '0', 'list' => [$default]];
        }
        array_unshift($json['data']['list'], $default);
        return $json['data'];
    }

    /**
     * 根据SKU获取供应商、供应商SKU、付款方式等信息
     * @author zhangbin
     * @param string $sku
     * @param int $export_tax_rebate 出口退税类型 0-非出口退税 1-出口退税
     * @return array
     */
    static public function getSupplierListLogic($param, $export_tax_rebate = 0)
    {
        if (isset($param['sku']) && !empty($param['sku'])) {
            // 根据SKU查下
            $data = self::getSupplierSkuBySkuList($param);
            if (empty($data['list'])) {
                return ['state' => '000001', 'msg' => '操作成功', 'data' => ['total' => '0', 'list' => []]];
            }
            $supplier_id_arr = array_column($data['list'], 'key');
            $bank = self::getSupplierBankById($supplier_id_arr, $export_tax_rebate);
            unset($supplier_id_arr);
            $payway = ConfigBase::getPaymentMethod();
            foreach ($data['list'] as &$itm) {
                $payways_id = isset($bank[$itm['key']]['payways_id']) ? $bank[$itm['key']]['payways_id'] : '';
                $itm['payType'] = isset($payway[$payways_id]) ? $payway[$payways_id] : '';
            }
            unset($bank, $payway);
            return ['state' => '000001', 'msg' => '操作成功', 'data' => $data];
        } else if (isset($param['name']) && !empty($param['name'])) {
            // 根据供应商名称查询
            $page = isset($param['pageNumber']) ? $param['pageNumber'] : 1;
            $limit = isset($param['pageData']) ? $param['pageData'] : 30;
            $data = self::getSupplierByName($page, $limit, $param['name']);
            if (empty($data['list'])) {
                return ['state' => '000001', 'msg' => '操作成功', 'data' => ['total' => '0', 'list' => []]];
            }
            $supplier_id_arr = array_column($data['list'], 'key');
            $supplier_info = self::getSupplierInfoById($supplier_id_arr);
            $supplier_bank = self::getSupplierBankById($supplier_id_arr);
            $payway = ConfigBase::getPaymentMethod();
            foreach ($data['list'] as &$itm) {
                $itm['aliWangWang'] = '';
                $itm['code'] = $itm['key'];
                $itm['contact'] = '';
                $itm['deliveryDay'] = '';
                $itm['level'] = '';
                $itm['price'] = '';
                $itm['qq'] = '';
                $itm['minCount'] = '';
                $itm['name'] = $itm['label'];
                $itm['remark'] = '';
                $itm['sku'] = '';
                $itm['state'] = '已审核';
                $itm['telNumber'] = '';
                $itm['link'] = '';
                $itm['packageSpecification'] = '';
                $payways_id = isset($supplier_bank[$itm['key']]['payways_id']) ? $supplier_bank[$itm['key']]['payways_id'] : '';
                $itm['payType'] = isset($payway[$payways_id]) ? $payway[$payways_id] : '';
                if (isset($supplier_info[$itm['key']])) {
                    $itm['aliWangWang'] = $supplier_info[$itm['key']]['wangwang'];
                    $itm['contact'] = $supplier_info[$itm['key']]['contact'];
                    $itm['level'] = $supplier_info[$itm['key']]['grade'];
                    $itm['qq'] = $supplier_info[$itm['key']]['qq'];
                    $itm['remark'] = $supplier_info[$itm['key']]['remark'];
                    $itm['telNumber'] = $supplier_info[$itm['key']]['mobile'];
                }
                unset($itm['label']);
            }
            unset($supplier_id_arr, $supplier_info, $supplier_bank, $payway);
            return ['state' => '000001', 'msg' => '操作成功', 'data' => $data];
        } else {
            return ['state' => '000001', 'msg' => '操作成功', 'data' => ['total' => '0', 'list' => []]];
        }
    }

    /**
     * 保存供应商SKU
     * @author zhangbin
     * @param string $operator 操作人
     * @param array $supplier_sku 需要更新的供应商SKU
     * @param array $supplier_contact 需要更新的供应商联系人
     * @return bool
     */
    static public function saveSupplierSku($operator, $supplier_sku, $supplier_contact)
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/saveSupplierSku';
        $param = [];
        $param['operator'] = $operator;
        $param['supplier_sku'] = $supplier_sku;
        $param['supplier_contact'] = $supplier_contact;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).';response=' . $response, 'error');
            return false;
        }
        return true;
    }

    /**
     * 新增采购供应商关系
     * @author zhangbin
     * @param array $data 数据
     * @return bool
     */
    static public function addPurchaseSupplier($data)
    {
        $url = config('skuapi') . 'index.php?s=skubase/supplier/addPurchaseSupplier';
        $param = [];
        $param['data'] = $data;
        $response = curl($url, json_encode($param), array('Content-Type: application/json'));
        $json = json_decode($response, TRUE);
        if (!$json || !isset($json['state']) || $json['state'] !== '000001') {
            trace('url=' . $url . ',param=' . json_encode($param).' response=' . $response, 'error');
            return false;
        }
        return true;
    }

}
