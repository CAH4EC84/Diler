<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 12.04.2016
 * Time: 9:55
 */
require_once('Classes/PHPExcel.php');
require_once '../conf/login.php';

$fromDate = $_GET['from'];
$toDate = $_GET['to'];
$params = array();
$params[] = $fromDate;
$params[] = $toDate;

$conn=sqlsrv_connect($serverName,$connectionInfo);

$query="Select DISTINCT Дата AS [Date],F.name as Client,F1.NAME AS Diler,Сумма as Summ,
                        R.NAME as Network,НомерЗаказа as Num,DT.NAME as DocType,DS.NAME as DocStatus
from FullData
Left Join medline39.dbo.FIRMS as F on F.ID=ИдАптеки
Left Join medline39.dbo.FIRMS as F1 on F1.ID=ИдПоставщика
Left Join medline39.dbo.FIRMS as R on R.ID=ИдАптечнойСети
Left Join medline39.dbo.SKL_DOC_TYPES as DT on DT.ID=ИдТипаДокумента
Left Join medline39.dbo.SKL_DOC_STATUS as DS on DS.ID=ИдСтатусаДокумента
where Дата between (?) and (?)
order by Date";

$result=sqlsrv_query($conn,$query,$params);

$i = 0;
$data=array();
while ($row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
    $data[$i]['Date']=$row['Date'];
    $data[$i]['Client']=$row['Client'];
    $data[$i]['Diler']=$row['Diler'];
    $data[$i]['Summ']=$row['Summ'];
    $data[$i]['Network']=$row['Network'];
    $data[$i]['Num']=$row['Num'];
    $data[$i]['DocType']=$row['DocType'];
    $data[$i]['DocStatus']=$row['DocStatus'];
    $i++;
    };



//Запись в ексель файл будет выполнятся поциями по 5000 строк.
$limit=5000;
$count=count($data);

// Вычисляем общее количество итераций
if( $count > 0) {
    $total_iterations = ceil($count/$limit);
}
echo 'Total records-'.$count.' itterations - '.$total_iterations;
$inputFileName = '../output/zakaz.xlsx';
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//Создаем объект
$objPHPExcel = new PHPExcel();
//Добавляем памяти для выгрузки больших файлов.
ini_set('memory_limit','-1');
//$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
//$cacheSettings = array( 'memoryCacheSize ' => '256MB');
$xlsRow=2;
for ($i=0; $i<$total_iterations; $i++)
{
    set_time_limit(300); //Время на один кргу цикла
    echo '<hr>Iteration-'.$i;
    // Echo memory usage
    echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB";
    //Чтение
    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    $objPHPExcel = $objReader->load($inputFileName);
//Получаем активный лист
    $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
  for ($j=$i*$limit;$j<($limit-1)+($limit*$i);$j++) {
      $objWorksheet->setCellValueByColumnAndRow(0,$xlsRow,$data[$j]['Date']);
      $objWorksheet->setCellValueByColumnAndRow(1,$xlsRow,$data[$j]['Client']);
      $objWorksheet->setCellValueByColumnAndRow(2,$xlsRow,$data[$j]['Diler']);
      $objWorksheet->setCellValueByColumnAndRow(3,$xlsRow,$data[$j]['Summ']);
      $objWorksheet->setCellValueByColumnAndRow(4,$xlsRow,$data[$j]['Network']);
      $objWorksheet->setCellValueByColumnAndRow(5,$xlsRow,$data[$j]['Num']);
      $objWorksheet->setCellValueByColumnAndRow(6,$xlsRow,$data[$j]['DocType']);
      $objWorksheet->setCellValueByColumnAndRow(7,$xlsRow,$data[$j]['DocStatus']);
      $xlsRow++;
  }
    // Выводим содержимое файла
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); // Формат вывода
    //Сохранить как локальный файл
    $objWriter->save('../output/zakaz.xlsx');
    $objPHPExcel->disconnectWorksheets();
    unset($objPHPExcel);
}

return 'File Ready';
?>