/*
 Created by Alexander on 26.01.2016.
 ��� �������� �������� ������ ������� � ���������� ��������� �� �����������
*/

$(document).ready(function () {
    console.log ("DOM loaded.Jquery-2.1.4");
    //������ ������ ������ �� �� ��� ���� �������.
    var tabs = $('input[name="maincontainer-radio"]');
    for (var i=0;i<tabs.length;i++) {
        console.log('Try to ask '+tabs[i].id);
        getInfo(tabs[i].id);
    }

    //��������� ������� ������ � ������ �������
    $(".inputFilter").keyup(debounce(function makeSearchableTabs () {
        console.log('Enter FN');

        var tabSelector=
            ".maincontainer > #subsInfo:checked ~ .maincontainer-subsInfo," +
            ".maincontainer > #firmsInfo:checked ~ .maincontainer-firmsInfo," +
            ".maincontainer > #moreInfo:checked ~ .maincontainer-moreInfo";
        //��� ������� ������ �������
        //var trNotHidden= $("tbody tr",tabSelector).not('[style="display: none;"]')
        var trAll = $("tbody tr",tabSelector);

        if ( $(".filtred").length) {
            console.log ('Found previous Filter');
            trAll = $(".filtred",tabSelector);
        } else {
            console.log ('NO FILTERS');
        }

        //������� ��� ������ �������
        var searchInputs = $(".inputFilter",tabSelector);

//�������� ���� � �������
        var searchValArr=[];
        for (var i=0; i<searchInputs.length;i++) {
            if( $(searchInputs[i]).children().val() ) { //���� ����� ������
                searchValArr[i]=$(searchInputs[i]).children().val();
                console.log(searchValArr);
            }
        }
        console.log (trAll);
//��������� ������
        trAll.hide() //�������� ��� �������
        for (var i=0;i<searchValArr.length;i++) {
            if (searchValArr[i]) {
                var colIndex = $(searchInputs[i]).index() + 1; //������ ����� ������� �������
                var colData = $("td:nth-child(" + colIndex + ")", trAll); //�������� ���  ������ �������
                colData.filter(":contains(" + searchValArr[i] + ")").parent("tr").addClass('filtred');
                trAll= $(".filtred");
            }
        }
        trAll.show();

/*
        //�������� ������
        for (var i =0; i<searchInputs.length; i++) {
            if( $(searchInputs[i]).children().val() ) { //���� ����� ������
                var searchVal = $(searchInputs[i]).children().val().toLowerCase() //��������� ������ ������ � ������ �������
                var colIndex = $(searchInputs[i]).index() + 1; //������ ����� �������
                var colData = $("td:nth-child(" + colIndex + ")", trAll); //�������� ���  ������ ������� �� ������� �����
                trAll.hide(); //�������� ��� ������
                colData.filter(":contains(" + searchVal + ")").parent("tr").show();
                useFilter=true;
            }
            if (!useFilter) {         //���� ��� ������� �� ���������� ��� ������
                trAll.show();
            }
        }
*/

    },500));


    });
//������ ������ �� �� ��� ������ �������
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


//������� ��������
function debounce(fn, duration) {
    var timer;
    return function() {
        clearTimeout(timer);
        timer = setTimeout(fn, duration)
    }
}




