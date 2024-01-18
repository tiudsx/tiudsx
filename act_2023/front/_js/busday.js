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

/**달력에 날짜 선택용 json */
var json_busDay = {};

/**
 * 버스 예약 가능한 날짜
 * @param {*} seq 
 * @param {*} bus_line 
 */
function fnBusDate(seq, bus_line){
    var formData = {
        "code":"busday",
        "seq":seq
    }

    bus_line = bus_line.substring(0, 2);
 
    $j.post("/act_2023/front/bus/view_bus_day.php", formData,
        function(data, textStatus, jqXHR) {
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
        }).fail(function(jqXHR, textStatus, errorThrown) {
    });
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

function fnBusTime(obj, busType) {
    var objStop = $j(obj).parent().find("#stopLocation");
    if (obj.value == "N") {
        objStop.text('');
        return;
    }

    if(busType == "S"){
        var bus_selected = $j("ul[class=busLine]:eq(0) li[class=on]");
    }else if(busType == "E"){
        var bus_selected = $j("ul[class=busLine]:eq(1) li[class=on]");
    }

    var bus_gubun = bus_selected.attr("bus_gubun");
    var arrObj = eval("busPoint." + bus_gubun); //정류장 목록
    var data = arrObj.filter(row => row.code == obj.value)[0] //선택 정류장 데이터

    objStop.html(data["point"] + " <span style='color:red;'>(" + data["time"].split(":")[0] + "시 " + data["time"].split(":")[1] + "분)</span>");
}