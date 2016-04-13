<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 13.04.2016
 * Time: 9:52
 */

require_once '../conf/login.php';
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//Добавляем памяти для выгрузки больших файлов.
ini_set('memory_limit','-1');

//Максимальное вермя выполнения скрипта
set_time_limit(1000);
echo 'Script START-',date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB <hr>";
$conn=sqlsrv_connect($serverName,$connectionInfo);
$query="Select DISTINCT Дата AS [Date],F.name as Client,F1.NAME AS Diler,Сумма as Summ,
                        R.NAME as Network,НомерЗаказа as Num,DT.NAME as DocType,DS.NAME as DocStatus
from FullData
Left Join medline39.dbo.FIRMS as F on F.ID=ИдАптеки
Left Join medline39.dbo.FIRMS as F1 on F1.ID=ИдПоставщика
Left Join medline39.dbo.FIRMS as R on R.ID=ИдАптечнойСети
Left Join medline39.dbo.SKL_DOC_TYPES as DT on DT.ID=ИдТипаДокумента
Left Join medline39.dbo.SKL_DOC_STATUS as DS on DS.ID=ИдСтатусаДокумента
where Дата between '01.01.2016' and '31.01.2016'
order by Date";


$result=sqlsrv_query($conn,$query) or die( print_r( sqlsrv_errors(), true));
$i = 0;
$data=array();
echo 'GET QUERY RESULT-',date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB <hr>";
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

echo 'FORMATED AS ARRAY-',date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB <hr>";
echo count($data),"<br>";

$xls = new COM("Excel.Application", NULL, CP_UTF8) or Die ("Did not instantiate Excel"); // Создаем новый COM-объект

$xls->Application->Visible = 1;      // Заставляем его отобразиться
$xls->Workbooks->Add();              // Добавляем новый документ

$xlsRow=1;

for ($j=0;$j<count($data);$j++) {
    $range = $xls->Cells($xlsRow,1);
    $range->Value = $data[$j]['Date'];
    $range= $xls->Cells($xlsRow,2);
    $range->Value = $data[$j]['Client'];
    $range= $xls->Cells($xlsRow,3);
    $range->Value = $data[$j]['Diler'];
    $range= $xls->Cells($xlsRow,4);
    $range->Value = $data[$j]['Summ'];
    $range= $xls->Cells($xlsRow,5);
    $range->Value = $data[$j]['Network'];
    $range= $xls->Cells($xlsRow,6);
    $range->Value = $data[$j]['Num'];
    $range= $xls->Cells($xlsRow,7);
    $range->Value = $data[$j]['DocType'];
    $range= $xls->Cells($xlsRow,8);
    $range->Value = $data[$j]['DocStatus'];
    $xlsRow++;
}


//$range = $xls->Cells($xlsRow,1);
//$range->Value="test";
echo 'INSERT COMPLITE-',date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB <hr>";


$xls->Workbooks[1]->SaveAs("testCOM.xlsx");
$xls->Quit();                        //Закрываем приложение
$xls = Null;
$range = Null;

?>