<?php

namespace app\common\service;

use think\Db;

/**
 * 下载导出
 * @author xjy
 */
class DownService
{
    public static $split = 1000000;//按多少个数量分割文件
    public static $path = FILE_PATH . 'export' . DS;//保存文件目录
    public static $i = 1;//当前写入条数
    public static $ii = 1;//当前文件个数
    public static $fp;//当前写入文件句柄
    public static $csvPaths = [];//保存的文件名称路径
    public static $csvPath = '';//当前写的文件目录
    public static $exportId = 0;
    public static $day = '';

    //分页执行sql 获取数据
    public static function getData($db, $titles, $function, $param)
    {
        $sql = '';
        $limit = 10000;//每次从数据库获取的数量
        self::$exportId = $param['export_id'];
        //循环从数据库取10000条数据 减少内存使用
        for ($i = 1; $i <= 300; $i++) {
            if ($i == 1) {
                if (is_string($db)) {
                    $sql = $db;
                } else {
                    $sql = $db->page($i, $limit)->fetchSql()->select();
                }
                $sql1 = stristr($sql, "limit", true);
                if (!$sql1) {
                } else {
                    $sql = $sql1;
                }
            }
            $offset = ($i - 1) * $limit;
            $sqlTemp = $sql . " limit {$offset},{$limit} ";
            $res = Db::query($sqlTemp);
            if (empty($res)) {
                if (empty(self::$csvPaths)) {
                    //获取数据为空是处理
                    self::error([0 => ['id' => 1, 'msg' => '导出数据为空']], self::$exportId);
                } else {
                    //获取数据完成时处理
                    self::closeFp();
                    self::complete();
                }
                break;
            } else {
                if (!empty($res)) {
                    //数据格式化返回
                    $data = call_user_func($function, $res);
                    if (!empty($data)) {
                        //写入csv
                        self::csv($data, $titles);
                    }
                }
            }
        }
        return ["state" => "000001", "msg" => '操作成功'];
    }

    //写入csv文件保存 超过规定数量分割文件
    public static function csv($data, $titles)
    {
        if (empty(self::$day)) {
            self::$day = date("Y-m-dHis") . mt_rand(1, 999999);
        }
        if (empty(self::$csvPath)) {
            $csvName = self::$day . "_" . self::$ii . ".csv";
            self::$csvPath = self::$path . $csvName;
        }
        if (empty(self::$fp)) {
            self::$fp = fopen(self::$csvPath, "w");
        }
        foreach ($titles as &$v2){
            $v2= iconv("UTF-8", "GBK//IGNORE", $v2);
        }
        fputcsv(self::$fp, array_values($titles));
        self::$i++;
        foreach ($data as $val) {
            if (self::$i == 1) {
                $csvName = self::$day . "_" . self::$ii . ".csv";
                self::$csvPath = self::$path . $csvName;
                self::$fp = fopen(self::$csvPath, "w");
                fputcsv(self::$fp, array_values($titles));
            }
            $r = array();
            foreach ($titles as $k => $v) {
                if (is_numeric($val[$k])) {
                    if ($val[$k] > 99999999) {
                        $r[] = $val[$k] . "\t";
                    } else {
                        $r[] = $val[$k];
                    }
                } else {
                    $r[]= iconv("UTF-8", "GBK//IGNORE", $val[$k]);
                }
            }
            fputcsv(self::$fp, $r);
            self::$i++;
            //超过规定数量 写入新的csv
            if (self::$i == self::$split) {
                self::$ii++;
                self::closeFp();
            }
            self::$csvPaths[self::$csvPath] = self::$csvPath;
        }
    }

    //关闭文件句柄
    public static function closeFp()
    {
        if (!empty(self::$fp)) {
            self::$i = 1;
            fclose(self::$fp);
            self::$fp = '';
        }
    }

    //返回文件绝对路径 超过两个文件打包成zip
    public static function file()
    {
        $csvPaths = array_values(self::$csvPaths);
        if (count($csvPaths) > 1) {
            $url = self::zipOpen($csvPaths, self::$path);
            $file = self::$path . $url;
        } else {
            $file = $csvPaths[0];
        }
        return $file;
    }

