<?php

namespace app\common\model;

use think\Db;

class ToolModel
{

    /**
     * 新增或更新数据
     * @param string $tablename 表名
     * @param array $data 数据
     * @param array $update_filter 更新时候需要过滤的字段
     * @param array $db 指定数据库
     */
    static public function dbInsertOrUpdate($tablename, $data, $update_filter = [],$db=[])
    {
        if (empty($tablename) || empty($data)){
            return false;
        }
        $insert_keys = array_keys($data[0]);
        $field_sql = '(`' . implode('`,`', $insert_keys) . '`)';
        $value_sql = '';
        foreach ($data as $itm){
            $itm = array_map(function ($val){
                return "'" . addslashes(trim($val)) . "'";
            }, $itm);
            $value_sql .= '(' . implode(',', $itm) . '),';
        }
        $value_sql = substr($value_sql, 0, -1);
        $update_keys = array_diff($insert_keys, $update_filter);
        $update_sql = '';
        foreach ($update_keys as $key){
            $update_sql .= "`{$key}`=VALUES(`{$key}`),";
        }
        $update_sql = substr($update_sql, 0, -1);
        $sql = "INSERT INTO {$tablename} {$field_sql} VALUES {$value_sql} ON DUPLICATE KEY UPDATE {$update_sql}";
        $result = Db::connect($db)->execute($sql);
        return $result;
    }

    /**
     * 查询一条数据
     * @param string $tablename 表名
     * @param array $data 数据
     * @param array $update_filter 更新时候需要过滤的字段
     */
    static public function find($table, $where = array(), $fileds = '*',$order='')
    {
        return Db::table($table)->field($fileds)->where($where)->order($order)->limit(1)->find();
    }
    //返回字段值 无返回null
    static public function getValue($table, $where = array(), $filed = 'id',$order='')
    {
        return Db::table($table)->where($where)->order($order)->limit(1)->value($filed);
    }
    /**
     * 插入数据
     * @param array $data
     * @return int 如果$data是一维数组的话，返回值是插入的id，如果是二维数组的话，返回插入行数
     */
    static public function insertData($data = array(), $table = '')
    {
        if (!is_array($data)) {
            return 0;
        }
        if (count($data) == count($data, 1)) {
            return Db::name($table)->insertGetId($data);
        } else {
            return Db::name($table)->insertAll($data);
        }
    }

    /**
     * 删除数据
     * @param array $where
     * @return int 返回影响行数
     */
    static public function deleteData($where = array(), $table = '')
    {
        return Db::name($table)->where($where)->delete();
    }

    /**
     * 更新数据
     * @param array $where
     * @return int 返回影响行数
     */
    static public function updateData($where = array(), $data = array(), $table = '')
    {
        return Db::name($table)->where($where)->update($data);
    }

    static public function raw($str){
        return Db::raw($str);
    }

    /**
     * 根据条件查询获得数据
     * @param array $where
     * @param string $fileds
     * @return array 返回二维数组，未找到记录则返回空数组
     */
    static public function findAllByWhere($table, $where = array(), $fileds = "*", $order = "id desc")
    {
        return Db::name($table)->field($fileds)->where($where)->order($order)->select();
    }
    //查询
    static public function select($table, $where = array(), $fileds = "*", $order = "",$key="")
    {
        $db=Db::name($table)->field($fileds)->where($where)->order($order);
        if(empty($key)){
            return $db->select();
        }else{
            return $db->column($fileds,$key);
        }

    }
    /**
     * 查询全部数据有分页查询
     * @param array $where
     * @param string $fileds
     * @param string $offset
     * @param string $num
     * @param string $order
     * @return array 返回二维数组，未找到记录则返回空数组
     */
    static public function loadAllData($table = '', $where, $fileds = '*', $offset = 0, $num = 1, $order = "id desc")
    {
        return Db::table($table)->field($fileds)->where($where)->order($order)->limit("$offset,$num")->select();
    }

    /**
     * 联表查询语句
     * @param array $where
     * @param string $fileds
     * @param array $join $join = [['think_work w','a.id=w.artist_id'],['think_card c','a.card_id=c.id']];
     * @param string $offset
     * @param string $num
     * @param string $order
     *  @param string $pagination   是否有分页
     * @return array 返回二维数组，未找到记录则返回空数组
     */
    static public function joinAllData($table = '', $where, $fileds = '*', $join = array(), $offset = 0, $num = 1, $order = "a.id desc", $pagination = True)
    {
        if ($pagination) {
            $result = Db::table($table)->alias('a')->field($fileds)->join($join)->where($where)->order($order)->limit("$offset,$num")->select();
        } else {
            $result = Db::table($table)->alias('a')->field($fileds)->join($join)->where($where)->order($order)->select();
        }
        return $result;
    }

    /**
     * 原生态查询
     * @param string $sql
     * @return array 返回二维数组，未找到记录则返回false
     */
    static public function queryData($sql)
    {
        return Db::query($sql);
    }

    /**
     * 批量插入数据
     * @param string $tablename 表名
     * @param array $data 数据
     * @return bool
     */
    static public function dbInsertAll($tablename, $data)
    {
        Db::connect()->name($tablename)->insertAll($data);
        return true;
    }

