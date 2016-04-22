<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 13.04.2016
 * Time: 11:44
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
where Дата between '01.01.2016' and '31.03.2016'
order by Date";


$result=sqlsrv_query($conn,$query) or die( print_r( sqlsrv_errors(), true));


$note=<<<XML
<report>
<body>
</body>
</report>
XML;
$xml= new SimpleXMLElement($note);
while ($row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
    $record =$xml->body->addChild("record");
    $record->addChild('Date',$row['Date']);
    $record->addChild('Client',$row['Client']);
    $record->addChild('Diler',$row['Diler']);
    $record->addChild('Summ',$row['Summ']);
    $record->addChild('Network',$row['Network']);
    $record->addChild('Num',$row['Num']);
    $record->addChild('DocType',$row['DocType']);
    $record->addChild('DocStatus',$row['DocStatus']);
};
echo 'INSERT COMPLITE-',date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB <hr>";

$xml->asXML('../output/TestXML.xml');

?>