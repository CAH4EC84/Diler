<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 19.01.2016
 * Time: 11:29
 * Модуль поиска данных о подписке на основании её номера или частичного названия.
 * Данный модуль не актуален 21.01.2016
 */
require_once '../conf/login.php';
$mssqlConn=sqlsrv_connect($serverName,$connectionInfo);


$searchId=536;
$searchName='Афган';
// Функиця возвращает данные и заголовки таблицы.
//list($tableData,$tableHeader)=searchSubs($mssqlConn,$searchId,$searchName);
searchSubs($mssqlConn,$searchId,$searchName);
//Функция формирует выводимую таблицу.
//echo makeTable($tableData,$tableHeader);



function searchSubs($mssqlConn,$id,$name)
{
	$query = strtolower("SELECT L.id as Номер,L.name as Название,MAX(V.timeof) AS Дата,L.is_active as Вкл FROM meduni.dbo.subsList AS L LEFT JOIN meduni.dbo.subsVer AS V ON L.id=V.subs_id
	WHERE L.id=" . $id . " OR L.name LIKE '%" . $name . "%'
	GROUP BY L.id,L.name,L.LastUpdated,L.actual_days,L.is_active
	ORDER BY L.id");
	echo $query."<hr>";
	echo strtolower(substr($query,0,stripos($query,'from')))."<hr>";
	$tmp=explode(',',substr($query,0,stripos($query,'from')));
	print_r($tmp);
	echo "<hr>";
	for ($i=0; $i<count($tmp); $i++) {
		$rowHeaders[]=substr($tmp[$i],stripos($tmp[$i],'as ')+3);
	}
	print_r($rowHeaders);
	$result = sqlsrv_query($mssqlConn, $query);
	//создаем заголовок таблицы
	echo <<<_TABEL
    <table border=1 id="subsInfo">
    <thead>
        <tr>
            <th>Номер</th>
            <th>Название</th>
            <th>Дата</th>
            <th>Вкл</th>
        </tr>
     </thead>
_TABEL;

	while ($row=sqlsrv_fetch_array($result,SQLSRV_FETCH_BOTH)) {
		//заполняем нашу таблицу данными
		$publishDate = date("d-m-Y H:i:00", date_format($row[2], 'U'));
		echo "<tr>";
		echo "<td>" . $row[0] . "</td>";
		echo "<td>" . $row[1] . "</td>";
		echo "<td>" . $publishDate . "</td>";
		echo "<td>" . $row[3] . "</td>";
		echo "</tr>";
	};

	//$headers='';
	//return array ($result,$headers);
}
/*
//создаем заголовок таблицы
	echo <<<_TABEL
    <table border=1 id="subsInfo">
    <thead>
        <tr>
            <th>Включена</th>
            <th>Подписка</th>
            <th>Название</th>
            <th>Дата_Обновления</th>
            <th>Актуальность</th>
        </tr>
     </thead>
_TABEL;

	while ($row=sqlsrv_fetch_array($result,SQLSRV_FETCH_BOTH)) {
		//заполняем нашу таблицу данными
		$publishDate = date("d-m-Y H:i:00", date_format($row["pd"], 'U'));
		echo "<tr>";
		echo "<td>" . $row['is_active'] . "</td>";
		echo "<td>" . $row['id'] . "</td>";
		echo "<td>" . $row['name'] . "</td>";
		echo "<td>" . $publishDate . "</td>";
		echo "<td>" . $row['actual_days'] . "</td>";
		echo "</tr>";
	};
*/
/*
function makeTable($td,$th)
{
	$table='';
	return $table;
}
*/