<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 21.01.2016
 * Time: 16:18
 */
require_once '../conf/login.php';
$mssqlConn=sqlsrv_connect($serverName,$connectionInfo);
$currentTab = $_POST['menu-div']; //�������� �������
$checkboxs = $_POST['maincontainer-checkbox_details']; //��������� ��� ��� ���������

$inset = 'subs';



$mode = 'subs'; //������ ���������� � ���������


