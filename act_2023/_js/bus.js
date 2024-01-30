var holidays = {
    "0101": { type: 0, title: "신정", year: "" },
    "0301": { type: 0, title: "삼일절", year: "" },
    "0505": { type: 0, title: "어린이날", year: "" },
    "0606": { type: 0, title: "현충일", year: "" },
    "0815": { type: 0, title: "광복절", year: "" },
    "1003": { type: 0, title: "개천절", year: "" },
    "1009": { type: 0, title: "한글날", year: "" },
    "1225": { type: 0, title: "크리스마스", year: "" },

    "0519": { type: 0, title: "석가탄신일", year: "2021" },

    "0920": { type: 0, title: "추석", year: "2021" },
    "0921": { type: 0, title: "추석", year: "2021" },
    "0922": { type: 0, title: "추석", year: "2021" },

    "0211": { type: 0, title: "설날", year: "2021" },
    "0212": { type: 0, title: "설날", year: "2021" },
    "0213": { type: 0, title: "설날", year: "2021" }
};

var selDate;
var busNum;
var busNumName;
var busType;

function fnBusChangeNext() {
    if ($j("#daytype").val() == 0) { //편도
        var btnonclick = $j("ul[class=busLine]:eq(0) li[class=on]").attr("onclick");
        var btntext = "";
        if ($j("#busgubun").val() == "Y") {
            btntext = "[양양행] ";
            $j(".selectStop li").eq(0).css("display", "");
            $j(".selectStop li").eq(1).css("display", "");
        } else if ($j("#busgubun").val() == "E") {
            btntext = "[동해행] ";
            $j(".selectStop li").eq(0).css("display", "");
            $j(".selectStop li").eq(1).css("display", "");
        } else {
            btntext = "[서울행] ";
            $j(".selectStop li").eq(2).css("display", "");
            $j(".selectStop li").eq(3).css("display", "");
        }
        var busname = $j("ul[class=busLine]:eq(0) li[class=on]").text();
        $j(".busLineTab").append('<li class="on" caldate="' + $j("#SurfBus").val() + '" style="cursor:pointer;" onclick="' + btnonclick.replace("fnPointList", "fnBusSeatInit").replace("this", "this, '" + busname + "'") + '">' + btntext + busname + '</li>');
    } else {
        $j(".selectStop li").eq(0).css("display", "");
        $j(".selectStop li").eq(1).css("display", "");
        $j(".selectStop li").eq(2).css("display", "");
        $j(".selectStop li").eq(3).css("display", "");

        var btntext0 = "",
            btntext1 = "";
        if ($j("#busgubun").val() == "E") {
            btntext0 = "[동해행] ";
            btntext1 = "[서울행] ";
        } else {
            btntext0 = "[양양행] ";
            btntext1 = "[서울행] ";
        }

        var btnonclick = $j("ul[class=busLine]:eq(1) li[class=on]").attr("onclick");
        var busname = $j("ul[class=busLine]:eq(1) li[class=on]").text();
        $j(".busLineTab").append('<li class="on" caldate="' + $j("#SurfBusS").val() + '" style="cursor:pointer;" onclick="' + btnonclick.replace("fnPointList", "fnBusSeatInit").replace("this", "this, '" + busname + "'") + '">' + btntext0 + $j("ul[class=busLine]:eq(1) li[class=on]").text() + '</li>');

        btnonclick = $j("ul[class=busLine]:eq(2) li[class=on]").attr("onclick");
        busname = $j("ul[class=busLine]:eq(2) li[class=on]").text();
        $j(".busLineTab").append('<li caldate="' + $j("#SurfBusE").val() + '" style="cursor:pointer;" onclick="' + btnonclick.replace("fnPointList", "fnBusSeatInit").replace("this", "this, '" + busname + "'") + '">' + btntext1 + $j("ul[class=busLine]:eq(2) li[class=on]").text() + '</li>');
        
        $j(".busLineTab2").html($j(".busLineTab").html());
    }

    $j(".busLineTab li").eq(0).click();

    $j('#resStep1').block({ message: null });
    $j('#resStep1').hide();
    $j(".busOption02").css("display", "");
    $j('#divConfirm').css("display", "");
    $j("#seatTab").css("display", "");

    // fnMapView("#seatTab", 80);
}

