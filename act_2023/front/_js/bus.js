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

jQuery(function() {
    jQuery('input[cal=busdate]').datepicker({
        minDate: new Date((new Date()).getFullYear() + '-01-01'),
        maxDate: new Date((new Date()).getFullYear() + '-12-31'),
        // onClose: function (selectedDate) {
        // 	if(selectedDate != ""){
        // 		fnBusSearchDate(selectedDate, $j(this).attr("gubun"));
        // 	}
        // },
        onSelect: function(selectedDate) {
            fnBusSearchDate(selectedDate, $j(this).attr("gubun"), $j(this).attr("id"));

            
            //달력컨트롤 : 출발노선 체크
            if($j("#nextchk").val() == "Y"){
                var arrDataS = 0;
                var arrDataE = 0;
                //편도
                if($j("#daytype").val() == "0"){
                    //양양행
                    if($j("#busgubun").val() == "Y"){

                    }else{ //서울행

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
            var busGubun = $j("#busgubun").val();
            if($j(this).attr("id") == "SurfBusS"){
                busGubun = busGubun.substring(0, 2) + "_S";
            }else if($j(this).attr("id") == "SurfBusE"){
                busGubun = busGubun.substring(0, 2) + "_E";
            }
            return rtnBusDate(date, date.getDay(), json_busDay, busGubun);
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
            gubun = "사당";
            imgnum = "Y1_1";
            pointname = "신도림";
        } else if (num == 2) { //종로선
            mapviewid = 5;
            gubun = "종로";
            imgnum = "Y2_2";
            pointname = "합정역";
        } else if (num == 3) { //오후 출발
            mapviewid = 9;
            gubun = "오후";
            imgnum = "S1_2";
            pointname = "남애3리";
        }else if (num == 4) { //저녁 출발
            mapviewid = 14;
            gubun = "저녁";
            imgnum = "S1_2";
            pointname = "남애3리";
        }
    }else if(shopseq == 14){ //동해
        if (num == 1) { //사당선
            mapviewid = 0;
            gubun = "사당";
            imgnum = "Y1_1";
            pointname = "신도림";
        } else if (num == 2) { //오후 출발
            mapviewid = 5;
            gubun = "오후";
            imgnum = "E1_1";
            pointname = "솔.동해점";
        }
    }

    fnBusMap(gubun, imgnum, pointname, ".mapviewid:eq(" + mapviewid + ")", "false");
}

//정류장지도 표시
function fnBusMap(gubun, imgnum, pointname, obj, bool) {
    MARKER_POINT = pointname;
    if (gubun == "오후" || gubun == "저녁") {
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

    $j("ul[class=busLine] li").remove();
    $j("ul[class=busLine]").eq(0).css("display", "block").append('<li><img src="/act_2023/images/viewicon/bus.svg" alt="">노선</li>');

    $j("ul[id=buspointlist]").css("display", "none");

    if (type == "change") { //정류장 변경
        fnBusSearchDate($j("#SurfBus").val(), $j("#SurfBus").attr("gubun"), $j("#SurfBus").attr("id"));
    }
    
    $j("#busgubun").val(gubun);
}

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
        $j("ul[class=busLine]").eq(0).css("display", "block").append('<li><img src="/act_2023/images/viewicon/bus.svg" alt="">노선</li>');
    } else { //왕복
        $j("#route").css("display", "none");
        $j("#busdate").css("display", "none");
        $j("#sbusdate").css("display", "");
        $j("#ebusdate").css("display", "");

        $j("ul[class=busLine] li").remove();
        $j("ul[class=busLine]").eq(1).css("display", "block").append('<li><img src="/act_2023/images/viewicon/bus.svg" alt="">출발노선</li>');
        $j("ul[class=busLine]").eq(2).css("display", "block").append('<li><img src="/act_2023/images/viewicon/bus.svg" alt="">복귀노선</li>');
    }
}

//달력 날짜 선택시 노선 바인딩
function fnBusSearchDate(selectedDate, gubun, objid) {
    //$j("#buspointlist").css("display", "none");
    //$j("#busnotdate").css("display", "none");
    var eqnum = 0;
    if (objid == "SurfBusS") {
        $j("ul[class=busLine]:eq(1) li").remove();
        $j("ul[class=busLine]").eq(1).css("display", "block").append('<li><img src="/act_2023/images/viewicon/bus.svg" alt="">출발노선</li>');
        eqnum = 1;
    } else if (objid == "SurfBusE") {
        $j("ul[class=busLine]:eq(2) li").remove();
        $j("ul[class=busLine]").eq(2).css("display", "block").append('<li><img src="/act_2023/images/viewicon/bus.svg" alt="">복귀노선</li>');
        eqnum = 2;
    } else {
        $j("ul[class=busLine]:eq(0) li").remove();
        $j("ul[class=busLine]").eq(0).css("display", "block").append('<li><img src="/act_2023/images/viewicon/bus.svg" alt="">노선</li>');
    }

    if (objid == "SurfBusE") {
        gubun = fnBusDateGubun(gubun, "SurfBusE");
    }
    var arrData = busData[gubun + selectedDate.substring(5).replace('-', '')];
    arrData.forEach(function(el) {
        
        var selVlu = el.busnum.substring(0, 3);
        var selBool = true;
        
        //프립 서핑패키지 : 오후차 예약불가
        // if(buschannel == 17 || buschannel == 20 || buschannel == 21 || buschannel == 22){
        //     if(selVlu == "SY2" || selVlu == "AE2"){
        //         selBool = false;
        //     }
        // }

        if(selBool){
            var objParam = {
                "code": "busseatcnt",
                "busDate": selectedDate,
                "busNum": el.busnum
            }
            $j.getJSON("/act_2023/front/bus/view_bus_day.php", objParam,
                function(data, textStatus, jqXHR) {
                    if (data[0].seatcnt == el.busseat) {
                        if (busrestype == "change" || busrestype == "seatview") {
                            $j("ul[class=busLine]").eq(eqnum).append('<li onclick="fnPointList(\'' + el.busnum + '\', ' + el.busseat + ', this);" busnum="' + el.busnum + '" style="cursor:pointer;text-decoration:line-through;">' + el.busname + '</li>');
                        } else {
                            $j("ul[class=busLine]").eq(eqnum).append('<li onclick="alert(\'선택하신 [' + el.busname + ']는 좌석이 매진되었습니다.\\n\\n취소 좌석이 발생할 경우 예매가능합니다.\');" style="cursor:pointer;text-decoration:line-through;">' + el.busname + '</li>');
                        }
                    } else {
                        $j("ul[class=busLine]").eq(eqnum).append('<li onclick="fnPointList(\'' + el.busnum + '\', ' + el.busseat + ', this);" busnum="' + el.busnum + '" style="cursor:pointer;">' + el.busname + '</li>');
                    }
                }
            );
        }

    });

    //$j(".busLine li").eq(1).click();
}