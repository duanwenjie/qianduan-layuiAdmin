<?php
/**
 * Description
 * User: dwj
 * Date: 2020/4/15
 * Time: 11:07 AM
 */

namespace app\index\service;
use app\index\model\TableModel;

class TableService
{
    static public function getSkuList($param)
    {

        $data = TableModel::dbGetSkuList($param);

        return $data;
    }
    static public function getCitys($param)
    {
        $type = $param['type'] ?? 1;
        $str = '[{
                    "name": "北京", 
                    "value": 1, 
                    "children": [
                        {"name": "北京市1", "value": 12, "children": [
                            {"name": "朝阳区1", "value": 13, "children": []},
                            {"name": "朝阳区2", "value": 14, "children": []},
                            {"name": "朝阳区3", "value": 15, "children": []},
                            {"name": "朝阳区4", "value": 16, "children": []}
                        ]},
                        {"name": "北京市2", "value": 17, "children": []},
                        {"name": "北京市3", "value": 18, "children": []},
                        {"name": "北京市4", "value": 19, "children": []}
                    ]
                },
                {
                    "name": "天津", 
                    "value": 2, 
                    "children": [
                        {"name": "天津市1", "value": 51, "children": []}
                    ]
                }]';
        $str2 = '[
                    {"name": "分组-1", "type": "optgroup"},
                    {"name": "北京", "value": 1},
                    {"name": "上海", "value": 2},
                    {"name": "分组-2", "type": "optgroup"},
                    {"name": "广州", "value": 3},
                    {"name": "深圳", "value": 4},
                    {"name": "天津", "value": 5}
                ]';
        if ($type == 1){
            $data = json_decode($str,true);
        }else{
            $data = json_decode($str2,true);
        }

        $result = array('code' => 0,'msg' => 'success','data' => $data);
        return $result;
    }

}