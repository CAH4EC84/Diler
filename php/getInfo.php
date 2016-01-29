<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 21.01.2016
 * Time: 16:18
 * При открытии сайта JS вызывает данный модуль, последовательно передавая ему имена вкладок.
 * Модуль заполняет данными все необходимые вкладки.
 * Далее если пользователь желает уточнить данные то в данный скрипт передаются запрашиваемые данные.
 */
require_once '../conf/login.php';
$mssqlConn=sqlsrv_connect($serverName,$connectionInfo);
if( $mssqlConn === false ) die( print_r( sqlsrv_errors(), true));
//Получаем полную информацию для каждой вкладки.
$tab = $_POST['tab'];
print_r($_POST);
//$tab = 'firmsInfo';

//Функиция получения данных и отрисовки таблицы
GetInfo($mssqlConn,$tab);

//Получания всех данных из таблицы, и списка её полей
function GetInfo($conn,$table) {
    //Заголовок таблицы
    $queryFields="Select COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS  where TABLE_NAME='".$table."'";
    echo $queryFields."<hr>";
    $resultFields=sqlsrv_query($conn,$queryFields) or die( print_r( sqlsrv_errors(), true));
    $tableHead= '<table border="1"> <thead><tr>';
    $columnCount=0;
    while ($row=sqlsrv_fetch_array($resultFields,SQLSRV_FETCH_NUMERIC)) {
        $tableHead.='<td>'.$row[0].'</td>';
        $columnCount++;
    };
    $tableHead.='</tr></thead>';

    //Данные в таблице
    $queryData="select * from ". $table." order by 1";
    echo $queryData."<hr>";
    $resultData=sqlsrv_query($conn,$queryData) or die( print_r( sqlsrv_errors(), true));
    $tableBody='<tbody>';
        while ($row2=sqlsrv_fetch_array($resultData,SQLSRV_FETCH_NUMERIC)) {
        $tableBody.='<tr>';
            for ($j=0; $j<$columnCount; $j++) {
            $tableBody.='<td>'.$row2[$j].'</td>';
        }
        $tableBody.='</tr>';
    };
    $tableBody.='</tbody></table>';


//Отрисовка таблицы
    $tableHead.=$tableBody;
    echo $tableHead;

}





