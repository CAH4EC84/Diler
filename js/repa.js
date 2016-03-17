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
                    $("#accordion").accordion({ //Диаграмы прячем в аккордион
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
                    refresh: true,
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
            $.ajax ({
                methode:'GET',
                dataType:'json',
                url:'php/clientList.php'
            })
                .done (function (data){
                console.log(data);
            })
        }
});




