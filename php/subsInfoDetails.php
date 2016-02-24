<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 24.02.2016
 * Time: 14:38
 */
require_once '../conf/login.php';
$conn=sqlsrv_connect($serverName,$connectionInfo);  // Подключаемся к MSSQL
if( $conn === false ) die( print_r( sqlsrv_errors(), true));


$subsId=$_GET['id']; //Ид запрашиваемой подписки передается из subGridModel->params[id]

$query = "SELECT node_firm,name,doc_type,base_file,base_timeOf,error_text,actual_days
        FROM subsInfoDetails WHERE subs_id=".$subsId." order by name";

// Запрос для получения данных.
$result=sqlsrv_query($conn,$query,$params) or die( print_r( sqlsrv_errors(), true));

// Заголовок с указанием содержимого.
header("Content-type: text/xml;charset=utf-8");
$s = "<?xml version='1.0' encoding='utf-8'?>";
$s .=  "<rows>";
// Обязательно передайте текстовые данные в CDATA
while($row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
    $s .=  "<row>";
    $s .= "<cell>". $row['node_firm']."</cell>";
    $s .= "<cell><![CDATA[". $row['name']."]]></cell>";
    $s .= "<cell>". $row['doc_type']."</cell>";
    $s .= "<cell><![CDATA[". $row['base_file']."]]></cell>";
    $s .= "<cell>". $row['base_timeOf']."</cell>";
    $s .= "<cell><![CDATA[". $row['error_text']."]]></cell>";
    $s .= "<cell>". $row['actual_days']."</cell>";
    $s .= "</row>";
}
$s .= "</rows>";
echo $s;