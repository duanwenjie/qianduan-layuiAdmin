<?php
/**
 * Excel服务类
 * User: duanwenjie
 * Date: 2019/3/7
 * Time: 10:30 AM
 */
namespace app\common\service;
use think\Exception;

class ExcelService
{

    /**
     * 导出Excel表格
     * @param $data
     * @param $excelFileName
     * @param $sheetTitle
     * @param null $firstLine
     * @author duanwenjie
     */
    static public function export($data, $excelFileName, $sheetTitle, $firstLine = null)
    {
        try{
            //导入扩展类
            vendor("Excel.PHPExcel", '.class.php');
            /* 实例化类 */
            $objPHPExcel = new \PHPExcel();
            $objPHPExcel->getProperties()
                ->setCreator("YKS")
                ->setLastModifiedBy("YKS")
                ->setTitle("Office 2003 XLSX Test Document")
                ->setSubject("Office 2003 XLSX Test Document")
                ->setDescription("Test document for Office 2003 XLSX, generated using PHP classes.")
                ->setKeywords("office 2003 openxml php")->setCategory("Test result file");
            $objActSheet = $objPHPExcel->setActiveSheetIndex(0);
            if ($firstLine){
                $r = 'A';
                foreach ($firstLine as $v){
                    $objActSheet->setCellValue($r . '1', $v);
                    $r++;
                }
            }
            $i = 2;
            foreach ($data as $value){
                $j = 'A';
                foreach ($value as $value2){
                    $objActSheet->setCellValue($j . $i, $value2);
                    $j++;
                }
                $i++;
            }
            $objPHPExcel->getActiveSheet()->setTitle($sheetTitle);
            $objPHPExcel->setActiveSheetIndex(0);
            ob_end_clean();
            header("Content-Type: application/vnd.ms-excel; charset=utf-8");
            header('Content-Disposition: attachment;filename=' . $excelFileName . '.xls');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }catch (Exception $e){
            trace($e->getMessage().',line=' . __LINE__, 'error');
            exit_json('000000',$e->getMessage());
        }
    }


    /**
     * 在本地生成文件
     * @param $data
     * @param $excelFileName
     * @param $sheetTitle
     * @param null $firstLine
     * @author duanwenjie
     */
    static public function createFileToLocale($data, $excelFileName, $sheetTitle, $firstLine = null)
    {
        try{
            //导入扩展类
            vendor("Excel.PHPExcel", '.class.php');
            /* 实例化类 */
            $objPHPExcel = new \PHPExcel();
            $objPHPExcel->getProperties()
                ->setCreator("YKS")
                ->setLastModifiedBy("YKS")
                ->setTitle("Office 2003 XLSX Test Document")
                ->setSubject("Office 2003 XLSX Test Document")
                ->setDescription("Test document for Office 2003 XLSX, generated using PHP classes.")
                ->setKeywords("office 2003 openxml php")
                ->setCategory("Test result file");
            $objActSheet = $objPHPExcel->setActiveSheetIndex(0);
            if ($firstLine){
                $r = 'A';
                foreach ($firstLine as $v){
                    $objActSheet->setCellValue($r . '1', $v);
                    $r++;
                }
            }
            $i = 2;
            foreach ($data as $value){
                $j = 'A';
                foreach ($value as $value2){
                    $objActSheet->setCellValue($j . $i, $value2);
                    $j++;
                }
                $i++;
            }
            $objPHPExcel->getActiveSheet()->setTitle($sheetTitle);
            $objPHPExcel->setActiveSheetIndex(0);
            ob_end_clean();
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save($excelFileName);
        }catch (Exception $e){
            trace($e->getMessage().',line=' . __LINE__, 'error');
            exit_json('000000',$e->getMessage());
        }
    }


    /**
     * 导入文件并解析文件数据
     * @param $filePath string 文件地址
     * @$num_check array 需要进行科学计数法转换的下标 [0,2,3]
     * @return array 解析后的数据
     * @author duanwenjie
     */
    static public function import($filePath, $num_check = array())
    {
       try{
           vendor("Excel.PHPExcel", '.class.php');
           $line = 0;
           $excelData = array();
           // 文件原始的格式 text/plain：csv文件 application/octet-stream：xlsx文件 application/vnd.ms-office：xls文件
           $original_type = mime_content_type($filePath);
           // 单独文件后缀判断方式（缺陷：无法识别人为暴力改文件格式后缀，会导致文件识别失败）
           $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
           if ($extension == 'xlsx' && $original_type == 'application/octet-stream'){
               $objReader = new \PHPExcel_Reader_Excel2007();
               $objPHPExcel = $objReader->load($filePath);
           }elseif ($extension == 'xls' && $original_type == 'application/vnd.ms-office'){
               $objReader = new \PHPExcel_Reader_Excel5();
               $objPHPExcel = $objReader->load($filePath);
           }elseif ($extension == 'csv' && $original_type == 'text/plain'){
               $handle = fopen($filePath, 'rb');
               while (feof($handle) === false){
                   ++$line;
                   if (($itm = fgetcsv($handle)) && $line >= 2){ // 去除表头
                       foreach ($itm as $key => &$val){
                           $val = trim($val);
                           if (in_array($key,$num_check))  $val = self::NumToStr($val); // 转换科学计数法数据
                           $encode = mb_detect_encoding($val, "UTF-8,GB2312,GBK,ASCII,EUC-CN");
                           $val = mb_convert_encoding($val, "UTF-8", $encode); // 将对应的字符串编码格式全部装换为中文，防止中文乱码
                       }
                       $excelData[] = $itm;
                   }
               }
               fclose($handle);
               return $excelData;
           }else{
               $excelData['error'] = array('msg' => "上传文件格式被修改，文件内容无法识别，请下载模板重新上传");
               return $excelData;
           }
           $objWorksheet = $objPHPExcel->getActiveSheet();
           $highestRow = $objWorksheet->getHighestRow();
           $highestColumn = $objWorksheet->getHighestColumn();
           $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
           for ($row = 2; $row <= $highestRow; $row++){ // 去除表头
               for ($col = 0; $col < $highestColumnIndex; $col++){
                   $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
               }
           }

           return $excelData;
       }catch (Exception $e){
           trace($e->getMessage().',line=' . __LINE__, 'error');
           exit_json('000000',$e->getMessage());
       }
    }

    /**
     * 将传入的科学计数法类型数据转换为正常的数字字符串数据
     * @param $num
     * @return float|string
     * @author duanwenjie
     */
    static public function NumToStr($num)
    {
        if (stripos($num, 'e') === false) return $num;
        $num = trim(preg_replace('/[=\'"]/', '', $num, 1), '"'); //出现科学计数法，还原成字符串
        $result = "";
        while ($num > 0){
            $v = $num - floor($num / 10) * 10;
            $num = floor($num / 10);
            $result = $v . $result;
        }

        return $result;
    }

}