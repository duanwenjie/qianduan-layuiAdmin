<?php

namespace app\common\service;

use app\common\model\ToolModel;

/**
 * User
 * @author zhangbin 2018-11-7
 */
class UserService
{

    /**
     * 获取用户名称
     * @author zhangbin
     * @param array $name 用户账号数组
     * @return array 格式如：array('zhangsan' => '张三','lisi' => '李四')
     */
    static public function getUserNick($name, $pageNumber = 1, $pageData = 200)
    {
        $user=self::getUsersAll();
        foreach ($name as $username) {
            if(!isset($user[$username])){
                $user[$username] = $username;
            }
        }
        return $user;
        $user = [];
        foreach ($name as $username) {
            $user[$username] = $username;
        }
        $result = self::getUsersByUserInfo($name, $pageNumber, $pageData);
        if (!empty($result)) {
            foreach ($result as $itm) {
                $user[$itm['userName']] = isset($itm['personName']) ? $itm['personName'] : $itm['userName'];
            }
        }
        return $user;
    }

    /**
     * 获取用户名称(调用第三方的用户模块接口)
     * @author xiongjingyang
     * @param array $name 用户账号数组,使用上网账号（完全匹配）
     * @return array 
     */
    static public function getUsersByUserInfo($name, $pageNumber = 1, $pageData = 200)
    {
/*        $url = \think\Config::get('motan.urc_url');
        $data = [];
        $data['pageNumber'] = 1;
        $data['pageData'] = 20;
        $data['operator'] = 'mengyuhua';
        $data['dingOrgId']=1;
        $result = MotanService::execute($url,$data, 'getUserByDingOrgId');
        var_dump($result);exit();*/
        if (empty($name) || !is_array($name)) {
            return [];
        }
        $url = \think\Config::get('motan.urc_url');
        if (empty($url)) {
            trace('motan.urc_url is empty,line=' . __LINE__, 'error');
            return [];
        }
        $data = [];
        $data['user']['userName'] = implode(',', $name);
        $data['pageNumber'] = $pageNumber;
        $data['pageData'] = $pageData;
        $data['operator'] = 'mengyuhua';
        $result = MotanService::execute($url, $data, 'getUsersByUserInfo');
        if (!$result || !isset($result['data']['lst']) || empty($result['data']['lst'])) {
            return [];
        }
        $list = [];
        foreach ($result['data']['lst'] as $itm) {
            $list[$itm['userName']] = $itm;
        }
        return $list;
    }

    /**
     * 获取用户名称(调用第三方的用户模块接口)
     * @author xiongjingyang
     * @param string $name 用户账号字符串模糊查询
     * @return array 
     */
    static public function fuzzSearchPersonByName($name)
    {
        $url = \think\Config::get('motan.urc_url');
        if (empty($url)) {
            trace('motan.urc_url is empty,line=' . __LINE__, 'error');
            return [];
        }
        $data = [];
        $data['name'] = $name;
        $data['operator'] = 'mengyuhua';
        $result = MotanService::execute($url, $data, 'fuzzSearchPersonByName');

        if (isset($result['data'])) {
            foreach ($result['data'] as $key => &$value) {
                $value['personName'] = isset($value['personName']) ? $value['personName'] : $value['userName'];
            }
        }
        return isset($result['data']) ? $result['data'] : array();
    }

    /**
     * 获取查询采购人员接口
     * @author xiongjingyang
     * @param string $name 用户账号字符串模糊查询
     * @return array 
     */
    static public function getEmployeeByName($param,$type=1)
    {
        $default = ['key' => '0', 'label' => '全部'];
        if($type==2){
            $default = [];
        }
        $pageData = isset($param['pageData']) ? $param['pageData'] : 20;
        $pageNumber = isset($param['pageNumber']) ? $param['pageNumber'] : 1;
        $procurementType = isset($param['procurementType']) ? $param['procurementType'] : 1;
        $businessLines = isset($param['businessLines']) ? $param['businessLines'] : 1;
        $name = isset($param['name']) ? $param['name'] : '';
        if (empty($name)) {
            return ['total' => 0, 'list' => [$default]];
        }
        $res=db('users_info')
            ->field('personName as label,`userName` as `key` ')
            ->where('userName|personName','like',"%{$name}%")
            ->page($pageNumber,$pageData)
            ->select();
        if (empty($res)) {
            return ['total' => 0, 'list' =>[$default]];
        }
        !empty($default)&&array_unshift($res, $default);
        $data['total'] = count($res);
        $data['list'] = $res;
        return $data;
    }

    /**
     * 通过账号名称查询采购人员的昵称
     */
    static public function getOPEmployeeByAccount($account)
    {
        $where = array();
        $data = array();
        if (empty($account)) {
            return 0;
        }
        $where['name'] = $account;
        $result = ToolModel::findByAttributes('users', $where, "nick");
        return isset($result['nick']) ? $result['nick'] : '';
    }

