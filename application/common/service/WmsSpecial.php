<?php

namespace app\common\service;

use think\Db;

/**
 * wms对pms：异常处理类
 * @author xiongjingyang
 */
class WmsSpecial {

    // 静态实例类
    private static $instance = null;
    private static $map = '';

    //WMS推送三无异常数据给PMS
    public static function getThreeOneData($data) {
        $resultData = [];
        $errorflag = 0;
        $list = [];
        $where = [];
        $arriveData = [];
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $secial = [];
                $secial['special_case_sn'] = $value['specialCaseSn'];
                $secial['special_type'] = 'THREE_NO';
                $secial['special_images'] = $value['specialCaseImages'];
                $secial['warehouse_code'] = $value['warehouseCode'];
                $secial['case_status'] = 1;    //待采购决策
                $secial['create_username'] = $value['createUsername'];
                $secial['create_time'] = $value['createTime'];
                $secial['arrive_neno'] = $value['arriveMemo'];
                $trackNumber=!empty($value['trackNumber']) ? $value['trackNumber']:'';
                $liableUsername=!empty($value['liableUsername']) ? $value['liableUsername']:'';
                $secial['data']= json_encode(["track_number"=>$trackNumber,"liableUsername"=>$liableUsername]);
                $secial['track_number'] = $trackNumber;
                $secial['liable_user_name'] = $liableUsername;
                $arriveData[$value['specialCaseSn'] . "_" . $value['warehouseCode']] = $secial;
                $resultData[] = ['specialCaseSn' => $value['specialCaseSn'], 'apiCode' => ''];
                $list[] = $secial;
            }
        } else {
            return ["state" => "000002", "data" => [], "msg" => "参数为空"];
        }
        \app\common\model\ToolModel::startTrans(); //开启事务机制
        $result = \app\common\model\ToolModel::dbInsertOrUpdate('special_case', $list);
        if ($result === false) {
            \app\common\model\ToolModel::rollback();
            return ["state" => "000000", "data" => [], "msg" => "操作失败"];
        } else {
            \app\common\model\ToolModel::commit();
            return ["state" => "000001", "data" => $resultData, "msg" => "操作成功"];
        }
    }


    /**    //PMS推送异常数据给WMS
     * param1 :$special_case_sn  异常编码
     * param2 :$type  create：创建、update:更新
     * 
     **/
    public static function pushSpecialChoiceData($special_case_sn,$type='create') {
        $where = [];
        $where['special_case_sn'] = $special_case_sn;
        $pushData = [];
        $list = [];
        $condition = self::convertMap($where);
        $join = [['`special_case` case', 'case.id=thread.special_case_id', 'left']];
        $fileds = "case.special_case_sn as specialCaseSn,case.warehouse_code as warehouseCode,case.special_sku as specialSku,case.special_case_quantity as specialQuantity,"
                . "case.special_type as specialType,case.choice_username as choiceUsername,case.choice_time as choiceTime,thread.*";
        $specialData = Db::table('special_case_thread')->alias('thread')->field($fileds)->join($join)->where($condition)->select();
        if (empty($specialData)) {
            return ["state" => "000002", "data" => [], "msg" => "该异常决策不存在"];
        }
        $special = $specialData[0];
        $specialData = array_column($specialData, null, 'id');
        $pushData = [
            "specialCaseSn" => $special['specialCaseSn'],
            "warehouseCode" => $special['warehouseCode'],
            "specialSku" => $special['specialSku'],
            "specialQuantity" => $special['specialQuantity'],
            "choiceUsername" => $special['choiceUsername'],
            "choiceTime" => $special['choiceTime'],
            "choice" => []
        ];
        $detail = [];
        $count = 0;
        $qualifer = [];
        foreach ($specialData as $key => $value) {
            if ($value['is_defective_good']==1) {
                $qualifer['threadQuantity'] = 0;
                $qualifer['threadChoice'] = 'TOGOOD';
                $qualifer['arriveOriginSn'] = $value['arrive_origin_sn'];
                $qualifer['threadMemo'] = $value['supplier_return_address'];
                $qualifer['detail'][] = [
                    "threadQuantity" => $value['thread_quantity'],
                    "threadChoice" => $value['thread_choice'],
                    "arriveOriginSn" => $value['arrive_origin_sn'],
                    "threadMemo" => $value['supplier_return_address']
                ];
                $count += $value['thread_quantity'];
            } else {  
                if($value['thread_choice']=="INPO" && $special['specialType']=='THREE_NO'){//INPO：入采购单(三无产品专用)
                    $detail[$key]['threadChoice'] = 'ORDER';
                }else if($value['thread_choice']=="ORIGINALBACK" && $special['specialType']=='THREE_NO'){   ////原路退回(三无产品专用)
                    $detail[$key]['threadChoice'] = 'BACK'; 
                }else{
                   $detail[$key]['threadChoice'] = $value['thread_choice']; 
                }
                $detail[$key]['threadQuantity'] = $value['thread_quantity'];
                $detail[$key]['arriveOriginSn'] = $value['arrive_origin_sn'];
                $detail[$key]['threadMemo'] = $value['supplier_return_address'];
                $detail[$key]['detail'] = [];
            }
        }
        if($qualifer){
            $qualifer['threadQuantity'] = $count;
        }
        if($detail && $qualifer){
            $detail = array_merge($detail,[$qualifer]);
        }else if(empty($detail) && !empty($qualifer)){
            $detail = [$qualifer]; 
         }else if(!empty($detail) && empty($qualifer)){
            $detail = $detail; 
        }
        sort($detail);
        $pushData['choice'] = $detail;

        $data = json_encode(["data" => [$pushData]]);
        if($type=='create'){
           $url = config("wmsUrl") . "/api/Pms/createSpecialCaseThread"; 
        }else if($type=='update'){
           $url = config("wmsUrl") . "/api/Pms/updateSpeacilCaseThread";
        }
        
        $resultJ = curl($url, $data, array('Content-Type: application/json'));
        $reponse = json_decode($resultJ, true);
        if ($reponse['state'] == "000001") {
            $return = ["state" => "000001", "data" => [], 'msg' => '推送成功'];
        } else {
            trace($resultJ, 'error');
            $return = ["state" => "000011", "data" => [], 'msg' => isset($reponse['msg']) ? $reponse['msg'] : ""];
        }
        return $return;
    }

    public static function convertMap($map) {
        $where = [];
        if (isset($map['special_case_sn'])) {
            $where['case.special_case_sn'] = $map['special_case_sn'];
        }
        if (isset($map['id'])) {
            $where['case.id'] = $map['id'];
        }
        return $where;
    }

    //PMS接收WMS推送的异常处理结果
    public static function getSpecialDealingResult($data) {
        $list=[];
        if (empty($data)) {
            return ["state" => "000002", "data" => [], "msg" => "参数不能为空"];
        }
        $specialData = $data[0];
        try {
            if (empty($specialData['choice'])) {
                return ['state' => '000004', 'msg' => '决策方案结果不能为空'];
            }
            $condition=[
                "special_case_sn"=>$specialData['specialCaseSn'],
                "warehouse_code"=>$specialData['warehouseCode']
            ];
            $specialResult = \app\common\model\ToolModel::findByAttributes('special_case', $condition, 'id,special_type');
            if (empty($specialResult)) {
                return ['state' => '000007', 'msg' => '异常编码不存在'];
            }
            $updateData=[
                "case_status"=>$specialData['caseStatus']
            ];
            $updateThread = \app\common\model\ToolModel::updateData($condition, $updateData, 'special_case');
            if ($updateThread === false) {
                Db::rollback();
                return ['state' => '000008', 'msg' => '结果更新失败'];
            }
            
            $treadArr=array_column($specialData['choice'],null,'threadChoice');
            if(in_array("TOGOOD",array_keys($treadArr))){
                $list=$treadArr['TOGOOD']['detail'];
            }
            unset($treadArr['TOGOOD']);
            if($treadArr && $list){
                sort($treadArr);
                $special=array_merge($treadArr,$list);
            }else if(empty($treadArr) && $list){
                $special=$list;
            }else if(!empty($treadArr) && empty($list)){
                $special=$treadArr;
            }
            foreach ($special as $key => $value) {
                if($specialResult['special_type']=='THREE_NO' && $value['threadChoice']=='BACK'){
                    $threadChoice='ORIGINALBACK';
                }else if($specialResult['special_type']=='THREE_NO' && $value['threadChoice']=='ORDER'){
                    $threadChoice='INPO';
                }else{
                    $threadChoice=$value['threadChoice'];
                }
                $condition = [
                    "special_case_id" => $specialResult['id'],
                    "thread_choice" => $threadChoice,
                    "is_defective_good"=>$value['isDefectiveGood']
                ];
                $updateData = [
                    "over_time" => $value['overTime'],
                    "over_username" => $value['overUsername'],
                    "over_memo" => $value['overMemo'],
                    "choice_status"=>2
                ];
                $choiceResult = \app\common\model\ToolModel::findByAttributes('special_case_thread', $condition, 'id');
                if ($choiceResult) {
                    $thread = \app\common\model\ToolModel::updateData($condition, $updateData, 'special_case_thread');
                    if ($thread===false) {
                        Db::rollback();
                        return ['state' => '000003', 'msg' => '结果更新失败'];
                    }
                }
            }
            Db::commit();
            return ["state" => "000001", "data" => [], "msg" => "操作成功"];
        } catch (Exception $e) {
            Db::rollback();
            trace($e->getMessage(), 'error');
            return ['state' => '000000', 'msg' => '操作失败'];
        }
    }
    //WMS推送不良品的异常拍照数据给PMS
    public static function getSpecialImagesData($data) {
        $list = [];$errorFlag=0;
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if(empty($value['specialCaseSn']) || empty($value['specialImages'])){
                    $errorFlag++;
                }
                $secial = [];
                $secial['special_case_sn'] = $value['specialCaseSn'];
                $secial['special_images'] = $value['specialImages'];
                $secial['warehouse_code'] = $value['warehouseCode'];
                $list[] = $secial;
            }
        } else {
            return ["state" => "000002", "data" => [], "msg" => "接收参数为空"];
        }
        if($errorFlag>0){
           return ["state" => "000003", "data" => [], "msg" => "仓库或者异常编码不能为空"]; 
        }
        \app\common\model\ToolModel::startTrans(); //开启事务机制
        $result = \app\common\model\ToolModel::dbInsertOrUpdate('special_case', $list);
        if ($result === false) {
            \app\common\model\ToolModel::rollback();
            return ["state" => "000000", "data" => [], "msg" => "操作失败"];
        } else {
            \app\common\model\ToolModel::commit();
            return ["state" => "000001", "data" => [], "msg" => "操作成功"];
        }
    }
}