    //事务机制处理
    static public function transaction($callback)
    {
        return Db::transaction($callback);
    }

    static public function startTrans()
    {
        return Db::startTrans();
    }

    static public function rollback()
    {
        return Db::rollback();
    }

    static public function commit()
    {
        return Db::commit();
    }
    // 解锁并关闭文件
    static public function closeFp($fp){
        flock($fp, LOCK_UN);
        fclose($fp);
    }
    //执行原生sql语句
    public static function execute($sql){
        return Db::connect()->execute($sql);
    }
    //复制数组
    public static function copyArr($arr){
        $temp=[];
        foreach ($arr as $k=>$v){
            if(is_array($v)){
                foreach ($v as $k2=>$v2){
                    $temp[$k][$k2]=$v2;
                }
            }else{
                $temp[$k]=$v;
            }
        }
        return $temp;
    }

    /**
     *串行执行,需执行完上一次操作才能继续执行
     * @param $param
     * @return array
     * @author dwj
     */
    static public function lock($param)
    {
        //加文件锁避免多人同时操作
        try {
            if(empty($param['lockTxt'])){
                $ft=str_replace(['\\',"::"],'',$param['lockFunction']);
            }else{
                $ft=$param['lockTxt'];
            }
            $file = FILE_PATH . 'lock'.DS."{$ft}.txt";
            $fp = fopen($file, 'w');
            if ($fp === false) {
                return returnArr(500, '系统繁忙,操作失败!');
            }
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                fwrite($fp , "1");
                self::startTrans();
                $do=1;
                //如没人操作这个文件 就执行
                $res =call_user_func($param['lockFunction'],$param);
                if (is_array($res)&&$res['code'] == 1) {
                    self::commit();
                } else {
                    self::rollback();
                }
                fclose($fp);
                return $res;
            } else {
                //如已经在运行中了 就提示
                fclose($fp);
                return returnArr(500, '上次操作正在执行中,请过几分钟再操作!');
            }
        } catch (\Exception $e) {
            !empty($do)&&self::rollback();
            trace($e->getMessage(), 'error');
            !empty($fp) && fclose($fp);
            return returnArr(600, '失败' . $e->getMessage());
        }
    }
    //串行不回滚
    static public function lockNoRollBack($param)
    {
        //加文件锁避免多人同时操作
        try {
            if(empty($param['lockTxt'])){
                $ft=str_replace(['\\',"::"],'',$param['lockFunction']);
            }else{
                $ft=$param['lockTxt'];
            }
            $file = FILE_PATH . 'lock'.DS."{$ft}.txt";
            $fp = fopen($file, 'w');
            if ($fp === false) {
                return returnArr(500, '系统繁忙,操作失败!');
            }
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                fwrite($fp , "1");
                //如没人操作这个文件 就执行
                $res =call_user_func($param['lockFunction'],$param);
                if (is_array($res)&&$res['code'] == 1) {

                } else {

                }
                fclose($fp);
                return $res;
            } else {
                //如已经在运行中了 就提示
                fclose($fp);
                return returnArr(500, '上次操作正在执行中,请过几分钟再操作!');
            }
        } catch (\Exception $e) {
            trace($e->getMessage(), 'error');
            !empty($fp) && fclose($fp);
            return returnArr(600, '失败' . $e->getMessage());
        }
    }
    /**
     *db带回滚操作
     * @param $param
     * @return array|mixed
     * @author dwj
     */
    static public function dbRollback($param){
        self::startTrans();
        try {
            $ret=call_user_func($param['function'],$param);
            if(!empty($ret)&&is_array($ret)&&$ret['code']==1){
                self::commit();
            }else{
                self::rollback();
            }
            return $ret;
        } catch (\Exception $e) {
            self::rollback();
            $trace=$e->getTrace();
            foreach ($trace as $k=>$v){
                if($k<3){
                    if(isset($v['file'])&&isset($v['line'])){
                        $file[]="file: {$v['file']}   line: {$v['line']}";
                    }
                }
            }
            $file[]="原因: ".$e->getMessage();
            \think\Log::record(join(PHP_EOL,$file), 'error');
            return returnArr(600, '失败' . join("------->",$file));
        }
    }

    static public function dbSkusystemPDO($sql,$list = false){
        try {
            // $pdo = new \PDO("mysql:host=test.skusystemdbm.kokoerp.com;dbname=skusystem;charset=utf8;", 'skusystem', 'RQoRyuSjBW53y5KM');
            //$pdo = new \PDO("mysql:host=skusystemdbs2.kokoerp.com;dbname=skusystem;charset=utf8;", 'read_only', 'gxeGTtUYZ0UAbIb9');
            //$pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            //$res = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $res= Db::connect('skuuu')->query($sql);
            // $res->setFetchMode(\PDO::FETCH_ASSOC);
            // $res->setFetchMode(\PDO::FETCH_NUM); //数字索引方式

            if(isset($res[0]['c'])){
                return $res[0]['c'];
            }else{
                $datas = [];
                foreach ($res as $val) {
                    $sku = strtoupper($val['sku']);
                    if($list){
                        $datas[$sku][] = $val;
                    }else{
                        $datas[$sku] = $val;
                    }
                }

                unset($pdo,$res);
                return $datas;
            }

        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
