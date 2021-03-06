<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 24.02.2016
 * Time: 14:38
 */
require_once '../conf/login.php';
$conn=sqlsrv_connect($serverName,$connectionInfo);  // ������������ � MSSQL
if( $conn === false ) die( print_r( sqlsrv_errors(), true));

//������ ���������
$subsId=$_GET['id']; //�� ������������� �������� ���������� �� subGridModel->params[id]
$page = $_GET['page'];// �������� ����� ��������. ������� jqGrid ������ ��� � 1.
$limit = $_GET['rows']; // ������� ����� �� ����� ����� � ������� - rowNum ��������
$sidx = $_GET['sidx']; // ������� ��� ����������. ������� sortname �������� ����� index �� colModel
$sord = $_GET['sord']; // ������� ����������.
if(!$sidx) $sidx =1; // ���� ������� ���������� �� �������, �� �����  ����������� �� ������ �������.

//���������� ���������� ������� � �������
$query="select Count(*) as count from subsInfoDetails WHERE subs_id=".$subsId;

$result=sqlsrv_query($conn,$query,$params) or die( print_r( sqlsrv_errors(), true));
$row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC);
$count = $row['count'];

//���� �������� rowNum ���������� � all ($limit), ���������� ������� �������.
if ($limit=='all') {
    $limit=$count;
}
// ��������� ����� ���������� �������.
if( $count > 0 && $limit > 0) {
    $total_pages = ceil($count/$limit);
} else {
    $total_pages = 0;
}
// ���� ������������� ����� �������� ������ ������ ���������� �������,
// �� ������������� ����� �������� � ������������.
if ($page > $total_pages) $page=$total_pages;

// ��������� ��������� �������� �����.
$start = $limit*$page - $limit;

// ���� ��������� �������� ������������,
// �� ������������� ��� � 0.
// ��������, ����� ������������
// ������ 0 � �������� ������������� ��������.
if($start <0) $start = 0;



$query = "SELECT *
        FROM (
           SELECT node_firm,name,doc_type,base_file,base_timeOf,error_text,actual_days, ROW_NUMBER() OVER (ORDER BY $sidx $sord) AS x
           FROM subsInfodetails WHERE subs_id=".$subsId."
        ) AS y
        WHERE y.x BETWEEN ".$start." AND ".($start+$limit)." ORDER BY y.x, $sidx $sord;";

//$query = "SELECT node_firm,name,doc_type,base_file,base_timeOf,error_text,actual_days FROM subsInfoDetails WHERE subs_id=".$subsId." order by name";

// ������ ��� ��������� ������.
$result=sqlsrv_query($conn,$query,$params) or die( print_r( sqlsrv_errors(), true));

// ��������� � ��������� �����������.
header("Content-type: text/xml;charset=utf-8");
$s = "<?xml version='1.0' encoding='utf-8'?>";
$s .=  "<rows>";
$s .= "<page>".$page."</page>";
$s .= "<total>".$total_pages."</total>";
$s .= "<records>".$count."</records>";

// ����������� ��������� ��������� ������ � CDATA
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