<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 17.02.2016
 * Time: 13:34
 */
// �������� ����������, ����������� ��� ����������� � ���� ������
// MSSQL. �� ������ ����� �����, ������, ��� ����.
require_once '../conf/login.php';
$conn=sqlsrv_connect($serverName,$connectionInfo);

// �������� ����� ��������. ������� jqGrid ������ ��� � 1.
$page = $_GET['page'];

// ������� ����� �� ����� ����� � ������� - rowNum ��������
$limit = $_GET['rows'];

// ������� ��� ����������. ������� sortname ��������
// ����� index �� colModel
$sidx = $_GET['sidx'];

// ������� ����������.
$sord = $_GET['sord'];

// ���� ������� ���������� �� �������, �� �����
// ����������� �� ������ �������.
if(!$sidx) $sidx =1;

// ������������ � MSSQL
$mssqlConn=sqlsrv_connect($serverName,$connectionInfo);
if( $mssqlConn === false ) die( print_r( sqlsrv_errors(), true));

// ��������� ���������� �����. ��� ���������� ��� ������������ ���������.
$query="select Count(*) as count from firmsInfo";
$result=sqlsrv_query($conn,$query) or die( print_r( sqlsrv_errors(), true));
$row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC);
$count = $row['count'];

//���� �������� rowNum ���������� � -1 ($limit), ���������� ������� �������.
if ($limit=-1) {
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
           SELECT *, ROW_NUMBER() OVER (ORDER BY $sidx $sord) AS x
           FROM firmsInfo
        ) AS y
        WHERE y.x BETWEEN ".$start." AND ".($start+$limit)." ORDER BY y.x, $sidx $sord;";
// ������ ��� ��������� ������.

$result=sqlsrv_query($conn,$query) or die( print_r( sqlsrv_errors(), true));

// ��������� � ��������� �����������.
header("Content-type: text/xml;charset=utf-8");

$s = "<?xml version='1.0' encoding='utf-8'?>";
$s .=  "<rows>";
$s .= "<page>".$page."</page>";
$s .= "<total>".$total_pages."</total>";
$s .= "<records>".$count."</records>";

// ����������� ��������� ��������� ������ � CDATA
while($row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
    $s .= "<row id='". $row['id']."'>";
    $s .= "<cell>". $row['id']."</cell>";
    $s .= "<cell>". $row['parent_id']."</cell>";
    $s .= "<cell>". $row['nodes_id']."</cell>";
    $s .= "<cell><![CDATA[". $row['name']."]]></cell>";
    $s .= "<cell><![CDATA[". $row['address1']."]]></cell>";
    $s .= "<cell><![CDATA[". $row['region']."]]></cell>";
    $s .= "<cell>". $row['subs_id']."</cell>";
    $s .= "</row>";
}
$s .= "</rows>";

echo $s;
?>