<?php
/**
 * Description
 * User: dwj
 * Date: 2020/4/15
 * Time: 11:07 AM
 */

namespace app\index\model;

use app\common\model\ToolModel;
use think\Model;

class TableModel extends Model
{
    static function dbGetSkuList($param)
    {
        $result = array('count' => 0,'list' => []);
        $where = "WHERE 1 = 1 ";
        $page = $param['page'] ?? 1;
        $limit_num = $param['limit'] ?? 15;
        $sku = $param['sku'] ?? '';
        $operator = $param['operator'] ?? '';
        $offset = ($page - 1) * $limit_num;
        $limit = " LIMIT $offset,$limit_num";

        if (!empty($sku)){
            $where .= " AND sku = '{$sku}'";
        }
        if (!empty($operator)){
            $where .= " AND operator = '{$operator}'";
        }
        $count_sql = "SELECT count(*) as num,SUM(reference_price) as reference_price_total FROM skus {$where}";
        $sql = "SELECT id,sku,name,reference_price,status,sales_status,
        CASE status WHEN 0 THEN '禁用' WHEN 1 THEN '启用' END statusName,
        CASE sales_status WHEN 1 THEN '正常在售' WHEN 2 THEN '清库存' WHEN 3 THEN '下架' ELSE '其他' END salesStatusName,
        operator,creation_time FROM skus {$where} ORDER BY id DESC {$limit}";

        $info = ToolModel::queryData($count_sql);
        $result['count'] = $info[0]['num'] ?? 0;
        $reference_price_total = $info[0]['reference_price_total'] ?? '';
        $result['list'] = ToolModel::queryData($sql);
        $totalRow = array('reference_price' => round($reference_price_total,'2'));
        $result['totalRow'] = $totalRow;

        return $result;
    }

}