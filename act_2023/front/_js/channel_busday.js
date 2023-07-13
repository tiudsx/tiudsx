var busPoint = {}
busPoint.sPointS = []; //서울 출발
busPoint.sPointE = []; //서울 출발 > 해변

busPoint.ePointS = []; //서울 복귀
busPoint.ePointE = []; //서울 복귀 > 도착

//출발, 도착 정류장 일열 텍스트
var busPoint_S = "";
var busPoint_E = "";

var MARKER_SPRITE_X_OFFSET = 29,
    MARKER_SPRITE_Y_OFFSET = 50;
var busPointListS = {};
var busPointListE = {};

var params = "gubun=pointlist&busSeq=" + busSeq;
$j.ajax({
    type: "POST",
    url: "/act_2023/front/bus_2023/view_bus_point.php",
    data: params,
    success: function(data) {
        $j.each(data, function(key, item) {
            var arrKey = key.split("_");
            var keyCode = arrKey[0];
            var keyName = arrKey[1];

            var arrItem = item.split("|");
            var itemTime = arrItem[0];
            var itemPoint = arrItem[1];
            var itemlat = arrItem[2];
            var itemlng = arrItem[3];
            var itemTime2 = arrItem[4];

            if(item == "End"){
                if(keyCode == "Send"){
                    keyCode = "busPoint.sPointE";
                }else{
                    keyCode = "busPoint.ePointE";
                }
                var endText = "도착";
            }else{
                if(keyCode == "사당" || keyCode == "종료" || keyCode == "동해"){
                    keyCode = "busPoint.sPointS";
                }else{
                    keyCode = "busPoint.ePointS";
                }
                var endText = "출발";
            }

            if(eval(keyCode + ".length") == 0){
                eval(keyCode + ".push({'code':'N', 'codename':'" + endText + "'})");
            }

            eval(keyCode + ".push({'code':'" + keyName + "', 'codename':'" + keyName + "', 'time':'" + itemTime + "', 'time2':'" + itemTime2 + "', 'point':'" + itemPoint + "', 'lat':'" + itemlat + "', 'lng':'" + itemlng + "'})");
        });
        
        // 서울출발
        var lastCnt = busPoint.sPointS.length - 1;
        $j.each(busPoint.sPointS, function(key, item) {
            if(key > 0){
                if(key == lastCnt){
                    busPoint_S += item.codename;
                }else{
                    busPoint_S += item.codename + " &gt; ";
                }

                var num = (key - 1);
                var X_OFFSET = MARKER_SPRITE_X_OFFSET * num;
                var Y_OFFSET = MARKER_SPRITE_Y_OFFSET * 3;

                var timeText1 = item.time.split(":");
                eval("busPointListS['" + item.codename + "'] = [" + X_OFFSET + ", " + Y_OFFSET + ", '" + item.lat + "', '" + item.lng + "', '" + item.point + "', '탑승시간 : <font color=\"red\">" + timeText1[0] + "시 " + timeText1[1] + "분</font>', " + num + "]");
            }
        });

        //서울 복귀
        lastCnt = busPoint.ePointS.length - 1;
        $j.each(busPoint.ePointS, function(key, item) {
            if(key > 0){
                if(key == lastCnt){
                    busPoint_E += item.codename;
                }else{
                    busPoint_E += item.codename + " &gt; ";
                }

                var num = (key - 1);
                var X_OFFSET = MARKER_SPRITE_X_OFFSET * num;
                var Y_OFFSET = MARKER_SPRITE_Y_OFFSET * 3;

                var timeText1 = item.time.split(":");
                var timeText2 = item.time2.split(":");
                eval("busPointListE['" + item.codename + "'] = [" + X_OFFSET + ", " + Y_OFFSET + ", '" + item.lat + "', '" + item.lng + "', '" + item.point + "', '탑승시간 : <font color=\"red\">" + timeText1[0] + "시 " + timeText1[1] + "분 / " + timeText2[0] + "시 " + timeText2[1] + "분</font>', " + num + "]");
            }
        });
    }
})

var busPointList = {
    "sPoint": { li: busPoint_S }, //서울 출발
    "ePoint": { li: busPoint_E } //서울 복귀
};

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
    var params = "gubun=point&res_spointname=" + obj.value + "&res_bus=" + busnum + "&busSeq=" + busSeq;
    $j.ajax({
        type: "POST",
        url: "/act_2023/front/bus_2023/view_bus_point.php",
        data: params,
        success: function(data) {
            objStop.html("탑승시간 : " + data.split("|")[0] + "<br> 탑승위치 : " + data.split("|")[1]);
        }
    })
}


function getBusNum(strbusnum, type){
    var rtnVlu = "";
    if(type == "1"){
        //2 : 오후 차량
        //5 : 저녁 차량
        rtnVlu = strbusnum.substring(2, 3);
    }else if(type == "2"){
        rtnVlu = strbusnum.substring(0, 2);
    }else if(type == "3"){
        rtnVlu = strbusnum.substring(0, 3);
    }
    
    return rtnVlu;
}