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
$range=$_GET['range'];
$params = array();
$params[] = $fromDate;
$params[] = $toDate;
$param[] = $range;
// Подключаемся к MSSQL
$mssqlConn=sqlsrv_connect($serverName,$connectionInfo);
if( $mssqlConn === false ) die( print_r( sqlsrv_errors(), true));

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

?>