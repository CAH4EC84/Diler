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
//

//Добавляем функцию поиска в каждую вкладку
    $(".inputFilter").keyup(debounce(function makeSearchableTabs () {
        var tabSelector=
            ".maincontainer > #subsInfo:checked ~ .maincontainer-subsInfo," +
            ".maincontainer > #firmsInfo:checked ~ .maincontainer-firmsInfo," +
            ".maincontainer > #moreInfo:checked ~ .maincontainer-moreInfo";
        //var trNotHidden= $("tbody tr",tabSelector).not('[style="display: none;"]')
        //Все видимые строки таблицы
        var trAll = $("tbody tr",tabSelector);
        //скрываем всю таблицу
        trAll.hide()

        //Фильтры для данной таблицы
        var searchInputs = $(".inputFilter",tabSelector);

        //Фильтруем данные
        var filtredClass='';
        searchInputs.each(function () {
            if ( $(this).children().val() ) {
                var colIndex = ($(this).index() + 1).toString(); //Узнаем номер столбца таблицы
                trAll.removeClass('filtredCol'+colIndex); //Удаляем ранее установленный на него фильтр
                var colData = $("td:nth-child(" + colIndex + ")", trAll); //выбираем все  данные столбца
                colData.filter(":contains(" + $(this).children().val() + ")").parent("tr").addClass('filtredCol'+colIndex);
                filtredClass+='.filtredCol'+colIndex;
            }
        });
        //Выводим только отфильрованные данные
        if (filtredClass) {
            $(filtredClass).show();
        } else {trAll.show()};
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




