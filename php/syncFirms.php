<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 18.01.2016
 * Time: 13:25
 * Синхронизация списка фирм из основной БД и нашей
 * Данный модуль не актуален 21.01.2016
 */

require_once '../conf/login.php';
$mssqlConn= sqlsrv_connect($serverName,$connectionInfo);

//Получаем данные в таблицу Firms
synctTableFirms($mssqlConn);

//Функция синхронизации таблиц
function synctTableFirms($mssqlConn)
{
    $query = "
 if (Select COUNT(*) from upi_0_2.dbo.Firms) <> (Select COUNT(*) from medline39.dbo.FIRMS as MF where MF.NODES_ID > 0 and MF.DELETED <> 100)
 begin
	DROP TABLE upi_0_2.dbo.Firms
	Select F.ID,F.PARENT_ID,F.NODES_ID,F.NAME,F.ADDRESS1,R.NAME as Region
	into upi_0_2.dbo.Firms
	from medline39.dbo.FIRMS as F left join medline39.dbo.REGIONS as R on F.STATE_ID=R.ID
	where F.NODES_ID > 0 and F.DELETED <> 100
	order by F.NODES_ID
 end
 if (Select Count(F.NODES_ID) from firms as F right join medline39.dbo.FIRMS as MF on F.NODES_ID=MF.NODES_ID
		  where MF.NODES_ID > 0 and MF.DELETED <> 100 and F.NAME <> MF.NAME) > 0
 begin
	UPDATE F
	SET F.NAME= MF.NAME, F.ADDRESS1 = MF.ADDRESS1
	FROM upi_0_2.dbo.Firms as F right join medline39.dbo.FIRMS as MF on F.NODES_ID=MF.NODES_ID
	where (MF.NODES_ID > 0 and MF.DELETED <> 100) and (F.NAME <> MF.NAME or  F.ADDRESS1 <> MF.ADDRESS1)
 end";

    sqlsrv_query($mssqlConn, $query);
}
