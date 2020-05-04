<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
/**
 * 退出并且返回JSON数据
 * @param string $state 状态码
 * @param string $msg 消息
 * @param array  $data 数据
 * @author dwj
 */
function exitJson($code = 0, $msg = '操作成功', $data = null)
{
    $jdata = array();
    $jdata['code'] = $code;
    $jdata['msg'] = $msg;
    if (!empty($data['count'])) $jdata['count'] = $data['count'];
    if (!empty($data['list'])) $jdata['data'] = $data['list'];
    if (!empty($data['totalRow'])) $jdata['totalRow'] = $data['totalRow']; // 统计数据 {'field' : 'value'}
    exitJson_data($jdata);
}

/**
 * 退出并且返回JSON数据
 * @param $data
 * @author dwj
 */
function exitJson_data($data)
{
    exit(json_encode($data, JSON_UNESCAPED_UNICODE));
}

/**
 * 返回格式化数组
 * @param string $code
 * @param string $msg
 * @param array  $data
 * @param null   $count
 * @return array
 * @author dwj
 */
function returnArr($code = '1', $msg = '成功', $data = [], $count = null)
{
    $res = array();
    $res['code'] = $code;
    if (is_array($msg)){
        $res['msg'] = join('<br/>', $msg);
    }else{
        $res['msg'] = $msg;
    }
    $res['data'] = $data;
    if (isset($count)){
        $res['count'] = $count;
    }
    return $res;
}

/**
 * 模拟CURL请求
 * @param string       $url 路径
 * @param array|string $data 数据
 * @param array        $httpHeader 请求头部信息
 * @param string       $method 方式
 * @param int          $timeout 超时时间
 * @return string
 */
function curl($url, $data, $httpHeader = array(), $method = 'POST', $timeout = 300)
{
    $ch = curl_init();
    $timeout = $timeout <= 0 ? 300 : $timeout;
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    if (strtoupper($method) == 'POST'){
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }else{
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($data));
    }
    if (!empty($httpHeader) && is_array($httpHeader)){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    // https请求 不验证证书和hosts
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $response = curl_exec($ch);
    $http = curl_getinfo($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if (empty($error) && $http['http_code'] == 200){
        return $response;
    }else{
        if ($timeout != 1){
            trace("http_code:{$http['http_code']} error:{$error}", 'error');
        }
        return $response;
    }
}

//多线程curl请求
function curlMulti($url, $data, $timeout = 300)
{
    $res = array();
    $mh = curl_multi_init();//创建多个curl语柄
    $count = count($data);
    for($k = 0; $k < $count; $k ++){
        $json = json_encode($data[$k]);
        $conn[$k] = curl_init($url);
        curl_setopt($conn[$k], CURLOPT_TIMEOUT, $timeout);//设置超时时间
        curl_setopt($conn[$k], CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($conn[$k], CURLOPT_MAXREDIRS, 7);//HTTp定向级别
        curl_setopt($conn[$k], CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
        curl_setopt($conn[$k], CURLOPT_SSL_VERIFYHOST, 0); // 不检查证书中是否设置域名
        curl_setopt($conn[$k], CURLOPT_SSL_VERIFYPEER, false);// 跳过证书检查
        curl_setopt($conn[$k], CURLOPT_RETURNTRANSFER, 1);//获取的信息以文件流的形式返回，而不是直接输出
        curl_setopt($conn[$k], CURLOPT_HEADER, false);//启用时会将头文件的信息作为数据流输出
        curl_setopt($conn[$k], CURLOPT_POSTFIELDS, $json);
        curl_setopt($conn[$k], CURLOPT_POST, 1);
        curl_setopt($conn[$k], CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_multi_add_handle($mh, $conn[$k]);
    }
    // 执行批处理句柄
    $active = null;
    $data = [];
    do{
        curl_multi_exec($mh, $active);//当无数据，active=true
    }while ($active > 0);//当正在接受数据时
    for($k = 0; $k < $count; $k ++){
        $http = curl_getinfo($conn[$k]);
        $error = curl_error($conn[$k]);
        if (empty($error) && $http['http_code'] == 200){
            $res[$k] = curl_multi_getcontent($conn[$k]);//获得返回信息
            $v1 = json_decode($res[$k], true);
            if (is_array($v1) && isset($v1['state']) && $v1['state'] == '000001' && !empty($v1['data'])){
                $data = array_merge($data, $v1['data']);
            }
        }else{
            trace("http_code:{$http['http_code']} error:{$error}", 'error');
        }
        curl_multi_remove_handle($mh, $conn[$k]);//释放资源
        curl_close($conn[$k]);//关闭语柄
    }
    curl_multi_close($mh);
    return $data;
}

/**
 * 获取毫秒
 * @param date $datetime 日期，如：2018-11-07
 * @return string
 */
function get_millitime($datetime)
{
    $time = strtotime($datetime);
    if ($time <= 0){
        return '0';
    }
    $time .= '000';
    return $time;
}

//如果时间没有返回空
function changeTime($datetime)
{
    if ($datetime == '1970-01-01 00:00:00' || $datetime == '0000-00-00 00:00:00'){
        return '';
    }
    return $datetime;
}

//图片上传处理
function upload_file($file)
{
    $url = config('yksFileUpload');
    $data = array('file' => new \CURLFile(realpath($file)));
    $data['file']->setPostFilename(basename($file));
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data']);
    $result = curl_exec($curl);
    $error = curl_error($curl);
    if (empty($error)){
        $res = json_decode($result, true);
        $res['data'][0]['awsPath'] = $res['data'][0]['path'];
        return $res;
    }else{
        trace($error, 'error');
        return ['state' => 500, 'msg' => $error];
    }
}

/**
 * 验证参数
 * @param       $param
 * @param array $checkField
 * @param bool  $is_need_return
 * @return array
 * @author dwj
 */
function checkParam($param, $checkField = [], $is_need_return = false)
{
    if ($is_need_return){
        foreach($checkField as $field){
            if (!isset($param[$field]) || (empty($param[$field]) && $param[$field] != '0')){
                return array("state" => "000100", "msg" => $field . '必填');
            }
        }
        return array("state" => "000001", "msg" => '');
    }else{
        foreach($checkField as $field){
            if (!isset($param[$field]) || (empty($param[$field]) && $param[$field] != '0')){
                exitJson('000100', $field . '必填');
            }
        }
    }
}

//处理接口返回
function curlData($res, $log = '')
{
    $json = json_decode($res, true);
    if (is_array($json) && isset($json['state']) && $json['state'] !== '000001'){
        $backtrace = debug_backtrace(false, 1);
        $file = '';
        if (!empty($backtrace[0]) && !empty($backtrace[0]['file']) && !empty($backtrace[0]['line'])){
            $file = "file=>  {$backtrace[0]['file']}   line=>  {$backtrace[0]['line']} " . PHP_EOL;
        }
        \think\Log::record($file . $res . $log, 'error');
        return [];
    }else{
        if (isset($json['data'])){
            return $json['data'];
        }else{
            return [];
        }
    }
}