    //错误信息写入
    public static function error($data, $exportId, $titles = [])
    {
        if (empty($exportId)) {
            trace('exportId不能为空', 'error');
            exit();
        }
        $title = [];
        foreach (array_keys($data[0]) as $v) {
            $title[$v] = $v;
        }
        self::$exportId = $exportId;
        self::csv($data, $title);
        self::closeFp();
        self::complete(2);
    }
    //导出
    public static function export($data,$title,$exportId){
        self::$exportId = $exportId;
        self::csv($data, $title);
        self::closeFp();
        self::complete();
    }
    //上传到aws
    public static function upload()
    {
        $file = self::file();
        $result = upload_file($file);
        if (!$result) {
            return ['code' => 500, 'msg' => '上传文件返回结果为空'];
        }
        if (!isset($result['state']) || $result['state'] !== '000001') {
            trace(json_encode($result),'error');
            return ['code' => 500, 'msg' => json_encode($result)];
        }
        $res = [];
        $res['source_file_url'] = str_replace("\\", '/', explode(ROOT_PATH, $file)[1]);
        $res['file_name'] = explode('other/', '');
        $res['file_url'] = $result['data'] ? $result['data'][0]['awsPath'] : '';
        $res['file_name'] = pathinfo($res['file_url'])['basename'];
        return ['code' => 1, 'data' => $res];
    }

    //处理结果
    public static function complete($result = 1)
    {
        $res = self::upload();
        if ($res['code'] == 1) {
            $update = $res['data'];
            $update['state'] = 3;
            $update['result'] = $result;
        } else {
            $update = [];
            $update['state'] = 3;
            $update['result'] = 2;
        }
        $update['complete_time'] = date('Y-m-d H:i:s');
        self::$ii = 1;
        self::$csvPaths = [];
        self::$fp = null;
        \app\api\model\ImportexportModel::dbUpdateImportexport(self::$exportId, $update);
        if ($res['code'] == 1) {
            exit_json();
        } else {
            exit_json($res['code'], $res['msg']);
        }
    }

    /**
     *导出csv文件 暂不用
     * @param $connect
     * @param $db
     * @param $titles '导出表头'
     * @param $function '处理逻辑函数'
     * @return string 文件地址
     */
    public static function doTest($connect, $db, $titles, $function)
    {
        $pdo = $connect->connect();
        $sql = $db->fetchSql(true)->select();
        $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $result = $pdo->query($sql);
        $split = self::$split;//按多少个数量切割
        $i = 1;
        $ii = 1;
        $csvPaths = array();
        $day = date("Y-m-dHis") . mt_rand(1, 999999);
        $csvName = "{$day}_{$ii}.csv";
        $path = File_PATH . 'csv' . DS;
        $csvPath = $path . $csvName;
        $fp = fopen($csvPath, "w");
        fputcsv($fp, array_values($titles));
        $i++;
        while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
            if ($i == 1) {
                $csvName = "{$day}_{$ii}.csv";
                $csvPath = $path . $csvName;
                $fp = fopen($csvPath, "w");
                fputcsv($fp, array_values($titles));
            }
            $val = $function($row);
            $r = array();
            foreach ($titles as $k => $v) {
                if (is_numeric($val[$k])) {
                    if ($val[$k] > 999999999) {
                        $r[] = $val[$k] . "\t";
                    } else {
                        $r[] = $val[$k];
                    }
                } else {
                    $r[] = $val[$k];
                }
            }
            fputcsv($fp, $r);
            $i++;
            if ($i == $split) {
                $ii++;
                $i = 1;
                fclose($fp);
                !empty($csvPath) && $csvPaths[] = $csvPath;
            }
        }
        if (!$row) {
            !empty($fp) && fclose($fp);
            !empty($csvPath) && $csvPaths[] = $csvPath;
        }
        $csvPaths = array_values(array_unique($csvPaths));
        if (count($csvPaths) > 1) {
            $url = self::zipOpen($csvPaths, $path);
            $file = $path . $url;
        } else {
            $file = $path . $csvName;
        }
        return $file;
    }

    //将文件打包zip
    public static function zipOpen($datalist, $outDir)
    {
        $md5sum = md5(serialize($datalist));
        $filename = $outDir . $md5sum . '.zip';
        $returnName = $md5sum . '.zip';
        if (file_exists($filename)) {
            return $returnName;
        }
        $zip = new \ZipArchive;
        if ($zip->open($filename, \ZIPARCHIVE::CREATE) !== TRUE) {
            return false;
        }
        $result = self::zipArrayFiles($datalist, $zip);
        if (false === $result) {
            $zip->unchangeAll();
            return false;
        }
        $zip->close();
        return $returnName;
    }

    //添加文件到zip
    public static function zipArrayFiles($datalist, $zip, $basePath = '')
    {
        foreach ($datalist as $dirname => $path) {
            if (!is_array($path)) {
                $zipPathName = $basePath ? $basePath . DS . basename($path) : basename($path);
                $result = $zip->addFile($path, $zipPathName);
                if (false === $result) {
                    return false;
                }
            } else {
                $zipDirName = $basePath ? $basePath . DS . $dirname : $dirname;
                $result = $zip->addEmptyDir($zipDirName);
                if (false === $result) {
                    return false;
                }
                $result = self::zipArrayFiles($path, $zip, $zipDirName);
                if (false === $result) {
                    return false;
                }
            }
        }
        return true;
    }
}
