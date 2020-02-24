<?php


namespace app\common\controller;
use PHPExcel_Cell_DataType;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_NumberFormat;

class ExcelWorksheets
{
    private $PHPExcel;
    private $PHPSheet;
    private $PHPWriter;
    private $title;
    private $rows;
    private $menus;

    private $sysmenu = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
    /**
     * 架构方法 设置参数
     */
    public function __construct()
    {
        // 创建Excel设置第一个工作表为操作表
        $this->PHPExcel  = new PHPExcel();
        $this->PHPExcel->setActiveSheetIndex(0);
        $this->PHPSheet  = $this->PHPExcel->getActiveSheet();
        $this->PHPWriter = PHPExcel_IOFactory::createWriter($this->PHPExcel,'Excel2007');
    }
    // 开始下载表格
    public function downExcel($title,$rows,$menus)
    {
        // 工作表信息
        $this->title = $title;
        $this->rows  = $rows;
        $this->menus = $menus;
        // 判断有多少个工作表
        for ($i=0;$i<count($title);$i++){

            if($i>0){
                $msgWorkSheet = new \PHPExcel_Worksheet($this->PHPExcel, $this->title[$i]); //创建一个工作表
                $this->PHPExcel->addSheet($msgWorkSheet); //插入工作表
                $this->PHPExcel->setActiveSheetIndex($i); //切换到新创建的工作表
                $this->PHPSheet  = $this->PHPExcel->getActiveSheet();
            }

            $this->PHPSheet->setTitle($this->title[$i]);

            $this->setMenu($i);

            $this->setContents($i);

        }
        $this->PHPExcel->setActiveSheetIndex(0); //切换到新创建的工作表

        $filename = implode("-", $title).".xlsx";
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$filename.'"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $this->PHPWriter->save('php://output');
    }
    /**
     * 设置标题栏
     */
    private function setMenu($index)
    {
        $max = count($this->menus[$index]);
        for ($im=0; $im < $max; $im++) {
            $this->PHPSheet->setCellValue($this->sysmenu[$im]."1",$this->menus[$index][$im]);
            $this->PHPSheet->getColumnDimension($this->sysmenu[$im])->setAutoSize(true);
        }
        $this->PHPSheet->getStyle('a1:'.$this->sysmenu[$max-1]."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $this->PHPSheet->getStyle('a1:'.$this->sysmenu[$max-1]."1")->getFill()->getStartColor()->setARGB('FF0070C0');
        $this->PHPSheet->getStyle('a1:'.$this->sysmenu[$max-1]."1")->getFont()->getColor()->setARGB('FFFFFFFF');
    }
    /**
     * 设置内容显示
     */
    private function setContents($index)
    {
        $r = 2;
        $insertdata = $this->rows[$index];
//        print_r($insertdata);
        foreach ($insertdata as $k => $v) {
            $this->setRowsContent($r,$v);
            $r++;

        }
    }
    /**
     * 设置每行数据
     */
    private function setRowsContent($rownum,$data)
    {
//        print_r($data);
        $styleThinBlackBorderOutline = array(
            'borders' => array (
                'outline' => array (
                    'style' => PHPExcel_Style_Border::BORDER_THIN,   //设置border样式
                    //'style' => PHPExcel_Style_Border::BORDER_THICK,  另一种样式
                    'color' => array ('argb' => 'FF000000'),          //设置border颜色
                ),
            ),
        );
//        $keyarr = array_keys($data);

        for ($ri=0; $ri < count($data); $ri++) {
            $keyarr = array_keys($data);
            $keyvalue = $keyarr[$ri];
            $this->PHPSheet->setCellValue($this->sysmenu[$ri].$rownum,$data[$keyvalue]);
            $this->PHPSheet->getStyle($this->sysmenu[$ri].$rownum)->applyFromArray($styleThinBlackBorderOutline);
            $this->PHPSheet->getColumnDimension($this->sysmenu[$ri])->setAutoSize(true);
            $this->PHPSheet->getStyle($ri)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

        }

    }
}