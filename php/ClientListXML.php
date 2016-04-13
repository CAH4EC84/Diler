<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 11.04.2016
 * Time: 14:06
 */
require_once '../conf/login.php';
$conn=sqlsrv_connect($serverName,$connectionInfo);
// Получаем номер страницы. Сначала jqGrid ставит его в 1.
$page = $_GET['page'];

// сколько строк мы хотим иметь в таблице - rowNum параметр
$limit = $_GET['rows'];

// Колонка для сортировки. Сначала sortname параметр
// затем index из colModel
$sidx = $_GET['sidx'];

// Порядок сортировки.
$sord = $_GET['sord'];

// Если колонка сортировки не указана, то будем
// сортировать по первой колонке.
if(!$sidx) $sidx =1;


$level=$_GET['level'];
switch ($level)  {
    case 'Регион':
        // Вычисляем количество строк. Это необходимо для постраничной навигации.
        $query="select Count(*) as count from medline39.dbo.REGIONS where PARENT_ID=4";
        $result=sqlsrv_query($conn,$query) or die( print_r( sqlsrv_errors(), true));
        $row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC);
        $count = $row['count'];
        //если параметр rowNum установлен в -1 ($limit), возвращаем таблицу целиком.
        if ($limit=='all') {
            $limit = $count;
        };
        // Вычисляем общее количество страниц.
        if( $count > 0 && $limit > 0) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }
        // Если запрашиваемый номер страницы больше общего количества страниц,
// то устанавливаем номер страницы в максимальный.
        if ($page > $total_pages) $page=$total_pages;
// Вычисляем начальное смещение строк.
        $start = $limit*$page - $limit;

// Если начальное смещение отрицательно,
// то устанавливаем его в 0.
// Например, когда пользователь
// выбрал 0 в качестве запрашиваемой страницы.
        if($start <0) $start = 0;

        $query="Select id,name from (
                  Select DISTINCT id,name,ROW_NUMBER() OVER (ORDER BY $sidx $sord) AS x
                  from medline39.dbo.REGIONS where PARENT_ID=4
                ) AS y
                WHERE y.x BETWEEN ".$start." AND ".($start+$limit)." ORDER BY y.x, $sidx $sord";
        break;

    case 'Апт. Сети':
        // Вычисляем количество строк. Это необходимо для постраничной навигации.
        $query="select Count(*) as count from medline39.dbo.FIRMS as F1
                inner join medline39.dbo.FIRMS as F2 on F1.ID=F2.PARENT_ID
                where f1.PARENT_ID<>0 and f1.DELETED<>100";
        $result=sqlsrv_query($conn,$query) or die( print_r( sqlsrv_errors(), true));
        $row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC);
        $count = $row['count'];
        //если параметр rowNum установлен в -1 ($limit), возвращаем таблицу целиком.
        if ($limit=='all') {
            $limit = $count;
        };
        // Вычисляем общее количество страниц.
        if( $count > 0 && $limit > 0) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }
        // Если запрашиваемый номер страницы больше общего количества страниц,
// то устанавливаем номер страницы в максимальный.
        if ($page > $total_pages) $page=$total_pages;
// Вычисляем начальное смещение строк.
        $start = $limit*$page - $limit;

// Если начальное смещение отрицательно,
// то устанавливаем его в 0.
// Например, когда пользователь
// выбрал 0 в качестве запрашиваемой страницы.
        if($start <0) $start = 0;
        $query="Select id,name from (
                  Select DISTINCT f1.ID as id ,F1.NAME as name,ROW_NUMBER() OVER (ORDER BY F1.name $sord) AS x
                  from medline39.dbo.FIRMS as F1
                  inner join medline39.dbo.FIRMS as F2 on F1.ID=F2.PARENT_ID
                  where f1.PARENT_ID<>0 and f1.DELETED<>100
                ) AS y
                WHERE y.x BETWEEN ".$start." AND ".($start+$limit)." ORDER BY y.x, $sidx $sord";
        break;
    case 'Аптеки':
        // Вычисляем количество строк. Это необходимо для постраничной навигации.
        $query="select Count(*) as count from medline39.dbo.FIRMS as F
                inner join medline39.dbo.FIRM_TO_TYPES as FTT on F.ID=FTT.FIRMS_ID
                where F.DELETED<>100 and FTT.DELETED<>100 and F.NODES_ID<>0 and F.PARENT_ID<>0 and FTT.TYPE=7";
        $result=sqlsrv_query($conn,$query) or die( print_r( sqlsrv_errors(), true));
        $row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC);
        $count = $row['count'];
        //если параметр rowNum установлен в -1 ($limit), возвращаем таблицу целиком.
        if ($limit=='all') {
            $limit = $count;
        };
        // Вычисляем общее количество страниц.
        if( $count > 0 && $limit > 0) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }
        // Если запрашиваемый номер страницы больше общего количества страниц,
