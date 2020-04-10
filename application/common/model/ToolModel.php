<?php

namespace app\common\model;

use think\Db;

/**
 * Description of ToolModel
 * @author zhangbin 2018-11-6
 */
class ToolModel
{

    /**
     * 新增或更新数据
     * @param string $tablename 表名
     * @param array $data 数据
     * @param array $update_filter 更新时候需要过滤的字段
     */
    static public function dbInsertOrUpdate($tablename, $data, $update_filter = [])
    {
        if (empty($tablename) || empty($data)) {
            return false;
        }
        $insert_keys = array_keys($data[0]);
        $field_sql = '(' . implode(',', $insert_keys) . ')';
        $value_sql = '';
        foreach ($data as $itm) {
            $itm = array_map(function($val) {
                return "'" . addslashes(trim($val)) . "'";
            }, $itm);
            $value_sql .= '(' . implode(',', $itm) . '),';
        }
        $value_sql = substr($value_sql, 0, -1);
        $update_keys = array_diff($insert_keys, $update_filter);
        $update_sql = '';
        foreach ($update_keys as $key) {
            $update_sql .= "{$key}=VALUES({$key}),";
        }
        $update_sql = substr($update_sql, 0, -1);
        $sql = "INSERT INTO {$tablename} {$field_sql} VALUES {$value_sql} ON DUPLICATE KEY UPDATE {$update_sql}";
        try {
            $result = Db::connect()->execute($sql);
            return $result;
        } catch (\Exception $ex) {
            trace($ex->getMessage(), 'error');
            trace('sql=' . $sql, 'error');
            return false;
        }
    }

    /**
     * 查询一条数据
     * @param string $tablename 表名
     * @param array $data 数据
     * @param array $update_filter 更新时候需要过滤的字段
     */
    static public function findByAttributes($table, $where = array(), $fileds = '*')
    {
        return Db::table($table)->field($fileds)->where($where)->find();
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
        //$subQuery = Db::table('think_user')->field('id,name')->where('id','>',10)->buildSql();  构建sql语句
        return Db::query($sql);
    }

    /**
     * 批量插入数据
     * @author zhangbin
     * @param string $tablename 表名
     * @param array $data 数据
     * @return bool
     */
    static public function dbInsertAll($tablename, $data)
    {
        try {
            Db::connect()->name($tablename)->insertAll($data);
            return true;
        } catch (\Exception $ex) {
            trace($ex->getMessage(), 'error');
            return false;
        }
    }

    //事务机制处理
    static public function transaction($callback)
    {
        return Db::transaction($callback);
    }

    //生成采购单日志
    static public function createLog($data = array())
    {
//        $data = array('operateTime' => time());
//        $data = array_merge($data, $dataLog);
        $data['operateTime'] = time();
        return self::insertData($data, 'operator_log');
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

}
