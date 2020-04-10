<?php

namespace app\common\service;
use think\Db;
/**
 * User
 * @author xiongjingyang
 */
class WmsPurchaseorder
{
    // 静态实例类
    private static $instance = null;
     /**
     * pms向wms推送采购单
     * @author xiongjingyang
     * @return array 
     */
    static public function pushWmsPurchaseorder($purchaseorder)
    {
        $return=[];$data=[];$error_flag = false;
        $purchaseList=self::combinedData($purchaseorder,'add');
      //  sort($purchaseList);
        $data['data']=$purchaseList;
        $data['sign']="81c4b6e5fd80424289f81a1f3d068e54";
        $data=json_encode($data);
        $url = config("wmsUrl") . "/api/Pms/createArriveNotice";   
        $resultJ=curl($url,$data, array('Content-Type: application/json'));
        $result=json_decode($resultJ,true);
        if(is_array($result)&&isset($result['state'])){
            if($result['state']=="000001"){
                $return=["state"=>"000001","data"=>$result['data'],'msg'=>'成功'];
            }else{
                $return=["state"=>"000011","data"=>[],'msg'=>$result['msg']];
            }
        }else{
            //trace($resultJ, 'error');
           $return=["state"=>"000011","data"=>[],'msg'=>'接口报错'];
        }
        self::pushDataLog($result,$data,1);
        return $return;
      //  return self::handleResult($return);
    }
    static public function handleResult($return,$type='add'){
        $data=[];$flag=1;
        if($return['state']=="000001" && $return['data']){
            $pArr=explode(",",$return['data']['arriveNoticeSn']);
            
            foreach($pArr as $key=>$value){
                $data[$key]['purchaseorder_sn']=$value;
                if($type=='add'){
                   $data[$key]['push_type']=1;
                }else if($type=='cancel'){
                   $data[$key]['push_type']=2;
                }else if($type=='handleOver'){
                   $data[$key]['push_type']=3;
                }
                $data[$key]['is_push']=1;
            }    
            
        }else{
            return ["state"=>"000000","data"=>[],'msg'=>$return['msg']];
        }
        try {
            $flag=1;
            foreach($data as $key=>$value){
                $condition=[];
                $condition['purchaseorder_sn']=$value['purchaseorder_sn'];
                $condition['push_type']=$value['push_type'];
                $select=\app\common\model\ToolModel::findByAttributes('pmspushwms',$condition);
                if(!empty($select)){
                    $value['response']=json_encode($return);
                    $res=\app\common\model\ToolModel::updateData($condition,$value,'pmspushwms');
                }
            }
        } catch (\Exception $ex) {
             $flag=0;
        }
        if($flag==1){
           return ["state"=>"000001","data"=>[],'msg'=>'推送成功']; 
        }else{
           return ["state"=>"000000","data"=>[],'msg'=>"推送失败"];
        }
    }
    //获取wms推送的收货信息
    public static function getReceiveData($data){
        self::recordLog('receive',$data);
        $resultData=[];$errorflag=0;
        $list=[];$where=[];$arriveData=[];
        if(!empty($data)){
            foreach($data as $key=>$value){
                $one=[];
                $one['arrive_process_sn'] = $value['arriveProcessSn'];
                $one['arrive_origin_sn'] = $value['arriveOriginSn'];
                $one['warehouse_code'] = $value['warehouseCode'];
                $one['recieve_time'] = $value['receiveTime'];
                $one['receive_username'] = $value['receiveUsername'];
                $one['sku'] = $value['sku'];
                $one['receive_quantity'] = $value['receiveQuantity'];
                $one['recieve_memo'] = $value['receiveMemo'];
                $isExists=self::verificatePurchaseOrder($value['arriveOriginSn'],$value['sku']);
                if($isExists==0){
                    return ["state"=>"000009","data"=>[],"msg"=>"采购明细不存在"];
                }
                $arriveData[$value['arriveOriginSn']."_".$value['sku']]=[$value['arriveOriginSn'],$value['sku']];
                $resultData[$value['arriveProcessSn']]=['arriveProcessSn'=>$value['arriveProcessSn'],'apiCode'=>''];
                $list[]=$one;
            }   
        }else{
            return ["state"=>"000002","data"=>[],"msg"=>"参数为空"];
        }
        \app\common\model\ToolModel::startTrans();//开启事务机制
        $result=\app\common\model\ToolModel::dbInsertOrUpdate('recieve',$list);
        if($result===false){
            \app\common\model\ToolModel::rollback();
           return ["state"=>"000000","data"=>[],"msg"=>"操作失败"];
        }else{
           $return=self::handlePurchaseorder($arriveData,"recieve");
           if($return['state']!='000001'){
               return ["state"=>"000008","data"=>[],"msg"=>"数据处理异常"];
           }  
           \app\common\model\ToolModel::commit();
           return ["state"=>"000001","data"=>[],"msg"=>"操作成功"];
        }
    }
    //获取wms推送的质检信息
    public static function getQualityData($data){
        self::recordLog('quality',$data);
        $where=[];$list=[];$arriveData=[];
        $special_case=[];$errorFlag=0;$special_Del=[];
        if(!empty($data)){
            foreach($data as $key=>$value){
                $secial=[];$one=[];
                $one['arrive_process_sn'] = $value['arriveProcessSn'];
                $one['arrive_origin_sn'] = $value['arriveOriginSn'];
                $one['contrast_time'] = $value['contrastTime'];
                $one['contrast_username'] = $value['contrastUsername'];
                $one['inspect_time'] = $value['inspectTime'];
                $one['inspect_username'] = $value['inspectUsername'];
                $one['inspect_sku'] = $value['inspectSku'];
                $one['supplier_quantity'] = $value['supplierQuantity'];
                $one['actual_arrive_quantity'] = $value['actualArriveQuantity'];
                $one['st_good_quantity'] = $value['stGoodQuantity'];
                $one['st_unqualified_quantity'] = $value['stUnqualifiedQuantity'];
                $one['unqualified_type'] = $value['unqualifiedType'];
                $one['unqualified_images'] = $value['unqualifiedImages'];
                $one['inspect_memo'] = $value['inspectMemo'];
                $isExists=self::verificatePurchaseOrder($value['arriveOriginSn'],$value['inspectSku']);
                if($isExists==0){
                    return ["state"=>"000009","data"=>[],"msg"=>"采购明细不存在"];
                }
                if($value['specialCase']){
                    foreach($value['specialCase'] as $val){
                        $secial['special_case_sn']=$val['specialCaseSn'];
                        $secial['special_type']=$val['specialCaseType'];
                        $secial['special_sku']=$value['inspectSku'];
                        $secial['special_case_quantity']=$val['specialQuantity'];
                        $secial['arrive_process_sn']=$value['arriveProcessSn'];
                        $secial['warehouse_code']=$value['warehouseCode'];
                        $secial['data']=!empty($val['detail']) ? json_encode($val['detail']) : '';
                        if($secial['special_case_quantity']==0){
                           $secial['case_status']=100; 
                        }else{
                           $secial['case_status']=1;  
                        }
                        $secial['arrive_origin_sn']=$value['arriveOriginSn'];
                        $special_case[$val['specialCaseSn']]=$secial;
                        $special_Del[]=$value['arriveProcessSn'];
                    }  
                }
                $list[]=$one;
                $arriveData[$value['arriveOriginSn']."_".$value['inspectSku']]=[$value['arriveOriginSn'],$value['inspectSku']];
            }   
        }else{
            return ["state"=>"000002","data"=>[],"msg"=>"参数为空"];
        }
        \app\common\model\ToolModel::startTrans();//开启事务机制
        $result=\app\common\model\ToolModel::dbInsertOrUpdate('inspect',$list);
        
        if($result===false){
           $errorFlag=1;
        }
        
        $condition=[];
        if($special_Del){
            $condition['arrive_process_sn']=['in',$special_Del];
            $condition['case_status']=['<>',100];
            $special=\app\common\model\ToolModel::findAllByWhere('special_case',$condition);
            $special=array_column($special,'special_case_sn');
            $delArr=array_diff($special,array_keys($special_case)); //删除的异常数据数组
            $delWhere=[];
            $delWhere['special_case_sn']=['in',$delArr];
            $delSpecial=\app\common\model\ToolModel::updateData($delWhere,['case_status'=>100], 'special_case');
            if($delSpecial===false){
               $errorFlag=1;
            }
        }
        
        $arriveProcessSn=array_column($data,'arriveProcessSn');
        $where['arrive_process_sn']=['in',$arriveProcessSn];
        $selectResult=\app\common\model\ToolModel::findAllByWhere('inspect', $where);
        $inspectArr=array_column($selectResult,'id','arrive_process_sn');
        if($special_case){
            foreach($special_case as $k=>&$v){
                if(isset($inspectArr[$v['arrive_process_sn']]) && !empty($inspectArr[$v['arrive_process_sn']])){
                    $v['inspect_id']=isset($inspectArr[$v['arrive_process_sn']]) ? $inspectArr[$v['arrive_process_sn']]:0;
                } 
            }
            sort($special_case);
            $resultSpecial=\app\common\model\ToolModel::dbInsertOrUpdate('special_case',$special_case);
            if($resultSpecial===false){
               $errorFlag=1;
            }
        } 
        if($errorFlag==0){    
          $return=self::handlePurchaseorder($arriveData,"inspect");
           if($return['state']!='000001'){
               return ["state"=>"000008","data"=>[],"msg"=>"数据处理异常"];
           } 
           \app\common\model\ToolModel::commit(); 
          return ["state"=>"000001","data"=>[],"msg"=>"操作成功"];
        }else{
           \app\common\model\ToolModel::rollback();  
           return ["state"=>"000000","data"=>[],"msg"=>"操作失败"];
        }  
    }
     //获取wms推送的上架信息
    public static function getShelveData($data){
        self::recordLog('shelve',$data);
        $resultData=[];$paramFlag=0; $arriveData=[]; 
        if(!empty($data)){
            foreach($data as $key=>$value){  
                
                if(isset($value['detail']) && !empty($value['detail'])){
                    foreach($value['detail'] as $key=>$val){
                        $one=[];
                        $one['sku'] = $val['sku'];
                        $one['shelve_quantity'] = $val['shelveQuantity'];
                        $one['shelve_detail_id'] = $val['shelveDetailId'];
                        $one['arrive_origin_sn'] = $value['arriveOriginSn'];
                        $one['warehouse_code'] = $value['warehouseCode'];
                        $one['shelve_time'] = $value['shelveTime'];
                        $one['shelve_username'] = $value['shelveUsername'];
                        $list[]=$one;
                        $isExists=self::verificatePurchaseOrder($value['arriveOriginSn'],$one['sku']);
                        if($isExists==0){
                            return ["state"=>"000009","data"=>[],"msg"=>"采购明细不存在"];
                        }
                        $arriveData[$value['arriveOriginSn']."_".$one['sku']]=[$value['arriveOriginSn'],$one['sku'],$one['shelve_time']];
                    }
                   
                }else{
                    $paramFlag=1;
                }
                $resultData[]=['apiCode'=>''];
            }   
        }else{
            return ["state"=>"000002","data"=>[],"msg"=>"参数异常"];
        }
        if($paramFlag==1){
            return ["state"=>"000002","data"=>[],"msg"=>"上架明细异常"];
        }
         \app\common\model\ToolModel::startTrans();
        $result=\app\common\model\ToolModel::dbInsertOrUpdate('shelve',$list);
        if($result===false){
             \app\common\model\ToolModel::rollback();
           return ["state"=>"000000","data"=>$resultData,"msg"=>"操作失败"]; 
        }else{
           $return=self::handlePurchaseorder($arriveData,"shelve");
           if($return['state']!='000001'){
               return ["state"=>"000008","data"=>[],"msg"=>"数据处理异常"];
           }
           \app\common\model\ToolModel::commit();
           return ["state"=>"000001","data"=>$resultData,"msg"=>"操作成功"];
        }   
    }
    public static function __callStatic($method, $params){
        if (is_null(self::$instance)) {
            self::$instance = new WmsPurchaseorder();
            call_user_func([self::$instance, $method], $params); 
        }
    }
    public static function combinedData($purchaseorder,$type='add'){
        $purchaseList=[];$where=[];$condition=[];
        $fileds="po.id as arriveOriginSn,po.create_by as username,po.is_package_material,"
                . "po.create_time  as arriveNoticeTime,po.supplier_id as supplierId,"
                . "po.supplier_name as supplierName,po.enterprise_dominant as enterpriseDominant,"
                . "po.warehouse_id as warehouseCode,pd.delivery_date as estimateArriveDate,"
                . "po.remark as arriveRemark,po.money as purchaseMoney,po.transportation_expense as transportFee,"
                . "pd.sku as sku,pd.sku_name as cnName,pd.single_price as purchaseSinglePrice,pd.quantity as purchaseQuantity,"
                . "pd.single_price as currentCost,pd.sku_unit as unit,pd.id as purchaseorder_detail_id,pd.ware_quantity as shelveQuantity";
        $join=[["purchaseorder_detail pd","po.id=pd.purchaseorder_id","left"]];
        if($type=="add"){
            $where['po.id']=["in",$purchaseorder];
            $where['pd.state']=["<",11]; 
        }else{
            $where['pd.id']=["in",$purchaseorder];
        }
        $result = Db::table("purchaseorder")->alias('po')->field($fileds)->join($join)->where($where)->select();
        $purchaseList=array_column($result,null,'arriveOriginSn');
        $purchaseList=[];$detail=[];
        if($result){
            foreach($result as $key=>$value){
               $purchaseList[$value['arriveOriginSn']]['arriveOriginSn']=$value['arriveOriginSn'];
               $purchaseList[$value['arriveOriginSn']]['username']=$value['username'];
               $purchaseList[$value['arriveOriginSn']]['arriveNoticeTime']=$value['arriveNoticeTime'];
               $purchaseList[$value['arriveOriginSn']]['supplierId']=$value['supplierId'];
               $purchaseList[$value['arriveOriginSn']]['supplierName']=$value['supplierName'];
               $purchaseList[$value['arriveOriginSn']]['enterpriseDominant']=$value['enterpriseDominant'];
               $purchaseList[$value['arriveOriginSn']]['warehouseCode']=$value['warehouseCode'];
               $purchaseList[$value['arriveOriginSn']]['estimateArriveDate']=$value['estimateArriveDate'];
               $purchaseList[$value['arriveOriginSn']]['arriveRemark']=$value['arriveRemark'];
               $purchaseList[$value['arriveOriginSn']]['purchaseMoney']=$value['purchaseMoney'];
               $purchaseList[$value['arriveOriginSn']]['transportFee']=$value['transportFee'];
               $purchaseList[$value['arriveOriginSn']]['shippingCode']="";
               $purchaseList[$value['arriveOriginSn']]['arriveType']=($value['is_package_material']==1) ? 'PACKAGE_MATERIAL':'NORMAL';
               $detail['sku']=$value['sku'];
               $detail['cnName']=$value['cnName'];
               $detail['purchaseSinglePrice']=$value['purchaseSinglePrice'];
               $detail['unit']=$value['unit'];
               $detail['purchaseQuantity']=$value['purchaseQuantity'];
               $detail['currentCost']=$value['currentCost'];
               $detail['shelveQuantity']=$value['shelveQuantity'];
               if($type=='cancel'){
                   $detail['purchaseQuantity']=0;
               }
               if($type=='cancel' || $type=='handleOver'){
                   $detail['overTime']=date('Y-m-d H:i:s'); 
               }else if($type=='add'){
                   $detail['overTime']=''; 
               }
               $purchaseList[$value['arriveOriginSn']]['detail'][]=$detail;
            } 
        }
        
        sort($purchaseList);
        return $purchaseList;  
    }
    //PMS修改采购合同数据推送到WMS
    public static function operateOrder($purchaseorder,$type='cancel'){
        $purchaseList=self::combinedData($purchaseorder,$type);
        $data['data']=$purchaseList;
        $data['sign']="81c4b6e5fd80424289f81a1f3d068e54";
        $data=json_encode($data);
        $url = config("wmsUrl") . "api/Pms/updateArriveNotice"; 
        $resultJ=curl($url,$data, array('Content-Type: application/json'));
        $result=json_decode($resultJ,true);
        if($type=="cancel"){
            $push_type=2;$msg="取消";
        }else if($type=="handleOver"){
            $push_type=3;$msg="手动完结";
        }
        if($result['state']=="000001"){
           $return=["state"=>"000001","msg"=>$msg.'成功','data'=>[]];
        }else{
           //trace($resultJ, 'error');
           $return=["state"=>"000000","msg"=>$result['msg'],'data'=>[]];
        } 
        self::pushDataLog($result,$data,$push_type);
        return $return;
    }
    public static function verificatePurchaseOrder($purchaseorder='',$sku=''){
        $where=[];
        if($purchaseorder){
            $where['po.id']=$purchaseorder;
        }
        if($sku){
            $where['pd.sku']=$sku;
        }
        
        $join=[["purchaseorder_detail pd","po.id=pd.purchaseorder_id","left"]];
        $result = Db::table("purchaseorder")->alias('po')->field('po.id,pd.sku,pd.purchaseorder_id')->join($join)->where($where)->select();
        if($result){
            return 1;
        }else{
            return 0;
        }  
    }
    public static function handlePurchaseorder($purchaseorder=[],$type="recieve"){
        $errorFlag=0;$shelveTime='';
        \app\common\model\ToolModel::startTrans();//开启事务机制
        foreach($purchaseorder as $key=>$value){
            $where=[];$update=[];
            $where['arrive_origin_sn']=$value[0];
            
            if($type=='recieve'){
                $fields="sum(receive_quantity) as recieve_quantity";
                $name='recieve_quantity';
                $table="recieve";
                $where['sku']=$value[1];
            }else if($type=='inspect'){   //质检
                $fields="sum(actual_arrive_quantity) as check_quantity";
                $name='check_quantity';
                $table="inspect"; 
                $where['inspect_sku']=$value[1];
            }else if($type=='shelve'){   //入库
                $fields="sum(shelve_quantity) as ware_quantity";
                $where['shelve_type']=10;
                $name='ware_quantity';
                $table="shelve";
                $where['sku']=$value[1];
                $shelveTime=isset($value[2]) ? $value[2] : '';
            }
            $result = \app\common\model\ToolModel::findByAttributes($table,$where,$fields);
            $resulName=empty($result[$name]) ? 0 : intval($result[$name]);
            if($shelveTime){
                $result['last_ware_date']=$shelveTime;
            }
            $where=[];
            $where['purchaseorder_id']=$value[0];
            $where['sku']=$value[1];
            $baseNum=\app\common\model\ToolModel::findByAttributes('purchaseorder_detail_base_num',$where);
            $base=(isset($baseNum[$name]) && !empty($baseNum[$name])) ? intval($baseNum[$name]) : 0;
            $result[$name]=$resulName+$base;
            try {
                $json=\app\common\model\ToolModel::updateData($where,$result,'purchaseorder_detail');
                if($json===false){
                   $errorFlag=1;
                }

                $detail = \app\common\model\ToolModel::findByAttributes('purchaseorder_detail',$where,'quantity,recieve_quantity,check_quantity,ware_quantity,state');
                $update['state']=$detail['state'];
                if($detail && $type=='recieve'){
                   if(($detail['quantity'] > $detail['recieve_quantity']) && $detail['state']<6 && $detail['recieve_quantity']>0){
                       $update['state']=6;
                   }else if(($detail['quantity'] <= $detail['recieve_quantity']) && $detail['state']<7 && $detail['recieve_quantity']>0){
                       $update['state']=7;
                   }else if($detail['recieve_quantity']==0){
                       $update['state']=5;
                   }
                }else if($detail['state']<10 && $type=='inspect'){
                       $update['state']=9;
                }else if($detail['state']<11 && $type=='shelve'){
                    if($detail['quantity']>$detail['ware_quantity']){
                        $update['state']=10;
                    }else if($detail['quantity']<=$detail['ware_quantity']){
                        $update['state']=11;
                    }
                }
              
               if($type=='inspect'){
                    $condition=[];
                    $condition['inspect_sku']=$value[1];
                    $condition['arrive_origin_sn']=$value[0];
                    $return_quantity = \app\common\model\ToolModel::findByAttributes('inspect',$condition,'sum(st_unqualified_quantity) return_quantity');
                    $update['return_quantity']=!empty($return_quantity['return_quantity']) ? $return_quantity['return_quantity']:0; 
               }
                $detailState=\app\common\model\ToolModel::updateData($where,$update,'purchaseorder_detail');     
                if($detailState===false){
                    $errorFlag=1;
                }
            } catch (\Exception $ex) {
                trace($ex->getMessage(), 'error');
                $errorFlag=1;
            }
        }
        $condition=[];
        if($type=='shelve'){
            foreach($purchaseorder as $key=>$value){
                $update=[];
                $condition['purchaseorder_id']=$value[0];
                $field="count(id) as Tcount,count(IF(state =11,TRUE,NULL)) AS overCount,count(if(state=12,true,null)) as handleCount,count(if(state=13,true,null)) as cancelCount";
                $purchaseSelect=\app\common\model\ToolModel::findByAttributes('purchaseorder_detail',$condition,$field);
                $over=$purchaseSelect['Tcount']-$purchaseSelect['overCount']-$purchaseSelect['cancelCount']-$purchaseSelect['handleCount'];
                if($over==0 && $purchaseSelect['handleCount']>0){
                    $update['state']=12;
                }else if($over==0 && $purchaseSelect['overCount']>0){
                    $update['state']=11;
                }else if($over==0 && $purchaseSelect['cancelCount']>0){
                    $update['state']=13;
                }
                if($purchaseSelect['Tcount']>0 && $over==0){
                    $condition=[
                        "id"=>$value[0]
                    ];
                    $state=\app\common\model\ToolModel::updateData($condition,$update,'purchaseorder');
                    if($state===false){
                       $errorFlag=1; 
                    }
                } 
            }
        }
       if($errorFlag==1){
           \app\common\model\ToolModel::rollback();
           return ["state"=>"000000","msg"=>'处理失败','data'=>[]];
       }else{
           \app\common\model\ToolModel::commit();
           return ["state"=>"000001","msg"=>'处理成功','data'=>[]];
       }
    }
    public static function recordLog($type,$response,$request=''){ 
        $data=[];
        $insertData=[
            "type"=>$type,
            "request"=>$request,
            "response"=>json_encode($response),
            "create_time"=>date('Y-m-d H:i:s')
        ];
        $data[0]=$insertData;
         \app\common\model\ToolModel::dbInsertOrUpdate('curl_api_log',$data);
    }
    public static function pushDataLog($pushData,$request,$type=1){ 
        $data=[];
        if($type==1){
            $url = config("wmsUrl") . "api/Pms/createArriveNotice"; 
        }else{
            $url = config("wmsUrl") . "api/Pms/updateArriveNotice"; 
        }
        
        $insertData=[
            "push_type"=>$type,
            "is_push"=>($pushData['state']=='000001') ?  1: 2,
            "request"=>$request,
            'push_url'=>$url,
            "response"=>json_encode($pushData),
            "create_time"=>date('Y-m-d H:i:s')
        ];
        $data[0]=$insertData;
         \app\common\model\ToolModel::dbInsertOrUpdate('pmspushwms',$data);
    }
    public static function  isOverPurchase($param){
        $data=[];
        if(empty($param)){
            return ["state"=>"000005","msg"=>'参数不能为空'];
        }
        $param['warehouseCode']=isset($param['warehouseCode']) ? $param['warehouseCode'] : '';
        $param['arriveOriginSn']=isset($param['arriveOriginSn']) ? $param['arriveOriginSn'] : '';
        $param['sku']=isset($param['sku']) ? $param['sku'] : '';
        if(empty($param['warehouseCode'])){
            return ["state"=>"000002","msg"=>'仓库不能为空'];
        }
        if(empty($param['arriveOriginSn'])){
            return ["state"=>"000003","msg"=>'采购单不能为空'];
        }
        if(empty($param['sku'])){
            return ["state"=>"000004","msg"=>'SKU不能为空'];
        }
        $data['data']=$param;
        $data['sign']="81c4b6e5fd80424289f81a1f3d068e54";
        $data=json_encode($data);
        $url = config("wmsUrl") . "api/Pms/arriveNoticeProcessDoing"; 
        $resultJ=curl($url,$data, array('Content-Type: application/json'));
        $result=json_decode($resultJ,true);
        return $result;
    }
}
