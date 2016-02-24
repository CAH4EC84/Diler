                               /**
 * Created by Alexander on 19.02.2016.
 */
                                    /*Данные запрашиваемые из БД */
//Подписки
    $(function () {
        console.log ('subsInfo');
        $("#subsInfo").jqGrid({
            url: "php/subsInfo.php",
            mtype: "GET",
            datatype: "xml",
            colNames: ["id", "name", "timeOf", "is_activee"],
            colModel: [
                { name: "id",width:10,searchoptions:{sopt:['eq','ne','bw','cn']} },
                { name: "name",searchoptions:{sopt:['eq','ne','bw','cn']} },
                { name: "timeOf",align: "right" },
                { name: "is_activee",width:10,  align: "right",searchoptions:{sopt:['eq','ne']} }
            ],
            pager: '#pager',
            rowNum:'10',
            rowList: [20,'all'],
            sortname: "id",
            viewrecords: true,
            gridview: true,
            autoencode: true,
            caption: "Подписки",
            height:'auto',
            autowidth:true,
            //Подтаблица
            subGrid:true,
            subGridUrl:'php/subsInfoDetails.php',
            subGridModel:[
                {
                    name: ['node_firm', 'name', 'doc_type', 'base_file', 'base_timeOf', 'error_text', 'actual_days'],
                    width: [10, 100, 10, 200, 200, 355, 10],
                    params: ['id']
                }
            ]
        });

        //Кнопки по навигации умолчанию
        $("#subsInfo").jqGrid('navGrid','#pager',{add:false,del:false,edit:false,refresh:true,search:true,view:false,},
        {}, // default settings for edit
        {}, // default settings for add
        {}, // delete instead that del:false we need this
        {closeOnEscape:true, multipleSearch:true, closeAfterSearch:true}, // search options
        {}, /* view parameters*/
        {} /*refreshing parametrs*/
        );

        //Добавляем свои кнопки
        $("#subsInfo").jqGrid('navGrid','#pager').jqGrid('navButtonAdd',"#pager",{
            caption:'',
            buttonicon:"ui-icon-cart",
            onClickButton: function () {
                console.log ('NB clicked');
            },
            position: "last",
            title:"Some new function",
            cursor: "pointer"
        });

       //Добавляем панель поиска
        $("#subsInfo").jqGrid('filterToolbar',{});
    });
/*
//Фирмы
$(function () {
    console.log ('firmsInfo');
    $("#firmsInfo").jqGrid({
        url: "php/firmsInfo.php",
        mtype: "GET",
        datatype: "xml",
        hiddengrid: true,
        ignoreCase: true,
        colNames: ["id", "parent_id", "nodes_id","name","address1","region", "subs_id"],
        colModel: [
            { name: "id",width:25 },
            { name: "parent_id",width:25 },
            { name: "nodes_id",width:15 },
            { name: "name" },
            { name: "address1" },
            { name: "region" },
            { name: "subs_id",width:15 }
        ],
        rowNum: '-1',
        scroll:true,
        sortname: "id",
        viewrecords: true,
        gridview: true,
        autoencode: true,
        caption: "Фирмы",
        height:'auto',
        autowidth:true
    });
});
*/