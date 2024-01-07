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

var rtnBusDate = function(day, getDay, json, bus) {
    var holiday = holidays[$j.datepicker.formatDate("mmdd", day)];
    var thisYear = $j.datepicker.formatDate("yy", day);

    if (json != "init") {
        var onoffDay = json[bus + ((day.getMonth() + 1) + 100).toString().substring(1, 3) + (day.getDate() + 100).toString().toString().substring(1, 3)];
    }

    var cssRes = "";
    if (holiday) {
        if (thisYear == holiday.year || holiday.year == "") {
            cssRes = "date-sunday";
        }
    }

    var result;
    if (getDay == 0) {
        cssRes = "date-sunday";
    } else if (getDay == 6) {
        cssRes = "date-saturday";
    } else {
        cssRes = "";
    }

    if (json == "init") {
        result = [true, cssRes];
    } else {
        if (onoffDay) {
            result = [true, cssRes];
        } else {
            result = [false, cssRes];
        }
    }

    return result;
}


var selDate;
var busNum;
var busNumName;
var busType;
//노선 클릭시 정류장 및 좌석 바인딩
function fnPointList(busnum, busseat, obj) {
    //$j('.busSeat').unblock(); 
    busnum = busnum.substring(0, 2);
    if ($j("#daytype").val() == 0) { //편도
        $j("ul[class=busLine]:eq(0) li").removeClass("on");
        $j(obj).addClass("on");

        $j("ul[id=buspointlist]").eq(0).css("display", "block");
        $j("li[id=buspointtext]").eq(0).html(busPointList[busnum].li);
    } else {
        if (busnum.substring(0, 1) == "Y" || busnum.substring(0, 1) == "E") {
            $j("ul[class=busLine]:eq(1) li").removeClass("on");
            $j(obj).addClass("on");

            $j("ul[id=buspointlist]").eq(1).css("display", "block");
            $j("li[id=buspointtext]").eq(1).html(busPointList[busnum].li);
        } else {
            $j("ul[class=busLine]:eq(2) li").removeClass("on");
            $j(obj).addClass("on");

            $j("ul[id=buspointlist]").eq(2).css("display", "block");
            $j("li[id=buspointtext]").eq(2).html(busPointList[busnum].li);
        }
    }
}