// то устанавливаем номер страницы в максимальный.
        if ($page > $total_pages) $page=$total_pages;
// Вычисляем начальное смещение строк.
        $start = $limit*$page - $limit;

// Если начальное смещение отрицательно,
// то устанавливаем его в 0.
// Например, когда пользователь
// выбрал 0 в качестве запрашиваемой страницы.
        if($start <0) $start = 0;
        $query="Select id,name from (
                Select F.ID as id,F.NAME as name,ROW_NUMBER() OVER (ORDER BY $sidx $sord) AS x
                from medline39.dbo.FIRMS as F
                inner join medline39.dbo.FIRM_TO_TYPES as FTT on F.ID=FTT.FIRMS_ID
                where F.DELETED<>100 and FTT.DELETED<>100 and F.NODES_ID<>0 and F.PARENT_ID<>0 and FTT.TYPE=7
                ) AS y
              WHERE y.x BETWEEN ".$start." AND ".($start+$limit)." ORDER BY y.x, $sidx $sord";
        break;
    case 'Поставщики':
        // Вычисляем количество строк. Это необходимо для постраничной навигации.
        $query="select Count(*) as count  from medline39.dbo.FIRMS as F
                inner join medline39.dbo.FIRM_TO_TYPES as FTT on F.ID=FTT.FIRMS_ID
                where F.DELETED<>100 and  FTT.DELETED<>100 and F.NODES_ID<>0 and F.PARENT_ID<>0 and FTT.TYPE=6";
        $result=sqlsrv_query($conn,$query) or die( print_r( sqlsrv_errors(), true));
        $row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC);
        $count = $row['count'];
        //если параметр rowNum установлен в -1 ($limit), возвращаем таблицу целиком.
        if ($limit=='all') {
            $limit = $count;
        };
        // Вычисляем общее количество страниц.
        if( $count > 0 && $limit > 0) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }
        // Если запрашиваемый номер страницы больше общего количества страниц,
// то устанавливаем номер страницы в максимальный.
        if ($page > $total_pages) $page=$total_pages;
// Вычисляем начальное смещение строк.
        $start = $limit*$page - $limit;

// Если начальное смещение отрицательно,
// то устанавливаем его в 0.
// Например, когда пользователь
// выбрал 0 в качестве запрашиваемой страницы.
        if($start <0) $start = 0;
        $query="Select * from (
                  Select F.ID as id,F.NAME as name,ROW_NUMBER() OVER (ORDER BY $sidx $sord) AS x
                  from medline39.dbo.FIRMS as F
                  inner join medline39.dbo.FIRM_TO_TYPES as FTT on F.ID=FTT.FIRMS_ID
                  where F.DELETED<>100 and  FTT.DELETED<>100 and F.NODES_ID<>0 and F.PARENT_ID<>0 and FTT.TYPE=6
                ) AS y
              WHERE y.x BETWEEN ".$start." AND ".($start+$limit)." ORDER BY y.x, $sidx $sord";
        break;
    default:
        throw new Exception('error rules level filter!)');
};


//echo $query;




// Запрос для получения данных.
$result=sqlsrv_query($conn,$query) or die( print_r( sqlsrv_errors(), true));

// Заголовок с указанием содержимого.
header("Content-type: text/xml;charset=utf-8");
$s = "<?xml version='1.0' encoding='utf-8'?>";
$s .=  "<rows>";
$s .= "<page>".$page."</page>";
$s .= "<total>".$total_pages."</total>";
$s .= "<records>".$count."</records>";

// Обязательно передайте текстовые данные в CDATA
while($row=sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)) {
//echo $row['NAME'].$row['parent'].$row['address1'].$row['region']."<br>";

    $s .= "<row>";
    $s .= "<cell>". $row['id']."</cell>";
    $s .= "<cell><![CDATA[". $row['name']."]]></cell>";
    $s .= "</row>";

}
$s .= "</rows>";
echo $s;


$query = "SELECT *
        FROM (
           SELECT *, ROW_NUMBER() OVER (ORDER BY $sidx $sord) AS x
           FROM firmsInfo".$qWhere."
        ) AS y
        WHERE y.x BETWEEN ".$start." AND ".($start+$limit)." ORDER BY y.x, $sidx $sord;";
?>