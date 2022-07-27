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
    $j(obj).addClass("on");

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
    var seatjson = [];
    var objParam = {
        "code": "frip_busseat",
        "busDate": selDate,
        "busNum": busnum,
        "seq": $j("#shopseq").val()
    }
    $j.getJSON("/act/surf/surfbus_day.php", objParam,
        function(data, textStatus, jqXHR) {
            seatjson = data;
        }
    );

    $j("#tbSeat .busSeatList").addClass("busSeatListN").removeClass("busSeatListY").removeClass("busSeatListC");
    seatjson.forEach(function(el) {
        if (el.seatYN == "Y") {
            $j("#tbSeat .busSeatList[busSeat=" + el.seatnum + "]").removeClass("busSeatListN").addClass("busSeatListY");
        } else if (el.seatYN == "N" && busrestype == "change") {
            if (busResData[busNum + "_" + el.seatnum] != null) {
                $j("#tbSeat .busSeatList[busSeat=" + el.seatnum + "]").removeClass("busSeatListN").addClass("busSeatListY");
                if (businit == 1) {} else {
                    $j("#tbSeat .busSeatList[busSeat=" + el.seatnum + "]").click();
                }
                // if($j("#" + selDate + "_" + busNum + "_" + el.seatnum).length == 0){
                // 	$j("#tbSeat .busSeatList[busSeat=" + el.seatnum + "]").removeClass("busSeatListN").addClass("busSeatListY");
                // 	// $j("#" + $j("#SurfBus").val()  + "_" + busNum + "_" + el.seatnum).remove();
                // 	$j("#tbSeat .busSeatList[busSeat=" + el.seatnum + "]").click();
                // 	//$j("#tbSeat .busSeatList[busSeat=" + el.seatnum + "]").removeClass("busSeatListN").addClass("busSeatListC");
                // 	//$j("#tbSeat .busSeatList[busSeat=" + el.seatnum + "]").removeAttr("onclick");
                // }else{
                // 	$j("#tbSeat .busSeatList[busSeat=" + el.seatnum + "]").removeClass("busSeatListN").addClass("busSeatListC");
                // }
            }
        }
    });

    if ($j("#tb" + selDate + '_' + busnum).length > 0) {
        var forObj = $j("#tb" + selDate + '_' + busnum + ' [id=hidbusSeat' + busType + ']');
        for (var i = 0; i < forObj.length; i++) {
            $j("#tbSeat .busSeatList[busSeat=" + forObj.eq(i).val() + "]").removeClass("busSeatListY").addClass("busSeatListC");
        }
    }

    if (busrestype == "change" && businit == 0) {
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

//달력 날짜 선택시 노선 바인딩
function fnBusSearchDate(selectedDate, gubun, objid) {
    //$j("#buspointlist").css("display", "none");
    //$j("#busnotdate").css("display", "none");
    var eqnum = 0;
    if (objid == "SurfBusS") {
        $j("ul[class=busLine]:eq(1) li").remove();
        $j("ul[class=busLine]").eq(1).css("display", "block").append('<li><img src="act/images/viewicon/bus.svg" alt="">출발</li>');
        eqnum = 1;
    } else if (objid == "SurfBusE") {
        $j("ul[class=busLine]:eq(2) li").remove();
        $j("ul[class=busLine]").eq(2).css("display", "block").append('<li><img src="act/images/viewicon/bus.svg" alt="">복귀</li>');
        eqnum = 2;
    } else {
        $j("ul[class=busLine]:eq(0) li").remove();
        $j("ul[class=busLine]").eq(0).css("display", "block").append('<li><img src="act/images/viewicon/bus.svg" alt="">노선</li>');
    }

    if (objid == "SurfBusE") {
        gubun = fnBusDateGubun(gubun, "SurfBusE");
    }
    var arrData = busData[gubun + selectedDate.substring(5).replace('-', '')];
    arrData.forEach(function(el) {
        var objParam = {
            "code": "frip_seatcnt",
            "busDate": selectedDate,
            "busNum": el.busnum,
            "seq": $j("#shopseq").val()
        }
        $j.getJSON("/act/surf/surfbus_day.php", objParam,
            function(data, textStatus, jqXHR) {
                el.busname = el.busname.replace("서울출발 ", "").replace("서울복귀 ", "");
                if (data[0].seatcnt == el.busseat) {
                    if (busrestype == "change") {
                        $j("ul[class=busLine]").eq(eqnum).append('<li onclick="fnPointList(\'' + el.busnum + '\', ' + el.busseat + ', this);" busnum="' + el.busnum + '" style="cursor:pointer;text-decoration:line-through;">' + el.busname + '</li>');
                    } else {
                        $j("ul[class=busLine]").eq(eqnum).append('<li onclick="alert(\'선택하신 [' + el.busname + ']는 좌석이 매진되었습니다.\\n\\n취소 좌석이 발생할 경우 예매가능합니다.\');" style="cursor:pointer;text-decoration:line-through;">' + el.busname + '</li>');
                    }
                } else {
                    $j("ul[class=busLine]").eq(eqnum).append('<li onclick="fnPointList(\'' + el.busnum + '\', ' + el.busseat + ', this);" busnum="' + el.busnum + '" style="cursor:pointer;">' + el.busname + '</li>');
                }
            }
        );

    });

    //$j(".busLine li").eq(1).click();
}

function fnBusDateGubun(gubun, objid) {
    if (objid == "SurfBusE") {
        if (gubun == "Y") {
            gubun = "S";
        } else {
            gubun = "A";
        }
    }
    return gubun;
}

jQuery(function() {
    jQuery('input[cal=busdate]').datepicker({
        minDate: new Date((new Date()).getFullYear() + '-01-01'),
        maxDate: new Date((new Date()).getFullYear() + '-12-31'),
        onSelect: function(selectedDate) {
            fnBusSearchDate(selectedDate, $j(this).attr("gubun"), $j(this).attr("id"));

            
            //달력컨트롤 : 출발노선 체크
            if($j("#nextchk").val() == "Y"){
                var arrDataS = 0;
                var arrDataE = 0;
                //편도
                if($j("#daytype").val() == "0"){
                    //서울출발 
                    if($j("#busgubun").val() == "Y"){

                    }else{ //서울 복귀

                    }
                    var arrDataS = busData[$j("#busgubun").val() + $j("#SurfBus").val().substring(5).replace('-', '')];
                    if(arrDataS.length == 1){
                        $j("li[busnum=" + arrDataS[0].busnum + "]").click();
                    }

                    if(arrDataS.length == 1){
                        //fnBusNext();
                    }
                }else{ //왕복

                    if($j("#SurfBusS").val() != ""){
                        //양양행 1대일경우
                        var gubun = fnBusDateGubun($j("#SurfBusS").attr("gubun"), "SurfBusS");
                        arrDataS = busData[gubun + $j("#SurfBusS").val().substring(5).replace('-', '')];
                        if(arrDataS.length == 1){
                            $j("li[busnum=" + arrDataS[0].busnum + "]").click();
                        }
                    }

                    if($j("#SurfBusE").val() != ""){
                        //서울행 1대일경우
                        var gubun = fnBusDateGubun($j("#SurfBusE").attr("gubun"), "SurfBusE");
                        arrDataE = busData[gubun + $j("#SurfBusE").val().substring(5).replace('-', '')];
                        if(arrDataE.length == 1){
                            $j("li[busnum=" + arrDataE[0].busnum + "]").click();
                        }
                    }

                    if(arrDataS.length == 1 && arrDataE.length == 1){
                        //fnBusNext();
                    }
                }
            }
        },
        beforeShowDay: function(date) {
            var gubun = fnBusDateGubun($j(this).attr("gubun"), $j(this).attr("id"));
            return rtnBusDate(date, date.getDay(), busData, gubun);
        }
    });

    jQuery('input[cal=sdate]').datepicker({
        beforeShow: function(date) {
            var date = jQuery(this).next().datepicker('getDate');

            if (!(date == null)) {
                date.setDate(date.getDate()); // Add 7 days
                jQuery(this).datepicker("option", "maxDate", date);
            }
        },
        onClose: function(selectedDate) {
            // 시작일(fromDate) datepicker가 닫힐때
            // 종료일(toDate)의 선택할수있는 최소 날짜(minDate)를 선택한 시작일로 지정 
            var date = jQuery(this).datepicker('getDate');
            if (!(date == null)) {
                date.setDate(date.getDate()); // Add 7 days
                jQuery(this).next().datepicker("option", "minDate", date);
            }
        }
    });


    jQuery('input[cal=edate]').datepicker({
        beforeShow: function(date) {
            var date = jQuery(this).prev().datepicker('getDate');

            if (!(date == null)) {
                date.setDate(date.getDate()); // Add 7 days
                jQuery(this).datepicker("option", "minDate", date);
            }
        },
        onClose: function(selectedDate) {

            // 시작일(fromDate) datepicker가 닫힐때
            // 종료일(toDate)의 선택할수있는 최소 날짜(minDate)를 선택한 시작일로 지정 
            var date = jQuery(this).datepicker('getDate');

            if (!(date == null)) {
                date.setDate(date.getDate()); // Add 7 days
                jQuery(this).prev().datepicker("option", "maxDate", date);
            }
        }
    });

    jQuery('input[cal=date]').datepicker({});
});

jQuery(function($) {
    $.datepicker.regional['ko'] = {
        closeText: '닫기',
        prevText: '이전달',
        nextText: '다음달',
        currentText: '오늘',
        monthNames: ['1월(JAN)', '2월(FEB)', '3월(MAR)', '4월(APR)', '5월(MAY)', '6월(JUN)', '7월(JUL)', '8월(AUG)', '9월(SEP)', '10월(OCT)', '11월(NOV)', '12월(DEC)'],
        monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
        dayNames: ['일', '월', '화', '수', '목', '금', '토'],
        dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
        dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
        weekHeader: 'Wk',
        dateFormat: 'yy-mm-dd',
        firstDay: 0,
        isRTL: false,
        yearSuffix: '년',
        showMonthAfterYear: true,
        /* 연도를 월보다 앞에 표시 */
        changeYear: false,
        /* 연도 수정 가능 */
        changeMonth: false /* 월 수정 가능 */ ,
        showOn: "focus",
        /* focus/button/both 달력이 나타나는 이벤트 */
        showOtherMonths: false,
        /* 이전/다음달의 여분 날짜 보이기 */
        selectOtherMonths: false,
        /* 이전/다음달의 여분 날짜 선택 유무 */
        beforeShowDay: function(day) { //공휴일 설정
            var result;
            var holiday = holidays[$.datepicker.formatDate("mmdd", day)];
            var thisYear = $.datepicker.formatDate("yy", day);

            if (holiday) {
                if (thisYear == holiday.year || holiday.year == "") {
                    result = [true, "date-sunday", holiday.title];
                } else {
                    result = [true, ""];
                }
            } else {
                switch (day.getDay()) {
                    case 0: // is sunday?
                        result = [true, "date-sunday"];
                        break;
                    case 6: // is saturday?
                        result = [true, "date-saturday"];
                        break;
                    default:
                        result = [true, ""];
                        break;
                }
            }
            return result;
        }
    };
    $.datepicker.setDefaults($.datepicker.regional['ko']);
});

//일정 클릭시 행선지 영역 활성화
function fnBusDayType(gubun, obj) {
    $j("#ulDaytype li").removeClass("on");
    $j(obj).addClass("on");

    $j("#daytype").val(gubun);
    $j("#SurfBus").val("");
    $j("#SurfBusS").val("");
    $j("#SurfBusE").val("");
    $j("ul[id=buspointlist]").css("display", "none");

    if (gubun == 0) { //편도
        $j("#route").css("display", "");
        $j("#busdate").css("display", "");
        $j("#sbusdate").css("display", "none");
        $j("#ebusdate").css("display", "none");

        $j("ul[class=busLine] li").remove();
        $j("ul[class=busLine]").eq(0).css("display", "block").append('<li><img src="act/images/viewicon/bus.svg" alt="">노선</li>');
    } else { //왕복
        $j("#route").css("display", "none");
        $j("#busdate").css("display", "none");
        $j("#sbusdate").css("display", "");
        $j("#ebusdate").css("display", "");

        $j("ul[class=busLine] li").remove();
        $j("ul[class=busLine]").eq(1).css("display", "block").append('<li><img src="act/images/viewicon/bus.svg" alt="">출발노선</li>');
        $j("ul[class=busLine]").eq(2).css("display", "block").append('<li><img src="act/images/viewicon/bus.svg" alt="">복귀노선</li>');
    }
}

//행선지 클릭시 이용일 영역 활성화
function fnBusGubun(gubun, obj, type) {
    $j("#ulroute li").removeClass("on");
    $j(obj).addClass("on");

    $j("ul[class=busLine] li").remove();
    $j("ul[class=busLine]").eq(0).css("display", "block").append('<li><img src="act/images/viewicon/bus.svg" alt="">노선</li>');

    $j("ul[id=buspointlist]").css("display", "none");

    if (type == "change") { //정류장 변경
        fnBusSearchDate($j("#SurfBus").val(), $j("#SurfBus").attr("gubun"), $j("#SurfBus").attr("id"));
    } else {
        $j("#SurfBus").val("").attr("gubun", gubun.substring(0, 1));
    }
    $j("#busgubun").val(gubun);
    //$j("#tbSeat .busSeatList").addClass("busSeatListN").removeClass("busSeatListY").removeClass("busSeatListC");

    // if($j("#busnotdate").css("display") == "none"){
    // 	$j("#busnotdate").css("display", "block");
    // 	$j(".busLine").css("display", "none");
    // 	$j("#buspointlist").css("display", "none");
    // }
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
            btntext = "[출발] ";
            $j(".selectStop li").eq(0).css("display", "");
            $j(".selectStop li").eq(1).css("display", "");
        } else if ($j("#busgubun").val() == "E") {
            btntext = "[동해행] ";
            $j(".selectStop li").eq(0).css("display", "");
            $j(".selectStop li").eq(1).css("display", "");
        } else {
            btntext = "[복귀] ";
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
            btntext0 = "출발 ";
            btntext1 = "복귀 ";
        }

        btntext0 = "출발 ",
        btntext1 = "복귀 ";
        var btnonclick = $j("ul[class=busLine]:eq(1) li[class=on]").attr("onclick");
        var busname = $j("ul[class=busLine]:eq(1) li[class=on]").text();
        $j(".busLineTab").append('<li class="on" caldate="' + $j("#SurfBusS").val() + '" style="cursor:pointer;" onclick="' + btnonclick.replace("fnPointList", "fnBusSeatInit").replace("this", "this, '" + busname + "'") + '">' + btntext0 + $j("ul[class=busLine]:eq(1) li[class=on]").text() + '</li>');

        btnonclick = $j("ul[class=busLine]:eq(2) li[class=on]").attr("onclick");
        busname = $j("ul[class=busLine]:eq(2) li[class=on]").text();
        $j(".busLineTab").append('<li caldate="' + $j("#SurfBusE").val() + '" style="cursor:pointer;" onclick="' + btnonclick.replace("fnPointList", "fnBusSeatInit").replace("this", "this, '" + busname + "'") + '">' + btntext1 + $j("ul[class=busLine]:eq(2) li[class=on]").text() + '</li>');
    }

    $j(".busLineTab li").eq(0).click();

    $j('#resStep1').block({ message: null });

    $j(".busOption02").css("display", "");
    $j('#divConfirm').css("display", "");
    $j("#seatTab").css("display", "");

    fnMapView("#seatTab", 80);
}

