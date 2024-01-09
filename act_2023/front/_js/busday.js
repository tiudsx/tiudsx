var busPoint = {}
var busPoint_Num = {}
var busPoint_Map = {}

var MARKER_SPRITE_X_OFFSET = 29,
    MARKER_SPRITE_Y_OFFSET = 50;

var jsonData = null;
var params = "gubun=pointlist&shopseq=" + shopseq;
$j.ajax({
    type: "POST",
    url: "/act_2023/front/bus/view_bus_point.php",
    data: params,
    success: function(data) {
        jsonData = data;
    }
})

//버스 예약 가능한 날짜
function fnBusDate(seq, bus_line){
    var objParam = {
        "code":"busday",
        "seq":seq
    }

    bus_line = bus_line.substring(0, 2);
 
    $j.getJSON("/act_2023/front/bus/view_bus_day.php", objParam,
        function (data, textStatus, jqXHR) {
            Object.entries(data).forEach(function(el) {
                if(el[0].substring(0, 2) == "SA" || el[0].substring(0, 2) == "JO"){ //사당, 종로
                    if(json_busDay[bus_line + "_S"] == null){
                        json_busDay[bus_line + "_S"] = {};
                    }
                    
                    json_busDay[bus_line + "_S"][el[0].substring(2, 6)] = "1";
                }else{
                    if(json_busDay[bus_line + "_E"] == null){
                        json_busDay[bus_line + "_E"] = [];
                    }

                    json_busDay[bus_line + "_E"][el[0].substring(2, 6)] = "1";
                }
            });
        }
    );
}

//버스 정류장 호출 함수
function fnBusPointList(){
    $j.each(jsonData, function(key, item) {
        var arrKey = key.split("_");
        var keyCode = arrKey[0]; //행선지
        var keyName = arrKey[1]; //정류장

        var arrItem = item.split("|");
        var itemTime = arrItem[0]; //탑승시간
        var itemPoint = arrItem[1]; //정류장 위치
        var itemlat = arrItem[2]; //위도
        var itemlng = arrItem[3]; //경도

        if(busPoint[keyCode] == null){ //신규 행선지
            busPoint[keyCode] = [];

            if(!(keyCode == "Send" || keyCode == "Eend")){
                busPoint_Num[keyCode] = 0;
                busPoint_Map[keyCode] = [];
            }
        }
        
        busPoint[keyCode].push({'code': keyName, 'codename': keyName, 'time': itemTime, 'point': itemPoint, 'lat': itemlat, 'lng': itemlng});

        if(!(keyCode == "Send" || keyCode == "Eend")){
            var num = busPoint_Num[keyCode];
            busPoint_Num[keyCode]++;

            var X_OFFSET = MARKER_SPRITE_X_OFFSET * num;
            var Y_OFFSET = MARKER_SPRITE_Y_OFFSET * 3;

            var timeText = itemTime.split(":");
            timeText = '탑승시간 : <font color=\"red\">' + timeText[0] + '시 ' + timeText[1] + '분</font>';
            busPoint_Map[keyCode][keyName] = ({X_OFFSET, Y_OFFSET, itemlat, itemlng, itemPoint, timeText, num});
        }
    });
}

function getListFilter(data, key, value){
    return data.filter(function (object) { 
        return object[key] === value;
    });
}

function fnBusTime(obj, busnum, num) {
    if (num == -1) {
        var objStop = $j(obj).parent().find("#stopLocation");
    } else {
        var objStop = $j("td[id=stopLocation]").eq(num);
    }
    if (obj.value == "N") {
        objStop.text('');
        return;
    }

    busnum = busnum.substring(0, 3)
    var params = "gubun=point&bus_line=" + obj.value + "&point=" + busnum + "&shopseq=" + shopseq;
    $j.ajax({
        type: "POST",
        url: "/act_2023/front/bus/view_bus_point.php",
        data: params,
        success: function(data) {
            objStop.html("탑승시간 : " + data.split("|")[0] + "<br> 탑승위치 : " + data.split("|")[1]);
        }
    })    
}