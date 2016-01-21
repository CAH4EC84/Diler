<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 21.01.2016
 * Time: 16:18
 */
require_once '../conf/login.php';
$mssqlConn=sqlsrv_connect($serverName,$connectionInfo);
$currentTab = $_POST['menu-div']; //Активная вкладка
$checkboxs = $_POST['maincontainer-checkbox_details']; //выбранные для для уточнения

$inset = 'subs';



$mode = 'subs'; //запрос информации о подписках


