                               /**
 * Created by Alexander on 19.02.2016.
 */
                                    /*Данные запрашиваемые из БД */



$(function () {
//Делаем табы
        //Открываем таблицы по мере обновления табов.
        var initialized = [false, false, false];
        $("#tabs").tabs({
            create: function (event, ui) {
                creationGrid0();
                initialized[0] = true;
                makeAutoComplite();
            },
            activate: function (event, ui) {
                //Таблица с подписками
                if (ui.newTab.index() == 0 && !initialized[0]) {
                    creationGrid0();
                    initialized[0] = true;
                    makeAutoComplite();
                }
                //Таблица с фирмами
                else if (ui.newTab.index() == 1 && !initialized[1]) {
                    creationGrid1();
                    initialized[1] = true;
                    makeAutoComplite();
                }
                //структура прайса  (SQL запрос)
                else if (ui.newTab.index() == 2 && !initialized[2]) {
                    getPriceStructure();
                    initialized[2] = true;
                }
                //Рисуем графики
                else if (ui.newTab.index() == 3 & !initialized[3]) {
                    $("#chartlinesfrom, #chartlinesto").datepicker({dateFormat: "dd.mm.yy"}); //Добавляем календари сверху
                    $("#accordion").accordion({ //аккордион
                        collapsible: true,
                        active: false,
                        heightStyle: content,
                        activate: function (event, ui) {
                            getClientsList();
                        }
                    });
                    drawGraphs();
                    initialized[3] = true;
                }
            }
        });
        //

//Подписки
        function creationGrid0() {
            $("#subsInfo").jqGrid({
                url: "php/subsInfo.php",
                mtype: "GET",
                datatype: "xml",
                colNames: ["id", "name", "timeOf", "is_activee"],
                colModel: [
                    {name: "id", width: 100, searchoptions: {sopt: ['eq', 'ne', 'bw', 'cn']}, align: "center"},
                    {name: "name", width: 300, searchoptions: {sopt: ['eq', 'ne', 'bw', 'cn']}, align: "center"},
                    {name: "timeOf", width: 300, align: "center"},
                    {name: "is_activee", width: 100, align: "center", searchoptions: {sopt: ['eq', 'ne']}}
                ],
                pager: '#pager',
                rowNum: '20',
                rowList: [20, 50, 'all'],
                sortname: "id",
                viewrecords: true,
                gridview: true,
                autoencode: true,
                caption: "Подписки",
                height: 'auto',
                autowidth: true,
                subGrid: true, //Подтаблица subsInfoDetails
                //полноценная вложенная таблица
                subGridRowExpanded: function (subgrid_id, row_id) {
                    // передаем 2 параметра
                    // subgrid_id используется для создания уникального идентификатора diva подчиненной таблицы
                    // the row_id номер разворачиваемой строчки
                    var subgrid_table_id;
                    subgrid_table_id = subgrid_id + "_t";
                    jQuery("#" + subgrid_id).html("<table id='" + subgrid_table_id + "' class='scroll'></table>");
                    jQuery("#" + subgrid_table_id).jqGrid({

                        url: "php/subsInfoDetails.php?q=2&id=" + row_id,
                        datatype: "xml",
                        colNames: ['node_firm', 'name', 'doc_type', 'base_file', 'base_timeOf', 'error_text', 'actual_days'],
                        colModel: [
                            {name: "node_firm", index: "node_firm", width: 30, key: true},
                            {name: "name", index: "name", width: 200},
                            {name: "doc_type", width: 30, align: "center"},
                            {name: "base_file", width: 200, align: "center"},
                            {name: "base_timeOf", width: 150, align: "center"},
                            {name: "error_text", width: 300, align: "center"},
                            {name: "actual_days", width: 30, align: "center"}
                        ],
                        height: '100%',
                        autowidth: true,
                        rowNum: 'all',
                        sortname: 'name',
                        sortorder: "asc",
                        altRows: false //полосатая таблица
                    });
                }
            });


// Подписки кнопки по навигации умолчанию
            $("#subsInfo").jqGrid('navGrid', '#pager', {
                    add: false,
                    del: false,
                    edit: false,
                    refresh: false,
                    search: true,
                    view: false,
                },
                {}, // default settings for edit
                {}, // default settings for add
                {}, // delete instead that del:false we need this
                {closeOnEscape: true, multipleSearch: true, closeAfterSearch: true}, // search options
                {}, /* view parameters*/
                {} /*refreshing parametrs*/
            );

            //Подписки добавляем панель поиска
            $("#subsInfo").jqGrid('filterToolbar', {});
        }

//Фирмы
        function creationGrid1() {
            $("#firmsInfo").jqGrid({
                url: "php/firmsInfo.php",
                mtype: "GET",
                datetype: "xml",
                ignoreCase: true,
                colNames: ["nodes_id", "name", "parent", "address1", "region"],
                colModel: [
                    {name: "nodes_id", width: '50'},
                    {name: "name", width: '300'},
                    {name: "parent", width: '300'},
                    {name: "address1", width: '300'},
                    {name: "region", width: '300'}
                ],
                pager: '#pager2',
                rowNum: '20',
                rowList: [20, 50, 'all'],
                sortname: "nodes_id",
                viewrecords: true,
                gridview: true,
                autoencode: true,
                caption: "Фирмы",
                height: 'auto',
                autowidth: 'true',
                subGrid: true,//Подтаблица firmsInfoDetails
                //полноценная вложенная таблица
                subGridRowExpanded: function (subgrid_id, row_id) {
                    // передаем 2 параметра
                    // subgrid_id используется для создания уникального идентификатора diva подчиненной таблицы
                    // the row_id номер разворачиваемой строчки используется для получения значения nodes_id
                    var subgrid_table_id;
                    var nodes_id;
                    nodes_id = $("#tabs-2 #" + row_id + " td:nth-child(2)").text()
                    subgrid_table_id = subgrid_id + "_t";
                    jQuery("#" + subgrid_id).html("<table id='" + subgrid_table_id + "' class='scroll'></table>");
                    jQuery("#" + subgrid_table_id).jqGrid({
                        url: "php/firmsInfoDetails.php?q=2&nodes_id=" + nodes_id,
                        datatype: "xml",
                        colNames: ['ID', 'ID_OLD', 'PARENT_ID', 'TYPENAME', 'SUBS_ID', 'USERNAME', 'PASSWORD'],
                        colModel: [
                            {name: "ID", index: "id", width: 50, key: true},
                            {name: "ID_OLD", index: "id_old", width: 20},
                            {name: "PARENT_ID", index: "parent_id", width: 50, align: "center"},
                            {name: "TYPENAME", index: "typename", width: 200, align: "center"},
                            {name: "SUBS_ID", index: "subs_id", width: 150, align: "center"},
                            {name: "USERNAME", index: "username", width: 300, align: "center"},
                            {name: "PASSWORD", index: "password", width: 300, align: "center"}
                        ],
                        height: '100%',
                        autowidth: true,
                        rowNum: 'all',
                        sortname: 'id',
                        sortorder: "asc",
                        altRows: false //полосатая таблица
                    });
                }
            })
            //Фирмы добавляем панель поиска
            $("#firmsInfo").jqGrid('filterToolbar', {});
        }

        function creationGrid2() {
            console.log('MORE GRID');
        }

//Автодополнение поисковых полей.
        function makeAutoComplite() {
            //Узнаем с какой таблицей и полем работает пользователь.
            $("input[id*=name],input[id*=parent],input[id*=address1]").click(function () {
                tableFiled = $(this).parent().parent().parent().parent().parent().parent().attr('id')
                window.activeTable = tableFiled.split('_')[1]
                window.activeField = tableFiled.split('_')[2]
            })

            //Автодополнение реализовано через функцию.
            $("input[id*=name],input[id*=parent],input[id*=address1]").autocomplete({
                minLength: 3, //минимальное кол-во символов
                source: function (request, response) {
                    $.ajax({
                        url: "php/autocomplite.php",
                        dataType: "xml",
                        // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
                        data: {
                            table: window.activeTable,
                            field: window.activeField,
                            nameStartWith: request.term //набираемый текст
                        },
                        //обработка успешного выполнения запроса
                        success: function (xmlResponse) {
                            console.log(xmlResponse);
                            response($("row", xmlResponse).map(function () {
                                    return {value: $("cell", this).text()}
                                })
                            )
                        }
                    });
                }
            });
        }

//Функция получения текста запроса из xml файла
        function getPriceStructure() {
            $('#tabs-3 input[type="button"]').click(function () {
                $.get('php/accessQueries.php', {nodes_id: $("#nodes_id").val(), doc_id: $("#doc_id").val()},
                    function (data) {
                        $('#sqlStructureResult').html(data);
                        alert('Загрузка завершена.');
                    });

            });
        }

//Построение графиков

        function drawGraphs() {
            $('#tabs-4 input[type="button"]').click(function () {
                $.ajax({
                    beforeSend: function() {
                        $("#loading").dialog({
                            modal: true,
                            height: 50,
                            width: 200,
                            zIndex: 999,
                            resizable: false,
                            title: "Please wait loading..."
                        })
                        $("#loading").dialog("open");
                    },
                    methode:'GET',
                    async:false,
                    dataType:'json',
                    url: 'php/jqplot-data.php',
                    data: {
                        from: $("#chartlinesfrom").val(),
                        to: $("#chartlinesto").val(),
                        range: $("#range").val()
                    }
                    })
                    .fail(function() {
                        $("#loading").dialog("close");
                        alert( "error try later" );
                    })
                    .done (function (data) {
                    $("#loading").dialog("close");
                        //После ответа сервера рисуем график
                    /*
                     var x={"\u0412\u043e\u043b\u043e\u0433\u0434\u0430":{"32016":50215.76,"22016":60287.19,"12016":63766.19},"\u041a\u0430\u043b\u0438\u043d\u0438\u043d\u0433\u0440\u0430\u0434":{"32016":78766635.243,"12016":118898801.806,"22016":118841957.278},"\u041a\u0440\u0430\u0441\u043d\u043e\u0434\u0430\u0440\u0441\u043a\u0438\u0439 \u043a\u0440\u0430\u0439":{"22016":71899686.838,"12016":127951973.845,"32016":58851399.116},"\u041c\u043e\u0441\u043a\u0432\u0430":{"32016":43778657.91,"22016":61356736.67,"12016":85656635.75},"\u041c\u0443\u0440\u043c\u0430\u043d\u0441\u043a":{"22016":175732254.166,"12016":165999534.305,"32016":109492734.015},"\u041d\u043e\u0432\u0433\u043e\u0440\u043e\u0434":{"12016":47246905.89,"22016":41590937.083,"32016":28869438.479},"\u041f\u0435\u0442\u0440\u043e\u0437\u0430\u0432\u043e\u0434\u0441\u043a":{"12016":81937964.836,"32016":56295418.777,"22016":81714551.629},"\u041f\u0441\u043a\u043e\u0432":{"32016":31787553.182,"22016":44462348.398,"12016":47088726.154},"\u0420\u0435\u0441\u043f\u0443\u0431\u043b\u0438\u043a\u0430 \u041a\u0430\u0440\u0435\u043b\u0438\u044f":{"22016":93360325.806,"32016":61072929.452,"12016":89275250.366},"\u0421.-\u041f\u0435\u0442\u0435\u0440\u0431\u0443\u0440\u0433":{"32016":1201376239.236,"12016":1973955201.632,"22016":1635298500.47},"\u0421\u043c\u043e\u043b\u0435\u043d\u0441\u043a":{"22016":22467052.67,"12016":28040670.86,"32016":17816990.77},"\u0421\u0442\u0430\u0432\u0440\u043e\u043f\u043e\u043b\u044c\u0441\u043a\u0438\u0439 \u043a\u0440\u0430\u0439":{"32016":859738.33,"12016":1935928.72,"22016":1364922.134}};
                     var y={"3.2016":50215.76,"2.2016":60287.19,"1.2016":63766.19};
                     var header=[];
                     var data = [];
                     var line=[];
                     var i = 0 ;
                     $.each(y, function(index, value) {

                     if (typeof(value)=='object') {
                     header[i]=index
                     $.each(value, function (d,s) {line.push([d,s])})
                     data[i]=line;
                     line=[];
                     i++;
                     } else {data.push([index, value])}
                     });
                     */
                        var line1 = [];
                        $.each(data, function(index, value) {
                            line1.push([index, value])
                        });
                        //Данные по дням
                        var day_conf={
                            title:'Распределение по дням',
                            legend: {
                                show: true,
                                placement: 'outside'
                            },
                            seriesDefaults: { //Плавные линии вместо ломаных
                                rendererOptions: {
                                    smooth: true
                                }
                            },
                            axes:{ //Настройка осей
                                xaxis:{
                                    renderer:$.jqplot.DateAxisRenderer, //Поддержка дат
                                    tickRenderer: $.jqplot.CanvasAxisTickRenderer, //Дополнительные опции для оси X
                                    tickOptions:{
                                        formatString:'%d.%m.%y', //Формат даты
                                        angle:'-30' //угол наклона
                                    },
                                    tickInterval:'1 day', //частота отсечек на оси x
                                    max:$("#chartlinesto").val()
                                },
                                yaxis:{
                                    //min:0,
                                    tickOptions:{formatString:"%'d"} //разделение разрядов
                                }
                            },
                            series:[
                                {
                                    lineWidth:4,
                                    markerOptions:{style:'square'},
                                    pointLabels: {show: true}, //отображения значений на метках графика
                                }
                            ],
                        };
                        //Данные по месяцам
                        var month_conf={
                            title:'Распределение по месяцам',
                            legend: {
                                show: true,
                                placement: 'outside'
                            },
                            seriesDefaults: { //Плавные линии вместо ломаных
                                rendererOptions: {
                                    smooth: true
                                }
                            },
                            axes:{ //Настройка осей
                                xaxis:{
                                    tickRenderer: $.jqplot.CanvasAxisTickRenderer, //Дополнительные опции для оси X
                                    tickOptions:{angle:'-30'}, //угол наклона
                                    min:0,
                                    max:13,
                                    tickInterval:1, //частота отсечек на оси x
                                },
                                yaxis:{
                                  //  min:0,
                                    tickOptions:{formatString:"%'d"} //разделение разрядов
                                }
                            },
                            series:[
                                {
                                    lineWidth:4,
                                    markerOptions:{style:'square'},
                                    pointLabels: {show: true}, //отображения значений на метках графика
                                }
                            ]
                        };
                        //Данные по годам
                        var year_conf={
                            title:'Распределение по годам',
                            seriesDefaults: { //Плавные линии вместо ломаных
                                rendererOptions: {
                                    smooth: true
                                }
                            },
                            axes:{ //Настройка осей
                                xaxis:{
                                    tickRenderer: $.jqplot.CanvasAxisTickRenderer, //Дополнительные опции для оси X
                                    tickOptions:{angle:'-30'}, //угол наклона
                                    min:2014,
                                    max:2020,
                                    tickInterval:1, //частота отсечек на оси x
                                },
                                yaxis:{
                                  //  min:0,
                                    tickOptions:{formatString:"%'d"} //разделение разрядов
                                }
                            },
                            series:[
                                {
                                    lineWidth:4,
                                    markerOptions:{style:'square'},
                                    pointLabels: {show: true}, //отображения значений на метках графика
                                }
                            ]
                        };
                        switch ($("#range").val()) {
                            case 'day':
                                plot_conf=day_conf;
                                break
                            case 'month':
                                plot_conf=month_conf;
                                break
                            case 'year':
                                plot_conf=year_conf;
                                break;
                        }
                        var plot1 = $.jqplot('chartdivlines', [line1], plot_conf).replot();
                    })
                })
            };
//Получаем список клиентов для детальных графиков
        function getClientsList () {
            $("client").click (function (){
                $.ajax ({
                    methode:'GET',
                    dataType:'json',
                    url:'php/clientList.php'
                })
                    .done (function (data){
                    console.log(data);
                })
            })

        }
});




