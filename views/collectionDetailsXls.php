<?php
define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once ROOT.'/components/PHPExcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$xls = new PHPExcel();

// Set document properties
$xls->getProperties()->setCreator("")
    ->setLastModifiedBy("")
    ->setTitle(__FILE__)
    ->setSubject('Детализация инкассации')
    ->setDescription('Подробная информация по операциям в инкассации');

// Add some data
$xls->setActiveSheetIndex(0)
    ->setCellValue('a1', 'Детализация инкассации')
    ->setCellValue('a2', "Дата формирования отчета ".date('d.m.Y H:i'))
    ->setCellValue('a3', "Дата инкассации {$collectionParams[0]['dt_collection']}")
    ->setCellValue('a4', "Адрес инкассации {$collectionParams[0]['address']}");

$col = "a";
$row = 6;

if ($opers) {
    $xls->setActiveSheetIndex(0)
        ->setCellValue($col++.$row, '№ п/п')
        ->setCellValue($col++.$row, 'Дата')
        ->setCellValue($col++.$row, 'Услуга')
        ->setCellValue($col++.$row, 'Внесено')
        ->setCellValue($col++.$row, 'Цена по прайсу')
        ->setCellValue($col++.$row, 'Сдача')
        ->setCellValue($col++.$row++, 'НДС');

    $xls->getActiveSheet()->getStyle("A1:X1")->getFont()->setBold(true);
    $xls->getActiveSheet()->getStyle("A6:X6")->getFont()->setBold(true);
    
    $xls->getActiveSheet()->getColumnDimension("a")->setWidth(10);
    $xls->getActiveSheet()->getColumnDimension("b")->setAutoSize(true);
    $xls->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
    $xls->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
    $xls->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
    $xls->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
    $xls->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
    $xls->getActiveSheet()->getColumnDimension("h")->setAutoSize(true);

    $i = 1;
    foreach ($opers as $item) {
        $col = 'A';

        switch ($item['nds']) {
            case '4000':
                $nds = 'без НДС';
                break;
            case '1000':
                $nds = '18%';
                break;
            default:
                $nds = '';
                break;
        }

        if ($item['price'] <= $item['amount']) {
            $rest = $item['amount'] - $item['price'];
        } else {
            $rest = $item['amount'];
        }


        $xls->setActiveSheetIndex(0)
            ->setCellValue($col++.$row, $i++)
            ->setCellValue($col++.$row, $item['dt_oper'])
            ->setCellValue($col++.$row, $item['fullService']['name'])
            ->setCellValue($col++.$row, number_format($item['amount'], 2, '.', ' '))
            ->setCellValue($col++.$row, number_format($item['price'], 2, '.', ' '))
            ->setCellValue($col++.$row, number_format($rest, 2, '.', ' '))
            ->setCellValue($col++.$row, $nds);
        $row++;
    }

    $style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
    );

    $xls->getActiveSheet()->getStyle("A6:G6")->applyFromArray($style);
    $xls->getActiveSheet()->getStyle("A7:B$row")->applyFromArray($style);
    $xls->getActiveSheet()->getStyle("D7:G$row")->applyFromArray($style);
} else {
    $xls->setActiveSheetIndex(0)->setCellValue($col.$row, 'Нет данных');
}

// Rename worksheet
$xls->getActiveSheet()->setTitle('Отчет');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$xls->setActiveSheetIndex(0);

// Save Excel 2007 file
// PHPExcel_IOFactory::createReader('Excel5');
// PHPExcel_IOFactory::createReader('Excel2003XML');
$objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');

ob_start();
$objWriter->save('php://output');
$xlsData = ob_get_contents();
ob_end_clean();

$response['code'] = 0;
$response['file'] = "data:application/vnd.ms-excel;base64,".base64_encode($xlsData);

echo json_encode($response);

?>