function fnBusSeatInit(busnum, busseat, obj, busname) {
    $j(".busLineTab li").removeClass("on");
    $j(".busLineTab2 li").removeClass("on");
    $j(obj).addClass("on");

    var objClass1 = ".busLineTab";
    var objClass2 = ".busLineTab2";
    if($j(obj).parent().attr("class") == "busLineTab2"){
        objClass1 = ".busLineTab2";
        objClass2 = ".busLineTab";
    }

    if($j(objClass1 + " li").eq(0).hasClass("on")){
        $j(objClass2 + " li").eq(0).addClass("on");
    }

    if($j(objClass1 + " li").eq(1).hasClass("on")){
        $j(objClass2 + " li").eq(1).addClass("on");
    }

    var busSeatLast = "";
    if (busseat == 44) {
        busSeatLast = '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="41"><br>41</td>' +
            '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="42"><br>42</td>' +
            '<td>&nbsp;</td>' +
            '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="43"><br>43</td>' +
            '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="44"><br>44</td>';
    } else {
        busSeatLast = '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="41"><br>41</td>' +
            '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="42"><br>42</td>' +
            '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="43"><br>43</td>' +
            '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="44"><br>44</td>' +
            '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="45"><br>45</td>';
    }

    $j("#busSeatLast").html(busSeatLast);

    selDate = $j(obj).attr("caldate"); //$j("#SurfBus").val();
    busNum = busnum;
    busType = busnum.substring(0, 1);
    busNumName = busname;
    
    if (typeof dayCode === 'undefined') {
        dayCode = "busseat";
    }

    var seatjson = [];
    var objParam = {
        "code": dayCode,
        "busDate": selDate,
        "busNum": busnum
    }
    $j.getJSON("/act_2023/front/bus/view_bus_day.php", objParam,
        function(data, textStatus, jqXHR) {
            seatjson = data;
        }
    );

    $j("#tbSeat .busSeatList").addClass("busSeatListN").removeClass("busSeatListY").removeClass("busSeatListC");
    seatjson.forEach(function(el) {
        if (el.seatYN == "Y") {
            $j("#tbSeat .busSeatList[busSeat=" + el.seatnum + "]").removeClass("busSeatListN").addClass("busSeatListY");
        } else if (el.seatYN == "N" && (busrestype == "change" || busrestype == "seatview")) {
            if (busResData[busNum + "_" + el.seatnum] != null) {
                $j("#tbSeat .busSeatList[busSeat=" + el.seatnum + "]").removeClass("busSeatListN").addClass("busSeatListY");
                if (businit == 1) {} else {
                    $j("#tbSeat .busSeatList[busSeat=" + el.seatnum + "]").click();
                }
            }
        }
    });

    if ($j("#tb" + selDate + '_' + busnum).length > 0) {
        var forObj = $j("#tb" + selDate + '_' + busnum + ' [id=hidbusSeat' + busType + ']');
        for (var i = 0; i < forObj.length; i++) {
            $j("#tbSeat .busSeatList[busSeat=" + forObj.eq(i).val() + "]").removeClass("busSeatListY").addClass("busSeatListC");
        }
    }

    if ((busrestype == "change" || busrestype == "seatview") && businit == 0) {
        if ($j("#daytype").val() == 1) { //왕복
            for (key in busResData) {
                var arrVlu = busResData[key].split("/");
                if (arrVlu[0].substring(0, 1) == "S" || arrVlu[0].substring(0, 1) == "A") {
                    fnSeatChangeSelected(busResData[key]);
                }
            }
        }

        var forObj = $j("select[id=startLocation" + busType + "]"); //$j("#tb" + selDate + '_' + busnum + ' select');
        for (var i = 0; i < forObj.length; i++) {
            var arrBus = busResData[busNum + "_" + forObj.eq(i).attr("seatnum")].split("/");

            forObj.eq(i).val(arrBus[2]).change();
            forObj.eq(i).next().val(arrBus[3]);
        }
    }

    businit = 1;
}