function fnBusChangeNext() {
    if ($j("#daytype").val() == 0) { //편도
        // if($j("#SurfBus").val() == ""){
        // 	alert("이용일을 선택해주세요.");
        // 	return;
        // }

        // var busstop = $j("ul[class=busLine]:eq(0) li[class=on]").length;
        // if(busstop == 0){
        // 	alert("노선을 선택해주세요.");
        // 	return;
        // }

        var btnonclick = $j("ul[class=busLine]:eq(0) li[class=on]").attr("onclick");
        var btntext = "";
        if ($j("#busgubun").val() == "Y") {
            btntext = "출발 ";
            $j(".selectStop li").eq(0).css("display", "");
            $j(".selectStop li").eq(1).css("display", "");
        } else if ($j("#busgubun").val() == "E") {
            btntext = "[동해행] ";
            $j(".selectStop li").eq(0).css("display", "");
            $j(".selectStop li").eq(1).css("display", "");
        } else {
            btntext = "복귀 ";
            $j(".selectStop li").eq(2).css("display", "");
            $j(".selectStop li").eq(3).css("display", "");
        }
        var busname = $j("ul[class=busLine]:eq(0) li[class=on]").text();
        $j(".busLineTab").append('<li class="on" caldate="' + $j("#SurfBus").val() + '" style="cursor:pointer;" onclick="' + btnonclick.replace("fnPointList", "fnBusSeatInit").replace("this", "this, '" + busname + "'") + '">' + btntext + busname + '</li>');
    } else {
        // if($j("#SurfBusS").val() == ""){
        // 	alert("출발일을 선택해주세요.");
        // 	return;
        // }
        // var busstop = $j("ul[class=busLine]:eq(1) li[class=on]").length;
        // if(busstop == 0){
        // 	alert("출발노선을 선택해주세요.\n\n모든 노선이 매진된 경우 편도로 예약해주세요~");
        // 	return;
        // }

        // if($j("#SurfBusE").val() == ""){
        // 	alert("복귀일을 선택해주세요.");
        // 	return;
        // }
        // var busstop = $j("ul[class=busLine]:eq(2) li[class=on]").length;
        // if(busstop == 0){
        // 	alert("복귀노선을 선택해주세요.\n\n모든 노선이 매진된 경우 편도로 예약해주세요~");
        // 	return;
        // }

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
            btntext0 = "출발 ";
            btntext1 = "복귀 ";
        }

        var btnonclick = $j("ul[class=busLine]:eq(1) li[class=on]").attr("onclick");
        var busname = $j("ul[class=busLine]:eq(1) li[class=on]").text();
        $j(".busLineTab").append('<li class="on" caldate="' + $j("#SurfBusS").val() + '" style="cursor:pointer;" onclick="' + btnonclick.replace("fnPointList", "fnBusSeatInit").replace("this", "this, '" + busname + "'") + '">' + btntext0 + $j("ul[class=busLine]:eq(1) li[class=on]").text() + '</li>');

        btnonclick = $j("ul[class=busLine]:eq(2) li[class=on]").attr("onclick");
        busname = $j("ul[class=busLine]:eq(2) li[class=on]").text();
        $j(".busLineTab").append('<li caldate="' + $j("#SurfBusE").val() + '" style="cursor:pointer;" onclick="' + btnonclick.replace("fnPointList", "fnBusSeatInit").replace("this", "this, '" + busname + "'") + '">' + btntext1 + $j("ul[class=busLine]:eq(2) li[class=on]").text() + '</li>');
    }

    $j(".busLineTab li").eq(0).click();

    $j('#resStep1').block({ message: null });

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
        $j(obj).addClass("busSeatListY").removeClass("busSeatListC");

        if ($j("#" + selDate + '_' + busNum + ' tr').length == 2) {
            $j("#tb" + selDate + '_' + busNum).remove();
        } else {
            $j("#" + selDate + '_' + busNum + '_' + objVlu).remove();
        }
    } else {
        if (busrestype == "change") {
            if ($j("#daytype").val() == 0) { //편도
                var defaultCnt = Object.keys(busResData).length;
                var selCnt = $j("tr[trseat]").length + 1; //$j("select[id=startLocation" + busType + "]").length + 1;
                if (defaultCnt < selCnt) {
                    alert("선택된 좌석을 취소 후 해당 좌석을 선택해주세요~");
                    return;
                }
            } else { //왕복
                var defaultCntS = 0,
                    defaultCntE = 0;

                //기본 양양행 왕복
                var btntextS = "양양행",
                    btntextE = "서울행";
                var selCntS = $j("#selBusY tr[trseat]").length + 1;
                var selCntE = $j("#selBusS tr[trseat]").length + 1;

                if ($j("#busgubun").val() == "E") { //동해행 왕복
                    btntextS = "동해행";
                    btntextE = "서울행";
                    selCntS = $j("#selBusE tr[trseat]").length + 1;
                    selCntE = $j("#selBusA tr[trseat]").length + 1;
                }

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
                        alert(btntextS + "으로 선택된 좌석을 취소 후 해당 좌석을 선택해주세요~");
                        return;
                    }
                } else {
                    if (defaultCntE < selCntE) {
                        alert(btntextE + "으로 선택된 좌석을 취소 후 해당 좌석을 선택해주세요~");
                        return;
                    }
                }
            }
        } else if (busrestype == "channel") { //타채널 예약건
            var selCntS = $j("#selBusY tr[trseat]").length + 1;
            var selCntE = $j("#selBusS tr[trseat]").length + 1;

            if (busType == "E" || busType == "Y") {
                if (resbusseat1 < selCntS) {
                    alert("서울출발은 " + resbusseat1 + "좌석 예약 가능합니다.");
                    return;
                }
            } else {
                if (resbusseat2 < selCntE) {
                    alert("서울복귀는 " + resbusseat2 + "좌석 예약 가능합니다.");
                    return;
                }
            }
        }

        $j(obj).addClass("busSeatListC").removeClass("busSeatListY");

        var sPoint = "";
        var ePoint = "";
        var arrObjs = eval("busPoint.sPoint" + busNum);
        var arrObje = eval("busPoint.ePoint" + busType);
        arrObjs.forEach(function(el) {
            sPoint += "<option value='" + el.code + "'>" + el.codename + "</option>";
        });
        arrObje.forEach(function(el) {
            ePoint += "<option value='" + el.code + "'>" + el.codename + "</option>";
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
            '					<td style="text-align:center;" onclick="fnSeatDel(this, ' + objVlu + ');"><img src="act/images/button/close.png" style="width:18px;vertical-align:middle;" /></td>' +
            '				</tr>';
        if (tbCnt == 0) {
            insHtml += '			</tbody>' +
                '		</table>';
        }

        $j(bindObj).append(insHtml);
    }

    if (busrestype == "change") {
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
    var arrObjs = eval("busPoint.sPoint" + returnBusNum);
    var arrObje = eval("busPoint.ePoint" + returnBusType);
    arrObjs.forEach(function(el) {
        sPoint += "<option value='" + el.code + "'>" + el.codename + "</option>";
    });
    arrObje.forEach(function(el) {
        ePoint += "<option value='" + el.code + "'>" + el.codename + "</option>";
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
        '					<td style="text-align:center;" onclick="fnSeatDel(this, ' + returnSeat + ');"><img src="act/images/button/close.png" style="width:18px;vertical-align:middle;" /></td>' +
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

var MARKER_SPRITE_POSITION2 = {};
var MARKER_POINT = "",
    MARKER_ZOOM = 17;

function fnBusPoint(obj) {
    $j("input[btnpoint='point']").css("background", "").css("color", "");
    $j(obj).css("background", "#1973e1").css("color", "#fff");

    $j("table[view='tbBus1']").css("display", "none");
    $j("table[view='tbBus2']").css("display", "none");

    var gubun = "S",
        busnum = 1,
        tbBus = 2,
        mapviewid = 12,
        pointname = "니지모리";
    if ($j(obj).val() == "서울출발") {
        gubun = "Y";
        busnum = 1;
        tbBus = 1;
        mapviewid = 0;
        pointname = "공덕역";
    } else if ($j(obj).val() == "서울복귀") {
        gubun = "S";
        busnum = 1;
        tbBus = 2;
        mapviewid = 12;
        pointname = "니지모리스튜디오";
    }

    $j("table[view='tbBus" + tbBus + "']").css("display", "");

    //fnBusMap(gubun, 1, busnum, pointname, ".mapviewid:eq(" + mapviewid + ")", "false");
}

function fnBusMap(gubun, num, busnum, pointname, obj, bool) {
    MARKER_POINT = pointname;
    if (gubun == "S" || gubun == "A") {
        MARKER_ZOOM = 18;
    }

    if (MARKER_SPRITE_POSITION2[pointname] == null) {
        MARKER_SPRITE_POSITION2 = eval("busPointList" + gubun + busnum);
    }

    $j("#mapimg").css("display", "block");
    $j("#mapimg").attr("src", "https://actrip.cdn1.cafe24.com/act_bus/2022/" + gubun + busnum + "_" + num + ".jpg");

    $j(".mapviewid").css("background", "").css("color", "");
    $j(obj).css("background", "#1973e1").css("color", "#fff");

    $j("#ifrmBusMap").css("display", "block").attr("src", "/act/surf/surfmap_bus.html");
    // var obj = $j("#ifrmBusMap").get(0);
    // var objDoc = obj.contentWindow || obj.contentDocument;
    // objDoc.mapMove(pointname);

    if (bool != "false") {
        fnMapView('#mapimg', 40);
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

    var submiturl = "/act/surf/surf_save.php";
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

            //니즈모리 왕복
            var btntextS = "출발행",
                btntextE = "복귀행";
            var selCntS = $j("#selBusY tr[trseat]").length;
            var selCntE = $j("#selBusS tr[trseat]").length;

            if ($j("#busgubun").val() == "E") { //제천 왕복
                btntextS = "출발행";
                btntextE = "복귀행";
                selCntS = $j("#selBusE tr[trseat]").length;
                selCntE = $j("#selBusA tr[trseat]").length;
            }

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

        submiturl = "/act/frip/surf_return.php";

        if (!confirm("액트립 셔틀버스 예약건을 수정하시겠습니까?")) {
            return;
        }
    } else {
        if (busrestype == "channel") {
            var selCntS = $j("#selBusY tr[trseat]").length;
            var selCntE = $j("#selBusS tr[trseat]").length;

            if (resbusseat1 > 0 && resbusseat1 != selCntS) {
                alert("서울출발은 " + resbusseat1 + "좌석 예약해주세요~");
                return;
            }

            if (resbusseat2 > 0 && resbusseat2 != selCntE) {
                alert("서울복귀는 " + resbusseat2 + "좌석 예약해주세요");
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

        if (!confirm("셔틀버스를 예약하시겠습니까?")) {
            return;
        }
    }

    $j('#divConfirm').block({ message: "신청하신 예약건 진행 중입니다." });

    setTimeout('$j("#frmRes").attr("action", "' + submiturl + '").submit();', 500);
    //$j("#frmRes").attr("action", "/act/surf/surf_save.php").submit();
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