    //获取所有用户
    static public function getUsersByUserInfoAll($pageNumber = 0, $pageData =1000)
    {
        $url = \think\Config::get('motan.urc_url');
        if (empty($url)) {
            trace('motan.urc_url is empty,line=' . __LINE__, 'error');
            return [];
        }
        $data = [];
        $data['pageNumber'] = $pageNumber;
        $data['pageData'] = $pageData;
        $data['operator'] = 'mengyuhua';
        $result = MotanService::execute($url, $data, 'getUsersByUserInfo');
        return $result;
    }
    //返回所有用户
    static public function getUsersAll(){
        $user=cache('userAll');
        if(empty($user)){
            $user=db('users_info')->column('personName','userName');
            cache('userAll',$user);
        }
        return $user;
    }
    /**
     * 检查用户权限
     * @author zhangbin
     * @param string $operator 登录账号
     * @param type $key URC function key
     * @return bool
     */
    static public function checkUserFunc($operator, $key)
    {
        $result = false;
        try {
            $function = self::getAllFuncPermit($operator);
            if (empty($function)) {
                return $result;
            }
            foreach ($function as $itm) {
                if (strcmp($itm['key'], $key) === 0) {
                    $result = true;
                    break;
                }
            }
        } catch (\Exception $ex) {
            trace($ex->getMessage() . ',line=' . __LINE__, 'error');
        }
        return $result;
    }

    /**
     * 获取用户所有权限
     * @author zhangbin
     * @param string $operator 登录账号
     * @return array
     */
    static private function getAllFuncPermit($operator)
    {
        $url = \think\Config::get('motan.urc_url');
        if (empty($url)) {
            trace('motan.urc_url is empty,line=' . __LINE__, 'error');
            return [];
        }
        $data = [];
        $data['operator'] = $operator;
        $result = MotanService::execute($url, $data, 'getAllFuncPermit');
        if (!$result || !isset($result['state']) || $result['state'] !== '000001' ||
            !isset($result['data']['lstSysRoot']) || empty($result['data']['lstSysRoot'])) {
            return [];
        }
        $menu = [];
        foreach ($result['data']['lstSysRoot'] as $itm) {
            $json = json_decode($itm, true);
            if ($json && isset($json['menu']) && isset($json['system']) &&
                isset($json['system']['key']) && $json['system']['key'] === '010') {
                // 采购系统
                $menu = $json['menu'];
                break;
            }
        }
        $func = [];
        if (!empty($menu)) {
            self::getAllFuncByRecursive($menu, $func);
        }
        return $func;
    }

    /**
     * 获取用户所有权限-递归获取
     * @author zhangbin
     * @param array $data 数据
     * @param array $func 功能
     * @param int $level 层级
     */
    static private function getAllFuncByRecursive($data, &$func, $level = 1)
    {
        try {
            if ($level === 1) {
                foreach ($data as $itm) {
                    if (!isset($itm['module']) || empty($itm['module'])) {
                        continue;
                    }
                    ++$level;
                    self::getAllFuncByRecursive($itm['module'], $func, $level);
                }
            } else {
                foreach ($data as $itm) {
                    if (isset($itm['function']) && !empty($itm['function'])) {
                        foreach ($itm['function'] as $fun) {
                            $func[] = $fun;
                        }
                    }
                    if (isset($itm['module']) && !empty($itm['module'])) {
                        ++$level;
                        self::getAllFuncByRecursive($itm['module'], $func, $level);
                    }
                }
            }
        } catch (\Exception $ex) {
            trace($ex->getMessage() . ',line=' . __LINE__, 'error');
        }
        --$level;
    }

    /**
     * 搜索用户中心用户
     * @param string $name 用户名
     * @param int $pageNumber 页码
     * @param int $pageData 条数
     * @return array
     */
    static public function searchUserPerson($name, $pageNumber = 1, $pageData = 20)
    {
        if (empty($name)) {
            return [];
        }
        $url = \think\Config::get('motan.urc_url');
        if (empty($url)) {
            trace('motan.urc_url is empty,line=' . __LINE__, 'error');
            return [];
        }
        $data = [];
        $data['data']['searchContext'] = $name;
        $data['data']['pageNumber'] = $pageNumber;
        $data['data']['pageData'] = $pageData;
        $result = MotanService::execute($url, $data, 'searchUserPerson');
        if (!isset($result['state']) || $result['state'] !== '000001') {
            return [];
        }
        unset($data);
        $data = [];
        $data['total'] = count($result['data']);
        $data['list'] = [];
        foreach ($result['data'] as $itm) {
            $data['list'][] = [
                'key' => $itm['userName'],
                'label' => (isset($itm['personName']) ? $itm['personName'] : $itm['userName'])
            ];
        }
        return $data;
    }
    //获取全部用户 详细信息
    static public function getUserAll()
    {
        $user=[];
        $url = \think\Config::get('motan.urc_url');
        for ($i = 1; $i <20; $i++) {
            $data = [];
            $data['pageNumber'] =$i;
            $data['pageData'] = 1000;
            $data['operator'] = 'mengyuhua';
            $result = MotanService::execute($url,$data, 'getUserByUserInfo');
            if(!empty($result['data']['lst'][0])) {
                $user=array_merge($user,$result['data']['lst']);
            }
        }
        return $user;
    }

}