function fnBusNext() {
    $j(".busLineTab li").remove();

    $j(".selectStop li").css("display", "none");
    $j("#selBusY").html("");
    $j("#selBusE").html("");
    $j("#selBusS").html("");
    $j("#selBusA").html("");

    if ($j("#daytype").val() == 0) { //편도
        if ($j("#SurfBus").val() == "") {
            alert("이용일을 선택해주세요.");
            return;
        }

        var busstop = $j("ul[class=busLine]:eq(0) li[class=on]").length;
        if (busstop == 0) {
            alert("노선을 선택해주세요.");
            return;
        }

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
        if ($j("#SurfBusS").val() == "") {
            alert("출발일을 선택해주세요.");
            return;
        }
        var busstop = $j("ul[class=busLine]:eq(1) li[class=on]").length;
        if (busstop == 0) {
            alert("출발노선을 선택해주세요.\n\n모든 노선이 매진된 경우 편도로 예약해주세요~");
            return;
        }

        if ($j("#SurfBusE").val() == "") {
            alert("복귀일을 선택해주세요.");
            return;
        }
        var busstop = $j("ul[class=busLine]:eq(2) li[class=on]").length;
        if (busstop == 0) {
            alert("복귀노선을 선택해주세요.\n\n모든 노선이 매진된 경우 편도로 예약해주세요~");
            return;
        }

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

    $j('#resStep1').block({ focusInput: false, message: null });
    // if($j("#resseatnum").text() == ""){
    //     $j('#resStep1').block({ message: null });
    // }else{
    //     $j('#resStep1').block({ 
    //         message: $j("#resseatnum").html(),
    //         focusInput: false,
    //         css: { width: '50%', textAlign: 'center', left: '15%', top: '32%' }
    //     });        
    // }

    $j(".busOption02").css("display", "");
    $j('#divConfirm').css("display", "");
    $j("#seatTab").css("display", "");

    fnMapView("#seatTab", 80);
}

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

function fnBusPrev(num) {
    if (num == 0) {
        if (!confirm("선택하신 좌석 및 정류장 정보가 초기화됩니다.\n\n이전단계로 돌아가시겠습니까?")) {
            return;
        }
    }

    $j('#resStep1').unblock();

    $j(".busOption02").css("display", "none");
    $j('#divConfirm').css("display", "none")
    $j("#seatTab").css("display", "none");

    fnMapView('#view_tab3', 70);
}

//버스 좌석 선택시 컨트롤
function fnSeatSelected(obj) {    
    if ($j(obj).hasClass("busSeatListN")) return;

    var objVlu = $j(obj).attr("busSeat");
    if ($j(obj).hasClass("busSeatListC")) {
        if (busrestype == "seatview") { //내좌석보기
            return;
        }

        $j(obj).addClass("busSeatListY").removeClass("busSeatListC");

        if ($j("#" + selDate + '_' + busNum + ' tr').length == 2) {
            $j("#tb" + selDate + '_' + busNum).remove();
        } else {
            $j("#" + selDate + '_' + busNum + '_' + objVlu).remove();
        }
    } else {
        if (busrestype == "change" || busrestype == "seatview") {
            if ($j("#daytype").val() == 0) { //편도
                var defaultCnt = Object.keys(busResData).length;
                var selCnt = $j("tr[trseat]").length + 1; //$j("select[id=startLocation" + busType + "]").length + 1;
                if (defaultCnt < selCnt) {
                    if (busrestype == "seatview") { //내좌석보기
                        return;
                    }
                    alert("선택된 좌석을 취소 후 해당 좌석을 선택해주세요~");
                    return;
                }
            } else { //왕복
                var defaultCntS = 0,
                    defaultCntE = 0;

                //기본 양양행 왕복
                var btntextS = ((busTypeY == "Y") ? "양양행" : "동해행"),
                    btntextE = "서울행";
                var selCntS = $j("#selBus" + busTypeY + " tr[trseat]").length + 1;
                var selCntE = $j("#selBus" + busTypeS + " tr[trseat]").length + 1;

                for (key in busResData) {
                    var arrVlu = busResData[key].split("/");
                    if (arrVlu[0].substring(0, 1) == "S" || arrVlu[0].substring(0, 1) == "A") { //서울행
                        defaultCntE++;
                    } else { //양양,동해행
                        defaultCntS++;
                    }
                }

                if (busType == "E" || busType == "Y") {
                    if (defaultCntS < selCntS) {
                        if (busrestype == "seatview") { //내좌석보기
                            return;
                        }
                        alert(btntextS + "으로 선택된 좌석을 취소 후 해당 좌석을 선택해주세요~");
                        return;
                    }
                } else {
                    if (defaultCntE < selCntE) {
                        if (busrestype == "seatview") { //내좌석보기
                            return;
                        }
                        alert(btntextE + "으로 선택된 좌석을 취소 후 해당 좌석을 선택해주세요~");
                        return;
                    }
                }
            }
        } else if (busrestype == "channel") { //타채널 예약건
            var selCntS = $j("#selBus" + busTypeY + " tr[trseat]").length + 1;
            var selCntE = $j("#selBus" + busTypeS + " tr[trseat]").length + 1;
            
            if (busType == "E" || busType == "Y") {
                if (resbusseat1 < selCntS) {
                    alert(((busTypeY == "Y") ? "양양행" : "동해행") + "은 " + resbusseat1 + "좌석까지 예약 가능합니다.");
                    return;
                }
            } else {
                if (resbusseat2 < selCntE) {
                    alert("서울행은 " + resbusseat2 + "좌석까지 예약 가능합니다.");
                    return;
                }
            }
        }

        $j(obj).addClass("busSeatListC").removeClass("busSeatListY");

        var sPoint = "";
        var ePoint = "";

        var busPointCode = busNum.substring(0, 2);
        var arrObjs = eval("busPoint.sPoint" + busPointCode);
        var arrObje = eval("busPoint.ePoint" + busType + "end");

        var selVlu = "";
        if(buschannel == 17 || buschannel == 26){ //마린서프
            selVlu = "기사문해변";
        }else if(buschannel == 20 || buschannel == 24 || buschannel == 27){ //인구서프, 엉클 프립
            selVlu = "인구해변";
        }else if(buschannel == 21 || buschannel == 28){ //서프팩토리
            selVlu = "대진해변";
        }else if(buschannel == 22 || buschannel == 29){ //솔게하
            selVlu = "솔.동해점";
        }else if(buschannel == 23 || buschannel == 25){ //브라보서프, 금진 프립
            selVlu = "금진해변";
        }
        
        arrObjs.forEach(function(el) {
            if(busType == "E" || busType == "Y"){
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
            if(busType == "E" || busType == "Y"){
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

        var tbCnt = $j("#tb" + selDate + '_' + busNum).length;
        var insHtml = "";
        var bindObj = "#" + selDate + '_' + busNum;
        if (tbCnt == 0) {
            insHtml = '		<table class="et_vars exForm bd_tb " style="width:100%;margin-bottom:5px;" id="tb' + selDate + '_' + busNum + '">' +
                '			<colgroup>' +
                '				<col style="width:45px;">' +
                '				<col style="width:auto;">' +
                '				<col style="width:38px;">' +
                '			</colgroup>' +
                '			<tbody id="' + selDate + '_' + busNum + '">' +
                '				<tr>' +
                '					<th colspan="3">[' + selDate + '] ' + busNumName +
                '					</th>' +
                '				</tr>';
            bindObj = "#selBus" + busType;
        }

        insHtml += '				<tr id="' + selDate + '_' + busNum + '_' + objVlu + '" trseat="' + objVlu + '">' +
            '					<th style="padding:4px 6px;text-align:center;">' + objVlu + '번</th>' +
            '					<td style="line-height:2;">' +
            '						<select id="startLocation' + busType + '" seatnum="' + objVlu + '" name="startLocation' + busType + '[]" class="select" onchange="fnBusTime(this, \'' + busNum + '\', -1);">' +
            '							' + sPoint +
            '						</select> →' +
            '						<select id="endLocation' + busType + '" seatnum="' + objVlu + '" name="endLocation' + busType + '[]" class="select">' +
            '							' + ePoint +
            '						</select><br>' +
            '						<span id="stopLocation"></span>' +
            '						<input type="hidden" id="hidbusSeat' + busType + '" name="hidbusSeat' + busType + '[]" value="' + objVlu + '" />' +
            '						<input type="hidden" id="hidbusDate' + busType + '" name="hidbusDate' + busType + '[]" value="' + selDate + '" />' +
            '						<input type="hidden" id="hidbusNum' + busType + '" name="hidbusNum' + busType + '[]" value="' + busNum + '" />' +
            '					</td>' +
            '					<td style="text-align:center;" onclick="fnSeatDel(this, ' + objVlu + ');"><img src="/act_2023/images/button/close.png" style="width:18px;vertical-align:middle;" /></td>' +
            '				</tr>';
        if (tbCnt == 0) {
            insHtml += '			</tbody>' +
                '		</table>';
        }

        $j(bindObj).append(insHtml);
    }

    if (busrestype == "change" || busrestype == "seatview") {
        //2021-02-21_S22_37
        //$j("#" + $j("#SurfBus").val()  + "_" + busNum + "_" + el.seatnum)
    } else {
        fnPriceSum('', 1);
    }
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

//서핑버스 좌석선택 삭제
function fnSeatDel(obj, num) {
    var arrId = $j(obj).parents('tbody').attr('id').split('_');
    if (selDate == arrId[0] && busNum == arrId[1]) {
        $j("#tbSeat .busSeatList[busSeat=" + num + "]").removeClass("busSeatListC").addClass("busSeatListY");
    }

    if ($j(obj).parents('tbody').find('tr').length == 2) {
        $j(obj).parents('table').remove();
    } else {
        $j(obj).parents('tr').remove();
    }

    fnPriceSum('', 1);
}

function fnPriceSum(obj, num) {
    var cnt = $j("input[id=hidbusSeat" + busTypeY + "]").length + $j("input[id=hidbusSeat" + busTypeS + "]").length;

    if (cnt == 0) return;
    $j("#lastcouponprice").html("");
    if ($j("#couponcode").val() == "" || $j("#couponprice").val() == 0) {
        $j("#lastPrice").html(commify(cnt * 20000) + "원");
    } else {
        var cp = $j("#couponprice").val();
        if (cp <= 100) { //퍼센트 할인			
            cp = (1 - (cp / 100));
            $j("#lastPrice").html(commify((cnt * 20000) * cp) + "원");
            $j("#lastcouponprice").html(" (" + commify(cnt * 20000) + "원 - 할인쿠폰:" + commify((cnt * 20000) - ((cnt * 20000) * cp)) + "원)");
        } else { //금액할인
            $j("#lastPrice").html(commify((cnt * 20000) - cp) + "원");
            $j("#lastcouponprice").html(" (" + commify(cnt * 20000) + "원 - 할인쿠폰:" + commify(cp) + "원)");
        }
    }
}

function fnBusSave() {
    var chkVluY = $j("input[id=hidbusSeat" + busTypeY + "]").map(function() { return $j(this).val(); }).get();
    var chkVluS = $j("input[id=hidbusSeat" + busTypeS + "]").map(function() { return $j(this).val(); }).get();

    var chksLocationY = $j("select[id=startLocation" + busTypeY + "]").map(function() { return $j(this).val(); }).get();
    var chkeLocationY = $j("select[id=endLocation" + busTypeY + "]").map(function() { return $j(this).val(); }).get();
    var chksLocationS = $j("select[id=startLocation" + busTypeS + "]").map(function() { return $j(this).val(); }).get();
    var chkeLocationS = $j("select[id=endLocation" + busTypeS + "]").map(function() { return $j(this).val(); }).get();

    if ($j("#daytype").val() == 0) { //편도
        if (chkVluY == "" && chkVluS == "") {
            alert("셔틀버스 좌석을 선택해 주세요.");

            fnMapView("#seatTab", 80);
            return;
        }
    } else {
        if (chkVluY == "") {
            alert("셔틀버스 출발 좌석을 선택해 주세요.");

            fnMapView("#seatTab", 80);
            return;
        }

        if (chkVluS == "") {
            alert("셔틀버스 복귀 좌석을 선택해 주세요.");

            fnMapView("#seatTab", 80);
            return;
        }
    }

    if (chksLocationY.indexOf('N') != -1 || chkeLocationY.indexOf('N') != -1) {
        alert('셔틀버스 정류장을 선택해주세요.');
        return;
    }
    if (chksLocationS.indexOf('N') != -1 || chkeLocationS.indexOf('N') != -1) {
        alert('셔틀버스  정류장을 선택해주세요.');
        return;
    }

    var submiturl = "/act_2023/front/bus/view_bus_save.php";
    if (busrestype == "change") {
        if ($j("#daytype").val() == 0) { //편도
            var defaultCnt = Object.keys(busResData).length;
            var selCnt = $j("tr[trseat]").length;
            if (defaultCnt != selCnt) {
                alert("예약된 좌석수(" + defaultCnt + "자리)와 동일한 개수로 선택해주세요~");
                return;
            }
        } else { //왕복
            var defaultCntS = 0,
                defaultCntE = 0;

            //기본 양양행 왕복
            var btntextS = ((busTypeY == "Y") ? "양양행" : "동해행"),
                btntextE = "서울행";
            var selCntS = $j("#selBus" + busTypeY + " tr[trseat]").length;
            var selCntE = $j("#selBus" + busTypeS + " tr[trseat]").length;

            for (key in busResData) {
                var arrVlu = busResData[key].split("/");
                if (arrVlu[0].substring(0, 1) == "S" || arrVlu[0].substring(0, 1) == "A") { //서울행
                    defaultCntE++;
                } else { //양양,동해행
                    defaultCntS++;
                }
            }

            if (defaultCntS != selCntS) {
                alert(btntextS + "으로 예약된 좌석수(" + defaultCntS + "자리)와 동일한 개수로 선택해주세요~");
                return;
            }

            if (defaultCntE != selCntE) {
                alert(btntextE + "으로 예약된 좌석수(" + defaultCntE + "자리)와 동일한 개수로 선택해주세요~");
                return;
            }
        }

        submiturl = "/act_2023/front/order/order_return.php";

        if (!confirm("액트립 셔틀버스 예약건을 수정하시겠습니까?")) {
            return;
        }
    } else {
        if (busrestype == "channel") {
            var selCntS = $j("#selBus" + busTypeY + " tr[trseat]").length;
            var selCntE = $j("#selBus" + busTypeS + " tr[trseat]").length;

            if (resbusseat1 > 0 && resbusseat1 != selCntS) {
                alert(((busTypeY == "Y") ? "양양행" : "동해행") + "은 " + resbusseat1 + "좌석 예약해주세요~");
                return;
            }

            if (resbusseat2 > 0 && resbusseat2 != selCntE) {
                alert("서울행은 " + resbusseat2 + "좌석 예약해주세요");
                return;
            }
        }

        if ($j("#userName").val() == "") {
            alert("이름을 입력하세요.");
            return;
        }

        if ($j("#userPhone1").val() == "" || $j("#userPhone2").val() == "" || $j("#userPhone3").val() == "") {
            alert("연락처를 입력하세요.");
            return;
        }

        if (!$j("#chk8").is(':checked')) {
            alert("이용안내 및 취소/환불 규정에 대한 동의를 해주세요.");
            return;
        }

        if (!$j("#chk9").is(':checked')) {
            alert("개인정보 취급방침에 동의를 해주세요.");
            return;
        }

        if (!confirm("액트립 셔틀버스를 예약하시겠습니까?")) {
            return;
        }
    }

    $j('#divConfirm').block({ message: "신청하신 예약건 진행 중입니다." });

    setTimeout('$j("#frmRes").attr("action", "' + submiturl + '").submit();', 500);
}

function fnUnblock(objId) {
    $j(objId).unblock();
}

function fnCouponCheck(obj) {
    var cp = fnCoupon("BUS", "load", $j("#coupon").val());
    if (cp > 0) {
        $j("#coupondis").css("display", "");
        $j("#couponcode").val($j("#coupon").val())
        $j("#couponprice").val(cp);

        if (cp <= 100) { //퍼센트 할인
            $j("#coupondis").html("<br>적용쿠폰코드 : " + $j("#coupon").val() + "<br>총 결제금액에서 " + cp + "% 할인");
            if(cp == 100){
                $j("#coupondis").closest("tr").hide();
            }
        } else { //금액할인
            $j("#coupondis").html("<br>적용쿠폰코드 : " + $j("#coupon").val() + "<br>총 결제금액에서 " + commify(cp) + "원 할인");
        }
    } else {
        $j("#coupondis").css("display", "none");
        $j("#coupondis").html("");
        $j("#couponcode").val("")
        $j("#couponprice").val(0);
    }
    $j("#coupon").val("");

    fnPriceSum('', 1);
}
