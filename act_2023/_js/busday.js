var busPoint = {}
busPoint.sPointYS = []; //서울 > 양양행 (사당선)
busPoint.sPointYJ = []; //서울 > 양양행 (종로선)
busPoint.sPointSY = []; //양양 > 서울행

busPoint.ePointYend = []; //서울 > 양양 도착
busPoint.ePointEend = []; //서울 > 동해 도착
busPoint.ePointSend = []; //양양 > 서울 도착
busPoint.ePointAend = busPoint.ePointSend; //동해 > 서울 도착

busPoint.sPointES = busPoint.sPointYS; //서울 > 동해행 (사당선)
busPoint.sPointEJ = busPoint.sPointYJ; //서울 > 동해행 (종로선)
busPoint.sPointAE = []; //동해 > 서울행

//출발, 도착 정류장 일열 텍스트
var busPoint_1 = "";
var busPoint_2 = "";
var busPoint_3 = "";
var busPoint_4 = "";

var MARKER_SPRITE_X_OFFSET = 29,
    MARKER_SPRITE_Y_OFFSET = 50;
var busPointListY1 = {};
var busPointListY2 = {};
var busPointListS1 = {}
var busPointListA1 = {}

var params = "gubun=pointlist";
$j.ajax({
    type: "POST",
    url: "/act_2023/front/bus/view_bus_point.php",
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

            if(keyCode == "SY5" || keyCode == "AE5"){
            }else{
                if(item == "End"){
                    keyCode = "busPoint.ePoint" + keyCode;
                    var endText = "도착";
                }else{
                    keyCode = "busPoint.sPoint" + keyCode.substring(0, 2);
                    var endText = "출발";
                }

                if(eval(keyCode + ".length") == 0){
                    eval(keyCode + ".push({'code':'N', 'codename':'" + endText + "'})");
                }

                eval(keyCode + ".push({'code':'" + keyName + "', 'codename':'" + keyName + "', 'time':'" + itemTime + "', 'time2':'" + itemTime2 + "', 'point':'" + itemPoint + "', 'lat':'" + itemlat + "', 'lng':'" + itemlng + "'})");
            }
        });
        
        //사당 > 양양
        $j.each(busPoint.sPointYS, function(key, item) {
            if(key > 0){
                if(key == 5){
                    busPoint_1 += item.codename;
                }else{
                    busPoint_1 += item.codename + " &gt; ";
                }

                var num = (key - 1);
                var X_OFFSET = MARKER_SPRITE_X_OFFSET * num;
                var Y_OFFSET = MARKER_SPRITE_Y_OFFSET * 3;

                var timeText1 = item.time.split(":");
                eval("busPointListY1['" + item.codename + "'] = [" + X_OFFSET + ", " + Y_OFFSET + ", '" + item.lat + "', '" + item.lng + "', '" + item.point + "', '탑승시간 : <font color=\"red\">" + timeText1[0] + "시 " + timeText1[1] + "분</font>', " + num + "]");
                //console.log("busPointListY2['" + item.codename + "']  = [" + X_OFFSET + ", " + Y_OFFSET + ", '" + item.lat + "', '" + item.lng + "', '" + item.point + "', '탑승시간 : <font color=\"red\">" + timeText1[0] + "시 " + timeText1[1] + "분</font>', " + num + "]");
            }
            //console.log(key + " / " +  item.codename);
        });

        //종로 > 양양
        $j.each(busPoint.sPointYJ, function(key, item) {
            if(key > 0){
                if(key == 4){
                    busPoint_2 += item.codename;
                }else{
                    busPoint_2 += item.codename + " &gt; ";
                }

                var num = (key - 1);
                var X_OFFSET = MARKER_SPRITE_X_OFFSET * num;
                var Y_OFFSET = MARKER_SPRITE_Y_OFFSET * 3;

                var timeText1 = item.time.split(":");
                eval("busPointListY2['" + item.codename + "'] = [" + X_OFFSET + ", " + Y_OFFSET + ", '" + item.lat + "', '" + item.lng + "', '" + item.point + "', '탑승시간 : <font color=\"red\">" + timeText1[0] + "시 " + timeText1[1] + "분</font>', " + num + "]");
            }
        });

        //양양 > 서울
        $j.each(busPoint.sPointSY, function(key, item) {
            if(key > 0){
                if(key == 5){
                    busPoint_3 += item.codename;
                }else{
                    busPoint_3 += item.codename + " &gt; ";
                }

                var num = (key - 1);
                var X_OFFSET = MARKER_SPRITE_X_OFFSET * num;
                var Y_OFFSET = MARKER_SPRITE_Y_OFFSET * 3;

                var timeText1 = item.time.split(":");
                var timeText2 = item.time2.split(":");
                eval("busPointListS1['" + item.codename + "'] = [" + X_OFFSET + ", " + Y_OFFSET + ", '" + item.lat + "', '" + item.lng + "', '" + item.point + "', '탑승시간 : <font color=\"red\">" + timeText1[0] + "시 " + timeText1[1] + "분 / " + timeText2[0] + "시 " + timeText2[1] + "분</font>', " + num + "]");
            }
        });

        //동해 > 서울
        $j.each(busPoint.sPointAE, function(key, item) {
            if(key > 0){
                if(key == 3){
                    busPoint_4 += item.codename;
                }else{
                    busPoint_4 += item.codename + " &gt; ";
                }

                var num = (key - 1);
                var X_OFFSET = MARKER_SPRITE_X_OFFSET * num;
                var Y_OFFSET = MARKER_SPRITE_Y_OFFSET * 3;

                var timeText1 = item.time.split(":");
                var timeText2 = item.time2.split(":");
                eval("busPointListA1['" + item.codename + "'] = [" + X_OFFSET + ", " + Y_OFFSET + ", '" + item.lat + "', '" + item.lng + "', '" + item.point + "', '탑승시간 : <font color=\"red\">" + timeText1[0] + "시 " + timeText1[1] + "분 / " + timeText2[0] + "시 " + timeText2[1] + "분</font>', " + num + "]");
            }
        });
    }
})

