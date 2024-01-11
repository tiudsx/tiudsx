var holidays = {
    "0410": { type: 0, title: "국회의원 선거", year: "2024" },
    "0506": { type: 0, title: "어린이날 대체휴일", year: "2024" },

    "0101": { type: 0, title: "신정", year: "" },
    "0301": { type: 0, title: "삼일절", year: "" },
    "0505": { type: 0, title: "어린이날", year: "" },
    "0606": { type: 0, title: "현충일", year: "" },
    "0815": { type: 0, title: "광복절", year: "" },
    "1003": { type: 0, title: "개천절", year: "" },
    "1009": { type: 0, title: "한글날", year: "" },
    "1225": { type: 0, title: "크리스마스", year: "" },

    "0515": { type: 0, title: "석가탄신일", year: "2024" },

    "0916": { type: 0, title: "추석", year: "2024" },
    "0917": { type: 0, title: "추석", year: "2024" },
    "0918": { type: 0, title: "추석", year: "2024" },

    "0128": { type: 0, title: "설날", year: "2025" },
    "0129": { type: 0, title: "설날", year: "2025" },
    "0130": { type: 0, title: "설날", year: "2025" }
    
};

var rtnBusDate = function(day, getDay, json, bus) {
    var holiday = holidays[$j.datepicker.formatDate("mmdd", day)];
    var thisYear = $j.datepicker.formatDate("yy", day);

    if (json != "init") {
        var onoffDay = json[bus][$j.datepicker.formatDate("mmdd", day)];
    }

    var cssRes = "";
    var result;
    if (getDay == 0) {
        cssRes = "date-sunday";
    } else if (getDay == 6) {
        cssRes = "date-saturday";
    }

    //공휴일
    if (holiday) {
        if (thisYear == holiday.year || holiday.year == "") {
            cssRes = "date-sunday";
        }
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

/**선택 날짜 */
var selDate;

jQuery(function() {
    //셔틀버스 달력 표시
    jQuery('input[cal=busdate]').datepicker({
        minDate: new Date((new Date()).getFullYear() + '-01-01'),
        maxDate: new Date((new Date()).getFullYear() + '-12-31'),
        
        onSelect: function(selectedDate) {
            fnBusSearchDate(selectedDate, $j(this).attr("id"));
        },
        beforeShowDay: function(date) {
            var busLine = $j("#bus_line").val();
            if($j(this).attr("id") == "bus_start"){
                busLine = busLine + "_S";
            }else if($j(this).attr("id") == "bus_return"){
                busLine = busLine + "_E";
            }
            return rtnBusDate(date, date.getDay(), json_busDay, busLine);
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

//상단 탭 선택
function fnResViewBus(bool, objid, topCnt, obj) {
    $j(".vip-tabnavi li").removeClass("on");
    $j(obj).addClass("on");

    $j(".con_footer").css("display", "block");
    if (bool) {
        $j("#view_tab1").css("display", "block");
        $j("#view_tab2").css("display", "none");
        $j("#view_tab3").css("display", "none");
    } else {
        $j("#view_tab1").css("display", "none");

        if (objid == "#view_tab2") {
            $j("#view_tab2").css("display", "block");
            $j("#view_tab3").css("display", "none");
        } else {
            $j("#view_tab2").css("display", "none");
            $j("#view_tab3").css("display", "block");

            if (objid == "#view_tab3") {
                $j(".con_footer").css("display", "none");
            }
        }
    }

    fnMapView(objid, topCnt);
}

//정류장 탭 첫번째 노선 클릭
function fnMapClick(){
    if($j("#ifrmBusMap").css("display") == "none"){
        setTimeout('$j("input[type=button]").eq(0).click();', 500);
    }
}

var MARKER_SPRITE_POSITION2 = {};
var MARKER_POINT = "",
    MARKER_ZOOM = 17;

//정류장안내 위치
function fnBusPoint(obj, num) {
    $j("input[btnpoint='point']").css("background", "").css("color", "");
    $j(obj).css("background", "#1973e1").css("color", "#fff");

    $j("table[view='tbBus1']").css("display", "none");
    $j("table[view='tbBus2']").css("display", "none");
    $j("table[view='tbBus3']").css("display", "none");
    $j("table[view='tbBus4']").css("display", "none");
    
    $j("table[view='tbBus" + num + "']").css("display", "");
    
    var gubun = "Y",
        mapviewid = 0,
        pointname = "",
        imgnum = "";

    if(shopseq == 7){ //양양
        if (num == 1) { //사당선
            mapviewid = 0;
            gubun = "SA";
            imgnum = "Y1_1";
            pointname = "신도림";
        } else if (num == 2) { //종로선
            mapviewid = 5;
            gubun = "JO";
            imgnum = "Y2_2";
            pointname = "합정역";
        } else if (num == 3) { //오후 출발
            mapviewid = 9;
            gubun = "AM";
            imgnum = "S1_2";
            pointname = "남애3리";
        }else if (num == 4) { //저녁 출발
            mapviewid = 14;
            gubun = "PM";
            imgnum = "S1_2";
            pointname = "남애3리";
        }
    }else if(shopseq == 14){ //동해
        if (num == 1) { //사당선
            mapviewid = 0;
            gubun = "SA";
            imgnum = "Y1_1";
            pointname = "신도림";
        } else if (num == 2) { //오후 출발
            mapviewid = 5;
            gubun = "AM";
            imgnum = "E1_1";
            pointname = "솔.동해점";
        }
    }

    fnBusMap(gubun, imgnum, pointname, ".mapviewid:eq(" + mapviewid + ")", "false");
}

//정류장지도 표시
function fnBusMap(gubun, imgnum, pointname, obj, bool) {
    MARKER_POINT = pointname;
    if (gubun == "AM" || gubun == "PM") {
        MARKER_ZOOM = 18;
    }

    if (MARKER_SPRITE_POSITION2[pointname] == null) {
        MARKER_SPRITE_POSITION2 = busPoint_Map[gubun];
    }

    $j("#mapimg").css("display", "block");
    $j("#mapimg").attr("src", "https://actrip.cdn1.cafe24.com/act_bus/2022/" + imgnum + ".jpg");

    $j(".mapviewid").css("background", "").css("color", "");
    $j(obj).css("background", "#1973e1").css("color", "#fff");

    $j("#ifrmBusMap").css("display", "block").attr("src", "/act_2023/front/bus/view_bus_map.html");

    if (bool != "false") {
        fnMapView('#mapimg', 40);
    }
}


//행선지 클릭시 이용일 영역 활성화
function fnBusGubun(gubun, obj, type) {
    $j("#ulroute li").removeClass("on");
    $j(obj).addClass("on");


    if (type == "change") { //정류장 변경
        //fnBusSearchDate($j("#SurfBus").val());
    }
    
    $j("#bus_gubun").val(gubun); //편도, 왕복 구분 (S, E, A)
    $j("#bus_start").val(""); //출발일
    $j("#bus_return").val(""); //복귀일

    $j("ul[class=busLine] li:not(:first-child)").remove();

    if (gubun == "S") { //편도 - 출발
        $j("ul[data-key=bus_start]").css("display", "");
        $j("ul[data-key=bus_return]").css("display", "none");
    } else if (gubun == "E") { //편도 - 복귀
       $j("ul[data-key=bus_start]").css("display", "none");
        $j("ul[data-key=bus_return]").css("display", "");
    } else { //왕복
       $j("ul[data-key=bus_start]").css("display", "");
        $j("ul[data-key=bus_return]").css("display", "");
    }
}

/**
 * 달력 날짜 선택시 노선 바인딩
 * @param selectedDate 선택 날짜
 * @param objid id
 */
function fnBusSearchDate(selectedDate, objID) {
    var eqnum = 0;
    var bus_line = $j("#bus_line").val();
    
    if (objID == "bus_start") {
        eqnum = 0;
        bus_line = bus_line + "_S";
    } else if (objID == "bus_return") {
        eqnum = 1;
        bus_line = bus_line + "_E";
    }
    
    $j("ul[class=busLine]:eq(" + eqnum + ") li:not(:first-child)").remove();

    var objParam = {
        "code": "busseatcnt",
        "bus_date": selectedDate,
        "bus_line": bus_line,
        "shopseq": shopseq
    }
    $j.getJSON("/act_2023/front/bus/view_bus_day.php", objParam,
        function(data, textStatus, jqXHR) {
            data.forEach(function(el, i) {
                if (el.seat <= el.seatcnt && !(busrestype == "change" || busrestype == "seatview")) { //매진
                    $j("ul[class=busLine]").eq(eqnum).append('<li onclick="alert(\'선택하신 [' + bus_name + ']는 좌석이 매진되었습니다.\\n\\n취소 좌석이 발생할 경우 예매가능합니다.\');" style="cursor:pointer;text-decoration:line-through;">' + el.bus_name + '</li>');
                }else{
                    $j("ul[class=busLine]").eq(eqnum).append('<li onclick="fnPointList(this);" seat="' + el.seat + '" bus_gubun="' + el.bus_gubun + '" bus_num="' + el.bus_num + '" bus_price="' + el.bus_price + '" style="cursor:pointer;">' + el.bus_name + '</li>');
                }
            });
        }
    );
}

//
/**
 * 노선 클릭시 정류장 및 좌석 바인딩
 * @param obj this
 */
function fnPointList(obj) {
    $j(obj).parent().find("li").removeClass("on");
    $j(obj).addClass("on");
}

/**
 * 이전단계
 * @param {*} num 
 * @returns 
 */
function fnBusPrev(num) {
    if (num == 0) {
        if (!confirm("선택하신 좌석 및 정류장 정보가 초기화됩니다.\n\n이전단계로 돌아가시겠습니까?")) {
            return;
        }
        
        $j('#resStep1').css("display", "");
        
        $j("#seatTab").css("display", "none"); //노선버튼 표시
        $j("#bus_step1").css("display", "none"); //좌석 표시
    }else if(num == 1){
        $j('#frmRes').block({ css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: .6, 
            color: '#fff' 
        } });

        setTimeout( function() {
            $j('#frmRes').unblock();

            $j("#bus_step2").css("display", "none");
            $j('#divConfirm').css("display", "none");

            $j("#seatTab").css("display", ""); //노선버튼 표시
            $j("#bus_step1").css("display", ""); //좌석 표시
        }, 300 );
    }

    fnMapView('#view_tab3', 70);
}

/**
 * 좌석 선택하기
 * @returns 
 */
function fnBusNext(step) {
    if(step == 1){
        $j(".busLineTab li").remove();

        $j(".selectStop li").css("display", "none");
        $j("#selBus_S").html("");
        $j("#selBus_E").html("");

        if ($j("#bus_gubun").val() == "S" || $j("#bus_gubun").val() == "A") { //서울 출발
            if ($j("#bus_start").val() == "") {
                alert("출발일을 선택해주세요.");
                return;
            }

            var bus_selected = $j("ul[class=busLine]:eq(0) li[class=on]");
            if (bus_selected.length == 0) {
                alert("출발노선을 선택해주세요.");
                return;
            }
            
            $j(".selectStop li").eq(0).css("display", "");
            $j(".selectStop li").eq(1).css("display", "");

            $j(".busLineTab").append('<li class="on" caldate="' + $j("#bus_start").val() + '" style="cursor:pointer;" bus_gubun="' + bus_selected.attr("bus_gubun") + '" bus_num="' + bus_selected.attr("bus_num") + '" bus_price="' + bus_selected.attr("bus_price") + '"  bus_name="' + bus_selected.text() + '" onclick="fnBusSeatInit(this, 0);">[출발] ' +  bus_selected.text() + '</li>');
        }

        if ($j("#bus_gubun").val() == "E" || $j("#bus_gubun").val() == "A") { //서울 복귀
            if ($j("#bus_return").val() == "") {
                alert("복귀일을 선택해주세요.");
                return;
            }

            var bus_selected = $j("ul[class=busLine]:eq(1) li[class=on]");
            if (bus_selected.length == 0) {
                alert("복귀노선을 선택해주세요.");
                return;
            }
            
            $j(".selectStop li").eq(2).css("display", "");
            $j(".selectStop li").eq(3).css("display", "");

            var classOn = "class='on' ";
            if($j("#bus_gubun").val() == "A"){
                classOn = "";
            }
            $j(".busLineTab").append('<li ' + classOn + 'caldate="' + $j("#bus_return").val() + '" style="cursor:pointer;" bus_gubun="' + bus_selected.attr("bus_gubun") + '" bus_num="' + bus_selected.attr("bus_num") + '" bus_price="' + bus_selected.attr("bus_price") + '"  bus_name="' + bus_selected.text() + '" onclick="fnBusSeatInit(this, 1);">[복귀] ' +  bus_selected.text() + '</li>');
        }

        $j(".busLineTab2").html($j(".busLineTab").html());

        $j('#resStep1').block({ css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: .6, 
            color: '#fff' 
        } });

        setTimeout( function() {
            $j('#resStep1').css("display", "none");
        
            $j("#seatTab").css("display", ""); //노선버튼 표시
            $j("#bus_step1").css("display", ""); //좌석 표시

            $j('#resStep1').unblock();
            $j(".busLineTab li").eq(0).click(); //첫번째 노선버튼 클릭
        }, 300 );
        
    }else if(step == 2){
        var chkVluS = $j("input[id=hidbusSeatS]").map(function() { return $j(this).val(); }).get();
        var chkVluE = $j("input[id=hidbusSeatE]").map(function() { return $j(this).val(); }).get();
    
        if (($j("#bus_gubun").val() == "S" || $j("#bus_gubun").val() == "A") && chkVluS == "") {
            alert("셔틀버스 출발 좌석을 선택해 주세요.");
            return;
        }

        if (($j("#bus_gubun").val() == "E" || $j("#bus_gubun").val() == "A") && chkVluE == "") {
            alert("셔틀버스 복귀 좌석을 선택해 주세요.");
            return;
        }

        $j('#bus_step1').block({ css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: .6, 
            color: '#fff' 
        } });

        setTimeout( function() {
            $j("#seatTab").css("display", "none"); //노선버튼 표시
            $j("#bus_step1").css("display", "none"); //좌석 표시
    
            $j("#bus_step2").css("display", "");
            $j('#divConfirm').css("display", "");

            $j('#bus_step1').unblock();
        }, 300 );
    }

    //fnMapView("#seatTab", 80);

}

/**
 * 셔틀예약 2단계 노선 버튼 클릭
 * @param {*} obj 
 * @param {*} num 
 */
function fnBusSeatInit(obj, num) {
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
    var selObj = $j("ul[class=busLine]:eq(" + num + ") li[class=on]");
    
    if (selObj.attr("seat") == 44) {
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
    
    selDate = $j(obj).attr("caldate"); //선택 날짜
    
    if (typeof dayCode === 'undefined') {
        dayCode = "busseat";
    }

    var busnum = selObj.attr("bus_num"); //버스 호차
    var bus_gubun = selObj.attr("bus_gubun"); //버스 호차

    var seatjson = [];
    var objParam = {
        "code": dayCode,
        "bus_date": selDate,
        "bus_gubun": bus_gubun,
        "bus_num": busnum,
        "shopseq": shopseq
    }
    $j.getJSON("/act_2023/front/bus/view_bus_day.php", objParam,
        function(data, textStatus, jqXHR) {
            seatjson = data;
        }
    );

    //예약 좌석 표시
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
    
    //도착정류장
    if(bus_gubun == "SA" || bus_gubun == "JO"){
        var busType = "S";
    }else{
        var busType = "E";
    }
    
    //선택 좌석 표시
    if ($j(`#selBus_${busType} > table`).length > 0) {
        var forObj = $j(`#selBus_${busType} [id=hidbusSeat${busType}]`);
        for (var i = 0; i < forObj.length; i++) {
            $j("#tbSeat .busSeatList[busSeat=" + forObj.eq(i).val() + "]").removeClass("busSeatListY").addClass("busSeatListC");
        }
    }

    //정류장 변경시...
    if ((busrestype == "change" || busrestype == "seatview") && businit == 0) {
        if ($j("#bus_gubun").val() == "A") { //왕복
            for (key in busResData) {
                var arrVlu = busResData[key].split("/");
                if (arrVlu[0].substring(0, 1) == "S" || arrVlu[0].substring(0, 1) == "A") {
                    fnSeatChangeSelected(busResData[key]);
                }
            }
        }

        var forObj = $j("select[id=startLocation" + busType + "]");
        for (var i = 0; i < forObj.length; i++) {
            var arrBus = busResData[busNum + "_" + forObj.eq(i).attr("seatnum")].split("/");

            forObj.eq(i).val(arrBus[2]).change();
            forObj.eq(i).next().val(arrBus[3]);
        }
    }

    businit = 1;
}

/**
 * 버스 좌석 선택시 컨트롤
 * @param {*} obj 
 * @returns 
 */
function fnSeatSelected(obj) {    
    if ($j(obj).hasClass("busSeatListN")) return; //선택 불가 (예매된 좌석)
    
    /**선택된 노선 */
    var selObj = $j(".busLineTab li[class=on]");
    /**버스노선 */
    var bus_gubun = selObj.attr("bus_gubun");
    /**버스호차 */
    var bus_num = selObj.attr("bus_num");
    /**버스 노선명 표시 */
    var bus_text = selObj.attr("bus_name");
    /**버스 가격 */
    var bus_price = selObj.attr("bus_price");

    //도착정류장
    if(bus_gubun == "SA" || bus_gubun == "JO"){
        var busType = "S";
    }else{
        var busType = "E";
    }

    var objVlu = $j(obj).attr("busSeat");
    if ($j(obj).hasClass("busSeatListC")) { //내가 예매한 좌석
        if (busrestype == "seatview") { //내좌석보기
            return;
        }

        $j(obj).addClass("busSeatListY").removeClass("busSeatListC");
        
        if ($j(`#selBus_${busType} > table tr`).length == 2) {
            $j(`#selBus_${busType} > table`).remove();
        } else {
            $j(`#selBus_${busType} [trseat=${objVlu}]`).remove();
        }
    } else { //예매 가능한 좌석
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
        
        var arrObj_S = eval("busPoint." + bus_gubun); //출발 정류장
        var sPoint = "<option value='N'>출발</option>";
        arrObj_S.forEach(function(el, i) {
            sPoint += "<option value='" + el.code + "'>" + el.codename + "</option>";
        });

        var arrObj_E = eval("busPoint." + busType + "end");
        var ePoint = "<option value='N'>도착</option>";
        arrObj_E.forEach(function(el, i) {
            ePoint += "<option value='" + el.code + "'>" + el.codename + "</option>"; 
        });

        var insHtml = "";
        var bindObj = "";

        /**좌석선택 여부 */
        var tbCnt = $j(`#selBus_${busType} > table`).length;
        if (tbCnt == 0) {
            insHtml = '		<table class="et_vars exForm bd_tb " style="width:100%;margin-bottom:5px;">' +
                '			<colgroup>' +
                '				<col style="width:45px;">' +
                '				<col style="width:auto;">' +
                '				<col style="width:38px;">' +
                '			</colgroup>' +
                '			<tbody>' +
                '				<tr>' +
                '					<th colspan="3">[' + selDate + '] ' + bus_text +
                '					</th>' +
                '				</tr>';
            bindObj = `#selBus_${busType}`;
        }else{
            bindObj = `#selBus_${busType} > table tbody`;
        }

        insHtml += '				<tr id="' + busType + '_' + objVlu + '" trseat="' + objVlu + '">' +
            '					<th style="padding:4px 6px;text-align:center;">' + objVlu + '번</th>' +
            '					<td style="line-height:2;">' +
            '						<select id="startLocation' + busType + '" seatnum="' + objVlu + '" name="startLocation' + busType + '[]" class="select" onchange="fnBusTime(this, \'' + busType + '\');">' +
            '							' + sPoint +
            '						</select> →' +
            '						<select id="endLocation' + busType + '" seatnum="' + objVlu + '" name="endLocation' + busType + '[]" class="select">' +
            '							' + ePoint +
            '						</select><br>' +
            '						<span id="stopLocation"></span>' +
            '						<input type="hidden" id="hidbusSeat' + busType + '" name="hidbusSeat' + busType + '[]" value="' + objVlu + '" />' +
            '						<input type="hidden" id="hidbusDate' + busType + '" name="hidbusDate' + busType + '[]" value="' + selDate + '" />' +
            '						<input type="hidden" id="hidbusNum' + busType + '" name="hidbusNum' + busType + '[]" value="' + bus_num + '" />' +
            '						<input type="hidden" id="hidbusPrice' + busType + '" value="' + bus_price + '" />' +
            '					</td>' +
            '					<td style="text-align:center;" onclick="fnSeatDel(this, \'' + busType + '\', ' + objVlu + ');"><img src="/act_2023/images/button/close.png" style="width:18px;vertical-align:middle;" /></td>' +
            '				</tr>';
        if (tbCnt == 0) {
            insHtml += '			</tbody>' +
                '		</table>';
        }

        $j(bindObj).append(insHtml);
    }

    if (busrestype == "change" || busrestype == "seatview") {
    } else {
        fnPriceSum('', 1);
    }
}

/**
 * 총 합계 금액
 * @param {*} obj 
 * @param {*} num 
 * @returns 
 */
function fnPriceSum(obj, num) {
    var cntS = $j("input[id=hidbusSeatS]").length;
    var cntE = $j("input[id=hidbusSeatE]").length;

    if ((cntS + cntE) == 0) return;
    
    var bus_priceS = $j("input[id=hidbusPriceS]").val();
    var bus_priceE = $j("input[id=hidbusPriceE]").val();

    var totalPrice = (cntS * bus_priceS) + (cntE * bus_priceE);

    $j("#lastcouponprice").html("");
    if ($j("#couponcode").val() == "" || $j("#couponprice").val() == 0) {
        $j("#lastPrice").html(commify(totalPrice) + "원");
    } else {
        var cp = $j("#couponprice").val();
        if (cp <= 100) { //퍼센트 할인			
            cp = (1 - (cp / 100));
            $j("#lastPrice").html(commify((totalPrice) * cp) + "원");
            $j("#lastcouponprice").html(" (" + commify(totalPrice) + "원 - 할인쿠폰:" + commify((totalPrice) - ((totalPrice) * cp)) + "원)");
        } else { //금액할인
            $j("#lastPrice").html(commify((totalPrice) - cp) + "원");
            $j("#lastcouponprice").html(" (" + commify(totalPrice) + "원 - 할인쿠폰:" + commify(cp) + "원)");
        }
    }
}

/**
 * 좌석선택 삭제
 * @param {*} obj 
 * @param {*} num 
 */
function fnSeatDel(obj, busGubun, num) {
    /**선택된 노선 */
    var selObj = $j(".busLineTab li[class=on]");
    /**버스노선 */
    var bus_gubun = selObj.attr("bus_gubun");

    //도착정류장
    if(bus_gubun == "SA" || bus_gubun == "JO"){
        var busType = "S";
    }else{
        var busType = "E";
    }
    
    if (busType == busGubun) {
        $j("#tbSeat .busSeatList[busSeat=" + num + "]").removeClass("busSeatListC").addClass("busSeatListY");
    }

    if ($j(obj).parents('tbody').find('tr').length == 2) {
        $j(obj).parents('table').remove();
    } else {
        $j(obj).parents('tr').remove();
    }

    fnPriceSum('', 1);
}

/**
 * 셔틀버스 예약하기
 * @returns 
 */
function fnBusSave() {
    var chkVluS = $j("input[id=hidbusSeatS]").map(function() { return $j(this).val(); }).get();
    var chkVluE = $j("input[id=hidbusSeatE]").map(function() { return $j(this).val(); }).get();

    if (($j("#bus_gubun").val() == "S" || $j("#bus_gubun").val() == "A") && chkVluS == "") {
        alert("셔틀버스 출발 좌석을 선택해 주세요.");
        return;
    }

    if (($j("#bus_gubun").val() == "E" || $j("#bus_gubun").val() == "A") && chkVluE == "") {
        alert("셔틀버스 복귀 좌석을 선택해 주세요.");
        return;
    }
    

    var chk_startS = $j("select[id=startLocationS]").map(function() { return $j(this).val(); }).get();
    var chk_endS = $j("select[id=endLocationS]").map(function() { return $j(this).val(); }).get();
    var chk_startE = $j("select[id=startLocationE]").map(function() { return $j(this).val(); }).get();
    var chk_endE = $j("select[id=endLocationE]").map(function() { return $j(this).val(); }).get();

    if (chk_startS.indexOf('N') != -1 || chk_endS.indexOf('N') != -1) {
        alert('출발일 정류장을 선택해주세요.');
        return;
    }
    if (chk_startE.indexOf('N') != -1 || chk_endE.indexOf('N') != -1) {
        alert('복귀일 정류장을 선택해주세요.');
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