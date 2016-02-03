/*
 Created by Alexander on 26.01.2016.
 При загрузке получаем список вкладок и асинхронно заполняем их информацией
*/

$(document).ready(function () {
    console.log ("DOM loaded.Jquery-2.1.4");
    //Запрос полных данных из БД для всех вкладок.
    var tabs = $('input[name="maincontainer-radio"]');
    for (var i=0;i<tabs.length;i++) {
        console.log('Try to ask '+tabs[i].id);
        getInfo(tabs[i].id);
    }

    //Добавляем функцию поиска в каждую вкладку
    $(".inputFilter").keyup(debounce(function makeSearchableTabs () {
        console.log('Enter FN');

        var tabSelector=
            ".maincontainer > #subsInfo:checked ~ .maincontainer-subsInfo," +
            ".maincontainer > #firmsInfo:checked ~ .maincontainer-firmsInfo," +
            ".maincontainer > #moreInfo:checked ~ .maincontainer-moreInfo";
        //Все видимые строки таблицы
        //var trNotHidden= $("tbody tr",tabSelector).not('[style="display: none;"]')
        var trAll = $("tbody tr",tabSelector);

        if ( $(".filtred").length) {
            console.log ('Found previous Filter');
            trAll = $(".filtred",tabSelector);
        } else {
            console.log ('NO FILTERS');
        }

        //Фильтры для данной таблицы
        var searchInputs = $(".inputFilter",tabSelector);

//Собираем инфу о фильрах
        var searchValArr=[];
        for (var i=0; i<searchInputs.length;i++) {
            if( $(searchInputs[i]).children().val() ) { //если стоит фильтр
                searchValArr[i]=$(searchInputs[i]).children().val();
                console.log(searchValArr);
            }
        }
        console.log (trAll);
//Фильтруем данные
        trAll.hide() //скрываем всю таблицу
        for (var i=0;i<searchValArr.length;i++) {
            if (searchValArr[i]) {
                var colIndex = $(searchInputs[i]).index() + 1; //Узнаем номер столбца таблицы
                var colData = $("td:nth-child(" + colIndex + ")", trAll); //выбираем все  данные столбца
                colData.filter(":contains(" + searchValArr[i] + ")").parent("tr").addClass('filtred');
                trAll= $(".filtred");
            }
        }
        trAll.show();

/*
        //Фильруем данные
        for (var i =0; i<searchInputs.length; i++) {
            if( $(searchInputs[i]).children().val() ) { //если стоит фильтр
                var searchVal = $(searchInputs[i]).children().val().toLowerCase() //переводим строку поиска в нижний регистр
                var colIndex = $(searchInputs[i]).index() + 1; //Узнаем номер столбца
                var colData = $("td:nth-child(" + colIndex + ")", trAll); //выбираем все  данные столбца из видимых строк
                trAll.hide(); //Скрываем все данные
                colData.filter(":contains(" + searchVal + ")").parent("tr").show();
                useFilter=true;
            }
            if (!useFilter) {         //если нет фильтра то отображаем все строки
                trAll.show();
            }
        }
*/

    },500));


    });
//Запрос данных из БД для каждой вкладки
function getInfo(tab) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange=function () {
        if (xhr.status==200 && xhr.readyState==4) {
            document.querySelectorAll('div[class$='+tab+']').item(0).innerHTML=xhr.responseText;
        }
    };
    var params="tab="+tab;
    xhr.open("POST","php/getInfo.php",false);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(params);
}


//Функция задержки
function debounce(fn, duration) {
    var timer;
    return function() {
        clearTimeout(timer);
        timer = setTimeout(fn, duration)
    }
}