var busPointList = {
    "YS": { li: busPoint_1 }, //사당 > 양양
    "YJ": { li: busPoint_2 }, //종로 > 양양
    "SY": { li: busPoint_3 }, //양양 > 서울

    "ES": { li: busPoint_1 }, //사당 > 동해
    "EJ": { li: busPoint_2 }, //종로 > 동해
    "AE": { li: busPoint_4 } //동해 > 서울
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

    // var arrObjs = eval("busPoint.sPoint" + getBusNum(busnum, "2"));
    // var arrData = getListFilter(arrObjs, "code", obj.value)[0];

    // if(getBusNum(busnum, "1") == "5"){ //저녁 차량
    //     var strbustime = arrData.time2;
    // }else{ //오후 차량 및 기타
    //     var strbustime = arrData.time1;
    // }

    busnum = busnum.substring(0, 3)
    if(busnum == "ESa"){
		busnum = "YSa";
	}else if(busnum == "EJo"){
		busnum = "YJo";
	}
    
    var params = "gubun=point&res_spointname=" + obj.value + "&res_bus=" + busnum;
    $j.ajax({
        type: "POST",
        url: "/act_2023/front/bus/view_bus_point.php",
        data: params,
        success: function(data) {
            objStop.html("탑승시간 : " + data.split("|")[0] + "<br> 탑승위치 : " + data.split("|")[1]);
        }
    })
    
}

function getListFilter(data, key, value){
    return data.filter(function (object) { 
        return object[key] === value;
    });
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
        if(strbusnum == "ESa"){
            rtnVlu = "YJa";
        }else if(strbusnum == "EJo"){
            rtnVlu = "YJo";
        }
    }
    
    return rtnVlu;
}