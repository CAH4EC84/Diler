<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 17.03.2016
 * Time: 14:46
 */
require_once '../conf/login.php';
$conn=sqlsrv_connect($serverName,$connectionInfo);

if ($_GET['typename']=='client') {
    $query = "select NAME as name from firmsInfoDetails where TYPENAME='Продавец' order by NAME";
    $result = sqlsrv_query($conn, $query, $params) or die(print_r(sqlsrv_errors(), true));

    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_NUMERIC)) {
        $data[] = $row[0];
    };
}


$query="select NAME as name from firmsInfoDetails where TYPENAME='Покупатель' order by NAME";
$result=sqlsrv_query($conn,$query,$params) or die( print_r( sqlsrv_errors(), true));

while($row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
    $data[]=$row['name'];
};
echo $data;
?>