function fnSeatChangeSelected(arrVlu) {
    arrVlu = arrVlu.split("/");

    var returnBusNum = arrVlu[0];
    var returnBusType = arrVlu[0].substring(0, 1);
    var returnDate = $j("#SurfBusE").val();
    var returnSeat = arrVlu[1];
    var returnBusName = $j("li[busnum=" + returnBusNum + "]").text();

    var sPoint = "";
    var ePoint = "";
    
    var arrObjs = eval("busPoint.sPoint" + returnBusNum.substring(0, 2));
    var arrObje = eval("busPoint.ePoint" + returnBusType + "end");
    var selVlu = "";
    if(buschannel == 17 || buschannel == 26){ //마린서프
        selVlu = "기사문해변";
    }else if(buschannel == 20 || buschannel == 27){ //인구서프
        selVlu = "인구해변";
    }else if(buschannel == 21 || buschannel == 28){ //서프팩토리
        selVlu = "대진해변";
    }else if(buschannel == 22 || buschannel == 29){ //솔게하
        selVlu = "솔.동해점";
    }else if(buschannel == 23){ //브라보서프
        selVlu = "금진해변";
    }
        
    arrObjs.forEach(function(el) {
        if(returnBusType == "E" || returnBusType == "Y"){
            sPoint += "<option value='" + el.code + "'>" + el.codename + "</option>";
        }else{
            if(selVlu != "" && selVlu == el.code){
                sPoint += "<option value='N'>출발</option>";
                sPoint += "<option value='" + el.code + "'>" + el.codename + "</option>";
            }else if(selVlu == ""){
                sPoint += "<option value='" + el.code + "'>" + el.codename + "</option>";
            }
        }
    });
    arrObje.forEach(function(el) {
        if(returnBusType == "E" || returnBusType == "Y"){
            if(selVlu != "" && selVlu == el.code){
                ePoint += "<option value='N'>도착</option>";
                ePoint += "<option value='" + el.code + "'>" + el.codename + "</option>";
            }else if(selVlu == ""){
                ePoint += "<option value='" + el.code + "'>" + el.codename + "</option>";    
            }
        }else{
            ePoint += "<option value='" + el.code + "'>" + el.codename + "</option>";    
        }
    });

    var tbCnt = $j("#tb" + returnDate + '_' + returnBusNum).length;
    var insHtml = "";
    var bindObj = "#" + returnDate + '_' + returnBusNum;
    if (tbCnt == 0) {
        insHtml = '		<table class="et_vars exForm bd_tb " style="width:100%;margin-bottom:5px;" id="tb' + returnDate + '_' + returnBusNum + '">' +
            '			<colgroup>' +
            '				<col style="width:45px;">' +
            '				<col style="width:auto;">' +
            '				<col style="width:38px;">' +
            '			</colgroup>' +
            '			<tbody id="' + returnDate + '_' + returnBusNum + '">' +
            '				<tr>' +
            '					<th colspan="3">[' + returnDate + '] ' + returnBusName +
            '					</th>' +
            '				</tr>';
        bindObj = "#selBus" + returnBusType;
    }

    insHtml += '				<tr id="' + returnDate + '_' + returnBusNum + '_' + returnSeat + '" trseat="' + returnSeat + '">' +
        '					<th style="padding:4px 6px;text-align:center;">' + returnSeat + '번</th>' +
        '					<td style="line-height:2;">' +
        '						<select id="startLocation' + returnBusType + '" seatnum="' + returnSeat + '" name="startLocation' + returnBusType + '[]" class="select" onchange="fnBusTime(this, \'' + returnBusNum + '\', -1);">' +
        '							' + sPoint +
        '						</select> →' +
        '						<select id="endLocation' + returnBusType + '" seatnum="' + returnSeat + '" name="endLocation' + returnBusType + '[]" class="select">' +
        '							' + ePoint +
        '						</select><br>' +
        '						<span id="stopLocation"></span>' +
        '						<input type="hidden" id="hidbusSeat' + returnBusType + '" name="hidbusSeat' + returnBusType + '[]" value="' + returnSeat + '" />' +
        '						<input type="hidden" id="hidbusDate' + returnBusType + '" name="hidbusDate' + returnBusType + '[]" value="' + returnDate + '" />' +
        '						<input type="hidden" id="hidbusNum' + returnBusType + '" name="hidbusNum' + returnBusType + '[]" value="' + returnBusNum + '" />' +
        '					</td>' +
        '					<td style="text-align:center;" onclick="fnSeatDel(this, ' + returnSeat + ');"><img src="/act_2023/images/button/close.png" style="width:18px;vertical-align:middle;" /></td>' +
        '				</tr>';
    if (tbCnt == 0) {
        insHtml += '			</tbody>' +
            '		</table>';
    }

    $j(bindObj).append(insHtml);

    var forObj = $j("select[id=startLocation" + returnBusType + "]");
    for (var i = 0; i < forObj.length; i++) {
        var arrBus = busResData[returnBusNum + "_" + forObj.eq(i).attr("seatnum")].split("/");

        forObj.eq(i).val(arrBus[2]).change();
        forObj.eq(i).next().val(arrBus[3]);
    }
}
