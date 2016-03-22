<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 16.03.2016
 * Time: 9:55

 */
require_once '../conf/login.php';
$conn=sqlsrv_connect($serverName,$connectionInfo);

//Получаем  параметры даты запроса.
$fromDate=$_GET['from'];
$toDate=$_GET['to'];
$type=$_GET['type'];
$range=$_GET['range'];
$level=$_GET['level'];

//print_r($_GET);
$params = array();
$params[] = $fromDate;
$params[] = $toDate;
echo "<pre>";
print_r($_GET);
print_r($params);

// Подключаемся к MSSQL
$mssqlConn=sqlsrv_connect($serverName,$connectionInfo);
if( $mssqlConn === false ) die( print_r( sqlsrv_errors(), true));

//формируем имя запрашиваемой таблицы
switch ($type) {
    case 'zakaz':
        $qFrom='Заказы_клиентов_';
        break;
    case 'otkaz':
        $qFrom='Отказы_клиентов_';
        break;
    case 'tender':
        $qFrom='Конкурсы_клиентов_';
        break;
}

//Определяем диапозон
switch($range) {
    case 'day':
        $qFrom.='День';
        break;
    case 'month':
        $qFrom.='Месяц';
        $qWhere=" where Дата between ( Cast(month((?)) as nvarchar)+'.'+ Cast(YEAR((?))as nvarchar) ) and ( Cast(month((?)) as nvarchar)+'.'+ Cast(YEAR((?))as nvarchar) )";

        //Надо 4 раза передать 2 параметра...
        $tmpparam=$params;
        $params[1]=$tmpparam[0];
        $params[2]=$tmpparam[1];
        $params[3]=$tmpparam[1];
        break;
    case 'quater':
        $qFrom.='Квартал';
        $qWhere=" where Дата between datepart( qq ,CONVERT(char(10),(?),104)) and datepart( qq ,CONVERT(char(10),(?),104))";
        break;
    case 'year':
        $qFrom.='Год';
        $qWhere=" where Дата between Cast(year((?)) as nvarchar)  and Cast(year((?)) as nvarchar)";
        break;
    default:
        throw new Exception('error rules range filter!');
};

//Определяем уровень данных
switch ($level)  {
    case 'all':
        $qSelect="Select ".$qFrom.".Сумма as SUM,".$qFrom.".Дата as Date";
        $qorder=" order by Дата";
        break;
    case 'region':
        $qFrom.='_Регион';
        $qJoin=' Left Join medline39.dbo.REGIONS on '.$qFrom.'.ИдРегиона=medline39.dbo.REGIONS.ID';
        $qSelect="Select ".$qFrom.".Сумма as SUM,".$qFrom.".Дата as Date, medline39.dbo.REGIONS.NAME ";
        $qorder=" order by NAME";
        break;
    case 'network':
        $qFrom.='_АптСеть';
        $qJoin=' Left Join medline39.dbo.FIRMS on '.$qFrom.'.ИдАптечнойСети=medline39.dbo.Firms.ID';
        $qSelect="Select ".$qFrom.".Сумма as SUM,".$qFrom.".Дата as Date, medline39.dbo.FIRMS.NAME ";
        $qorder=" order by NAME";
        break;
    case 'client':
        $qFrom.='_Аптеки';
        $qJoin=' Left Join medline39.dbo.FIRMS on '.$qFrom.'.ИдАптечнойСети=medline39.dbo.Firms.ID';
        $qSelect="Select ".$qFrom.".Сумма as SUM,".$qFrom.".Дата as Date, medline39.dbo.FIRMS.NAME ";
        $qorder=" order by NAME";
        break;
    default:
        throw new Exception('error rules level filter!)');
};

$query=$qSelect.' FROM '.$qFrom.$qJoin.$qWhere.$qorder;
echo $query ."<hr>";
$result=sqlsrv_query($conn,$query,$params) or die( print_r( sqlsrv_errors(), true));

$tmp=array();
while($row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
    echo $row['SUM'].$row['Date'].$row['NAME']."<hr>";
    $tmp[$row['NAME']][$row['Date']]=round($row['SUM'],3);

}

$data=json_encode($tmp);
echo $data;














/*
switch ($range) {
    case 'day':
        $query='
SELECT DISTINCT
  TOP (100) PERCENT CONVERT(varchar(12), CONVERT(varchar(12), medline39.dbo.SKL_DOCUMENTS.CREATEDATE, 104)) AS x,
  round(sum(medline39.dbo.SKL_DOCUMENTS.ALLSUMM),2) AS y
FROM         medline39.dbo.SKL_DOCUMENTS
WHERE     (medline39.dbo.SKL_DOCUMENTS.TYPE = 8) AND (medline39.dbo.SKL_DOCUMENTS.CREATEDATE) >(?) and (medline39.dbo.SKL_DOCUMENTS.CREATEDATE) <(?)
group by CONVERT(varchar(12), CONVERT(varchar(12), medline39.dbo.SKL_DOCUMENTS.CREATEDATE, 104))
order by x';
        break;
    case 'month':
        $query='
SELECT DISTINCT
  TOP (100) PERCENT MONTH( CONVERT(varchar(12), CONVERT(varchar(12), medline39.dbo.SKL_DOCUMENTS.CREATEDATE, 104)) ) AS x,
  round(sum(medline39.dbo.SKL_DOCUMENTS.ALLSUMM),2) AS y
FROM         medline39.dbo.SKL_DOCUMENTS
WHERE     (medline39.dbo.SKL_DOCUMENTS.TYPE = 8) AND (medline39.dbo.SKL_DOCUMENTS.CREATEDATE) >(?) and (medline39.dbo.SKL_DOCUMENTS.CREATEDATE) <(?)
group by MONTH( CONVERT(varchar(12), CONVERT(varchar(12), medline39.dbo.SKL_DOCUMENTS.CREATEDATE, 104)) )
order by x';
        break;
    case 'year':
        $query='
SELECT DISTINCT
  TOP (100) PERCENT YEAR( CONVERT(varchar(12), CONVERT(varchar(12), medline39.dbo.SKL_DOCUMENTS.CREATEDATE, 104)) ) AS x,
  round(sum(medline39.dbo.SKL_DOCUMENTS.ALLSUMM),2) AS y
FROM         medline39.dbo.SKL_DOCUMENTS
WHERE     (medline39.dbo.SKL_DOCUMENTS.TYPE = 8) AND (medline39.dbo.SKL_DOCUMENTS.CREATEDATE) >(?) and (medline39.dbo.SKL_DOCUMENTS.CREATEDATE) <(?)
group by YEAR( CONVERT(varchar(12), CONVERT(varchar(12), medline39.dbo.SKL_DOCUMENTS.CREATEDATE, 104)) )
order by x';
        break;
    default:
        throw new Exception('error rules filter!!! :)');
}



//x - укороченная дата y - сумма заказов

$result=sqlsrv_query($conn,$query,$params) or die( print_r( sqlsrv_errors(), true));
$tmp=array();
while($row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
    $tmp[$row['x']]=round($row['y'],3);

}
$data=json_encode($tmp);
echo $data;
*/
?>