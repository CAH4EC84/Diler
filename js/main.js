/*
 Created by Alexander on 26.01.2016.
 При загрузке получаем список вкладок и асинхронно заполняем их информацией
*/

$(document).ready(function () {
    console.log ("DOM loaded.Jquery-2.1.4");
    var tabs = $('input[name="maincontainer-radio"]');
    for (var i=0;i<tabs.length;i++) {
        console.log('Try to ask '+tabs[i].id);
        getInfo(tabs[i].id);
    }
});

function getInfo(tab) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange=function () {
        if (xhr.status==200 && xhr.readyState==4) {
            document.querySelectorAll('div[class$='+tab+']').item(0).innerHTML=xhr.responseText;
        }
    };
    var params="tab="+tab;
    xhr.open("POST","php/getInfo.php",true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    xhr.send(params);
}

