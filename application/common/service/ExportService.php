<?php

namespace app\common\service;

/**
 * Description of ExportService
 * @author zhangbin 2018-12-26
 */
class ExportService
{

    // 导出的文件标题
    static private $_title;
    // 文件句柄
    static private $_handle;
    // 文件绝对路径
    static private $_file;

    /**
     * 导出文件-开始
     * @author zhangbin
     * @param array $title 标题，格式如：array('id' => '数据ID','sku' => 'SKU')
     * @param string $file_name 文件名，只允许数字字母下划线
     * @return bool
     */
    static public function start($title, $file_name = '')
    {
        try {
            if (!is_array($title)) {
                return false;
            }
            self::$_title = $title;
            $csv_dir = '/tmp/';
            if (!file_exists($csv_dir)) {
                mkdir($csv_dir, 0777, true);
            }
            if (!empty($file_name) && preg_match('/^[0-9a-zA-Z\_]+$/', $file_name)) {
                $csv_name = date('Y-m-d') . '_' . $file_name . '.csv';
            } else {
                $csv_name = date('Y-m-d') . '_' . md5(time() . rand(0, 9999)) . '.csv';
            }
            $csv_file = $csv_dir . iconv('utf-8', 'gbk', $csv_name);
            self::$_handle = fopen($csv_file, 'w');
            // 写入标题
            $string = '';
            foreach ($title as $key => $value) {
                $string .= '"' . $value . '",';
            }
            $string = substr($string, 0, -1) . "\r\n";
            fwrite(self::$_handle, $string);
            self::$_file = $csv_dir . $csv_name;
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * 写入文件
     * @author zhangbin
     * @param array $data 数据，格式如：array(array('id' => '1','sku' => 'A301'),array('id' => '2','sku' => 'A302'))
     * @return string
     */
    static public function writeData($data)
    {
        try {
            // 写入数据
            foreach ($data as $itm) {
                $fields = [];
                foreach (self::$_title as $key => $value) {
                    if (isset($itm[$key])) {
                        if (is_numeric($itm[$key]) && $itm[$key] > 999999999) {
                            $itm[$key] = $itm[$key] . "\t";
                        }
                        if (is_array($itm[$key])){
                            foreach ($itm[$key] as $value_){
                                $fields[] =  $value_;
                            }
                        }else{
                            $fields[] = $itm[$key];
                        }
                    } else {
                        $fields[] = '';
                    }
                }
                fputcsv(self::$_handle, $fields);
            }
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * 导出文件-结束
     * @author zhangbin
     * @return string|bool
     */
    static public function finish()
    {
        try {
            $file = self::$_file;
            if (self::$_handle) {
                fclose(self::$_handle);
            }
            self::$_title = null;
            self::$_handle = null;
            self::$_file = null;
            return $file;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * 导出文件-结束，并且上传文件服务器
     * @author zhangbin
     * @return array|bool [{"filename":"","extension":"","contentType":"","path":"","awsPath":"","size":0}]
     */
    static public function finishAndUpload()
    {
        try {
            $file = self::finish();
            $result = upload_file($file);
            if (!$result) {
                return false;
            }
            if (!isset($result['state']) || $result['state'] !== '000001') {
                return false;
            }
            $new_file_name = "";
            if (isset($result['data'][0]['awsPath'])) $new_file_name =  trim(strrchr($result['data'][0]['awsPath'], '/'),'/');
            $result['data'][0]['new_file_name'] = $new_file_name;
            return $result['data'];
        } catch (\Exception $ex) {
            trace('class=' . __CLASS__ . ',function=' . __FUNCTION__ . ',line=' . __LINE__, 'error');
            trace($ex->getMessage(), 'error');
            return false;
        }
    }

}
