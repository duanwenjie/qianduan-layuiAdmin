<?php

namespace app\common\service;

/**
 * Description of MotanService
 * @author zhangbin 2018-11-9
 */
class MotanService
{

    /**
     * 执行
     * @author zhangbin
     * @param \Motan\URL $url 请求的URL
     * @param array $data 请求的数据
     * @param string $action 方法
     * @return array|false
     */
    static public function execute($url, $data, $action = 'query')
    {
        $json='';
        try {
            $urlObj = new \Motan\URL($url);
            $urlObj->setVersion('1.0');
            $urlObj->setReadTimeOut(30);
            $cx = new \Motan\Client($urlObj);
            $json = json_encode($data);
            $response = $cx->$action($json);
            
            $result = json_decode($response, true);
            if (!$result) {
                return false;
            }
            return $result;
        } catch (\Exception $ex) {
            trace('motan execute false,url=' . $url . ',json=' . $json, 'error');
            trace($ex->getMessage(), 'error');
            return false;
        }
    }

}
