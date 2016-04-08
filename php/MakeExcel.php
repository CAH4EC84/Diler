<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 06.04.2016
 * Time: 13:29
 */

require_once('Classes/PHPExcel.php');
require_once '../conf/login.php';
$conn=sqlsrv_connect($serverName,$connectionInfo);
$query = "Select Препарат +'; '+ Производитель as Production, Sum(Количество) as Quantity
          from FullData where ИдАптечнойСети=20029739 and Дата between '2016-02-01' and '2016-03-31' and ИдТипаДокумента=8
          group by Препарат +'; '+ Производитель
          order by Quantity DESC";

$result=sqlsrv_query($conn,$query);
$data=array();
while ($row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
    $data[$row['Production']]=$row['Quantity'];
}
/*
echo "<pre>";
print_r($data);
echo "</pre>";
*/

//Создаем объект
$objPHPExcel = new PHPExcel();

//Устанавливаем индекс активного листа отсчет листов начинается с 0
$objPHPExcel->setActiveSheetIndex(0);

//Получаем активный лист
$objWorksheet = $objPHPExcel->getActiveSheet();

//Подписываем лист
$objWorksheet->setTitle('Клевер');

//Заполняем ячейки данными (Ячейки адресуются либо A1 либо через индексы где A1= (0,1)
$objWorksheet->setCellValue('A1','Препарат');
$objWorksheet->setCellValue('B1','Количество');

$xlsRow=2;
foreach ($data as $key=>$value) {
    $objWorksheet->setCellValueByColumnAndRow(0,$xlsRow,$key);
    $objWorksheet->setCellValueByColumnAndRow(1,$xlsRow,$value);
    $xlsRow++;
}

// Выводим содержимое файла

//Заголовки для вывода в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="klever.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); // Формат вывода
$objWriter->save('php://output');

//Сохранить как локальный файл
//$objWriter->save('../output/klever.xlsx');




?>
