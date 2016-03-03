<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 02.03.2016
 * Time: 10:08
 * Заголовок для XML
 * header('Content-Type: text/xml');
 */


require_once 'sqlParse/dqml2tree.php';
$nodesId=$_GET['nodes'];
$docId=$_GET['doc'];

$fieldList= [
    'PRICE2','PRICE3','PRICE5','PRICE10','PRICE15','PRICE20','NEW_PRICE1','NEW_COND1','NEW_PRICE2','NEW_COND2',
    'NAME_FORM','MAKER_COUNTRY','PRICE1',
    'INT_EXTKEY','PACK1','QUANTITY','LIFETIME',
    'COMMENT_','NODES_ID','VENDOR','Doc_Type',
    'COSTMAKER','COSTMAKER_NDS','NDS','UPAK','Z','EAN','COSTREESTR'
];

$xml = simplexml_load_file('../files/accessQueries.xml');
//определяем базовый или индивидуальный прайс.
if ($docId) { $checkQ=$nodesId."_".$docId;} else { $checkQ=$nodesId;}


//Находим текст запроса по ноду и типу документа
$query = $xml->queries->xpath('query[@name="'.$checkQ.'"]');
echo "check query:".$query[0]['name']."<hr>";
echo $query[0];

//Передаем  текст запроса для его парсинга
$sql_query =$query[0];
$query2tree = new dqml2tree($sql_query);
$sql_tree = $query2tree->make();

//Определяем есть ли у запроса подзапрос.
$subquery=$xml->queries->xpath('query[@name="'.$sql_tree['SQL']['SELECT']['FROM']['TABLE'].'"]') ;
if (!$subquery) {
    echo "NO SUBQUERY TRY TO PARSE INST->SELECT <pre>";
    //print_r( $sql_tree['SQL']['INSERT']['INTO']['1|*INSERT']['INTO'] );
    //print_r( $sql_tree['SQL']['SELECT']);
    //print_r( $sql_tree['SQL']['SELECT']['FROM'] );
    for ($i=0;$i<count($sql_tree['SQL']['SELECT']); $i++) {
        foreach($sql_tree['SQL']['SELECT'][$i.'|*SELECT'] as $key=>$value) {
            print_r($value);
            //$str = serialize($value);echo $str."<br />";
        }
    }
    echo "</pre>";

    //foreach ($fieldList as $validFileld) {}
    } else {
    echo "SUBQUERY FOUND <br>";
    echo $subquery[0]['name'] . "<hr>";
    echo $subquery[0];
}



/*echo "<pre>";
foreach ( $sql_tree['SQL']['INSERT']['INTO']['1|*INSERT']['INTO'] as $insFields ) {
    print_r($insFields);
    echo "<hr>";
}
echo "</pre>";*/
