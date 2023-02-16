var busPoint = {}
busPoint.sPointY1 = [];
busPoint.sPointY1.push({ "code": "N", "codename": "출발" });
busPoint.sPointY1.push({ "code": "신도림", "codename": "신도림" });
busPoint.sPointY1.push({ "code": "대림역", "codename": "대림역" });
busPoint.sPointY1.push({ "code": "사당역", "codename": "사당역" });
busPoint.sPointY1.push({ "code": "강남역", "codename": "강남역" });
busPoint.sPointY1.push({ "code": "종합운동장역", "codename": "종합운동장역" });

busPoint.sPointY3 = busPoint.sPointY1;
busPoint.sPointY5 = busPoint.sPointY1;

busPoint.sPointY2 = [];
busPoint.sPointY2.push({ "code": "N", "codename": "출발" });
busPoint.sPointY2.push({ "code": "합정역", "codename": "합정역" });
busPoint.sPointY2.push({ "code": "종로3가역", "codename": "종로3가역" });
busPoint.sPointY2.push({ "code": "건대입구", "codename": "건대입구" });
busPoint.sPointY2.push({ "code": "종합운동장역", "codename": "종합운동장역" });

busPoint.sPointY4 = busPoint.sPointY2;
busPoint.sPointY6 = busPoint.sPointY2;

busPoint.ePointY = [];
busPoint.ePointY.push({ "code": "N", "codename": "도착" });
busPoint.ePointY.push({ "code": "서피비치", "codename": "서피비치" });
busPoint.ePointY.push({ "code": "기사문", "codename": "기사문" });
busPoint.ePointY.push({ "code": "죽도해변", "codename": "죽도해변" });
busPoint.ePointY.push({ "code": "인구해변", "codename": "인구해변" });
busPoint.ePointY.push({ "code": "남애3리", "codename": "남애3리" });

busPoint.sPointE1 = busPoint.sPointY1;
busPoint.sPointE2 = busPoint.sPointY2;
busPoint.sPointE3 = busPoint.sPointY1;
busPoint.sPointE4 = busPoint.sPointY2;
busPoint.sPointE5 = busPoint.sPointY1;
busPoint.sPointE6 = busPoint.sPointY2;

busPoint.ePointE = [];
busPoint.ePointE.push({ "code": "N", "codename": "도착" });
busPoint.ePointE.push({ "code": "금진해변", "codename": "금진해변" });
busPoint.ePointE.push({ "code": "대진해변", "codename": "대진해변" });
busPoint.ePointE.push({ "code": "솔.동해점", "codename": "솔.동해점" });

busPoint.sPointS21 = [];
busPoint.sPointS21.push({ "code": "N", "codename": "출발" });
busPoint.sPointS21.push({ "code": "남애3리", "codename": "남애3리" });
busPoint.sPointS21.push({ "code": "인구해변", "codename": "인구해변" });
busPoint.sPointS21.push({ "code": "죽도해변", "codename": "죽도해변" });
busPoint.sPointS21.push({ "code": "기사문", "codename": "기사문" });
busPoint.sPointS21.push({ "code": "서피비치", "codename": "서피비치" });

busPoint.sPointS22 = busPoint.sPointS21;
busPoint.sPointS23 = busPoint.sPointS21;
busPoint.sPointS51 = busPoint.sPointS21;
busPoint.sPointS52 = busPoint.sPointS21;
busPoint.sPointS53 = busPoint.sPointS21;

busPoint.ePointS = [];
busPoint.ePointS.push({ "code": "N", "codename": "도착" });
busPoint.ePointS.push({ "code": "잠실역", "codename": "잠실역" });
busPoint.ePointS.push({ "code": "강남역", "codename": "강남역" });
busPoint.ePointS.push({ "code": "사당역", "codename": "사당역" });

busPoint.ePointA = busPoint.ePointS;

busPoint.sPointA21 = [];
busPoint.sPointA21.push({ "code": "N", "codename": "출발" });
busPoint.sPointA21.push({ "code": "솔.동해점", "codename": "솔.동해점" });
busPoint.sPointA21.push({ "code": "대진해변", "codename": "대진해변" });
busPoint.sPointA21.push({ "code": "금진해변", "codename": "금진해변" });

busPoint.sPointA22 = busPoint.sPointA21;
busPoint.sPointA23 = busPoint.sPointA21;
busPoint.sPointA51 = busPoint.sPointA21;
busPoint.sPointA52 = busPoint.sPointA21;
busPoint.sPointA53 = busPoint.sPointA21;

var busPoint_1 = "신도림 &gt; 대림역 &gt; 사당역 &gt; 강남역 &gt; 종합운동장역";
var busPoint_2 = "합정역 &gt; 종로3가역 &gt; 건대입구 &gt; 종합운동장역";
var busPoint_3 = "남애3리 &gt; 인구해변 &gt; 죽도해변 &gt; 기사문 &gt; 서피비치";
var busPoint_4 = "금진해변 &gt; 대진해변 &gt; 솔.동해점";
var busPointList = {
    "Y1": { li: busPoint_1 },
    "Y2": { li: busPoint_2 },
    "Y3": { li: busPoint_1 },
    "Y4": { li: busPoint_2 },
    "Y5": { li: busPoint_1 },
    "Y6": { li: busPoint_2 },
    "E1": { li: busPoint_1 },
    "E2": { li: busPoint_2 },
    "E3": { li: busPoint_1 },
    "E4": { li: busPoint_2 },
    "E5": { li: busPoint_1 },
    "E6": { li: busPoint_2 },
    "S21": { li: busPoint_3 },
    "S22": { li: busPoint_3 },
    "S23": { li: busPoint_3 },
    "S51": { li: busPoint_3 },
    "S52": { li: busPoint_3 },
    "S53": { li: busPoint_3 },
    "A21": { li: busPoint_4 },
    "A22": { li: busPoint_4 },
    "A23": { li: busPoint_4 },
    "A51": { li: busPoint_4 },
    "A52": { li: busPoint_4 },
    "A53": { li: busPoint_4 }
};

var MARKER_SPRITE_X_OFFSET = 29,
    MARKER_SPRITE_Y_OFFSET = 50;
var busPointListY1 = {
    "신도림": [0, MARKER_SPRITE_Y_OFFSET * 3, '37.5095592', '126.8885712', '홈플러스 신도림점 앞', '탑승시간 : <font color="red">06시 20분</font>', 0],
    "대림역": [MARKER_SPRITE_X_OFFSET * 1, MARKER_SPRITE_Y_OFFSET * 3, '37.4928008', '126.8947074', '대림역 2번출구 앞', '탑승시간 : <font color="red">06시 30분</font>', 1],
    "봉천역": [MARKER_SPRITE_X_OFFSET * 2, MARKER_SPRITE_Y_OFFSET * 3, '37.4821436', '126.9426997', '봉천역 1번출구 앞', '탑승시간 : <font color="red">06시 40분</font>', 2],
    "사당역": [MARKER_SPRITE_X_OFFSET * 3, MARKER_SPRITE_Y_OFFSET * 3, '37.4764763', '126.977734', '사당역 6번출구 방향 참약사 장수약국 앞', '탑승시간 : <font color="red">06시 50분</font>', 3],
    "강남역": [MARKER_SPRITE_X_OFFSET * 4, MARKER_SPRITE_Y_OFFSET * 3, '37.4982078', '127.0290928', '강남역 1번출구 버스정류장', '탑승시간 : <font color="red">07시 05분</font>', 4],
    "종합운동장역": [MARKER_SPRITE_X_OFFSET * 5, MARKER_SPRITE_Y_OFFSET * 3, '37.5104765', '127.0722925', '종합운동장역 4번출구 방향 버스정류장 뒤쪽', '탑승시간 : <font color="red">07시 20분</font>', 5]
};
var busPointListY2 = {
    "당산역": [0, MARKER_SPRITE_Y_OFFSET * 3, '37.5348183', '126.900387', '당산역 13출구 방향 버거킹 앞', '탑승시간 : <font color="red">06시 05분</font>', 0],
    "합정역": [MARKER_SPRITE_X_OFFSET * 1, MARKER_SPRITE_Y_OFFSET * 3, '37.5507926', '126.9159159', '합정역 3번출구 앞', '탑승시간 : <font color="red">06시 10분</font>', 1],
    "종로3가역": [MARKER_SPRITE_X_OFFSET * 2, MARKER_SPRITE_Y_OFFSET * 3, '37.5703347', '126.99317687', '종로3가역 12번출구 방향 새마을금고 앞', '탑승시간 : <font color="red">06시 35분</font>', 2],
    "왕십리역": [MARKER_SPRITE_X_OFFSET * 3, MARKER_SPRITE_Y_OFFSET * 3, '37.5615557', '127.0348018', '왕십리역 11번출구 방향 우리은행 앞', '탑승시간 : <font color="red">06시 50분</font>', 3],
    "건대입구": [MARKER_SPRITE_X_OFFSET * 4, MARKER_SPRITE_Y_OFFSET * 3, '37.5393413', '127.0716672', '건대입구역 롯데백화점 스타시티점 입구', '탑승시간 : <font color="red">07시 05분</font>', 4],
    "종합운동장역": [MARKER_SPRITE_X_OFFSET * 5, MARKER_SPRITE_Y_OFFSET * 3, '37.5104765', '127.0722925', '종합운동장역 4번출구 방향 버스정류장 뒤쪽', '탑승시간 : <font color="red">07시 20분</font>', 5]
};
var busPointListS1 = {
    "청시행비치": [0, 0, '37.910099', '128.8168456', '청시행비치 주차장 입구', '탑승시간 : <font color="red">14시 15분 / 17시 15분</font>', 0],
    "남애해변": [MARKER_SPRITE_X_OFFSET * 1, 0, '37.9452543', '128.7814356', '남애3리 입구', '탑승시간 : <font color="red">14시 30분 / 17시 30분</font>', 1],
    "인구해변": [MARKER_SPRITE_X_OFFSET * 2, 0, '37.9689758', '128.7599915', '현남면사무소 맞은편', '탑승시간 : <font color="red">14시 35분 / 17시 35분</font>', 2],
    "죽도해변": [MARKER_SPRITE_X_OFFSET * 3, 0, '37.9720003', '128.7595433', 'GS25 죽도비치점 맞은편', '탑승시간 : <font color="red">14시 40분 / 17시 40분</font>', 3],
    "동산항해변": [MARKER_SPRITE_X_OFFSET * 4, 0, '37.9763045', '128.7586692', '동산카센타 맞은편', '탑승시간 : <font color="red">14시 45분 / 17시 45분</font>', 4],
    "기사문해변": [MARKER_SPRITE_X_OFFSET * 5, 0, '38.0053627', '128.7306342', '기사문 해변주차장 입구', '탑승시간 : <font color="red">14시 50분 / 17시 50분</font>', 5],
    "서피비치": [MARKER_SPRITE_X_OFFSET * 6, 0, '38.0268271', '128.7169575', '서피비치 회전교차로 횡단보도 앞', '탑승시간 : <font color="red">15시 00분 / 18시 00분</font>', 6]
}
var busPointListA1 = {
    "솔.동해점": [0, 0, '37.5782382', '129.1156248', '솔.동해점 입구', '탑승시간 : <font color="red">14시 00분 / 17시 00분</font>', 0],
    "대진항": [MARKER_SPRITE_X_OFFSET * 1, 0, '37.5807657', '129.111344', '대진항 공영주차장 입구', '탑승시간 : <font color="red">14시 05분 / 17시 05분</font>', 1],
    "금진해변": [MARKER_SPRITE_X_OFFSET * 2, 0, '37.6347202', '129.0450586', '금진해변 공영주차장 입구', '탑승시간 : <font color="red">14시 20분 / 17시 20분</font>', 2]
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
    var params = "res_spointname=" + obj.value + "&res_bus=" + busnum;
    $j.ajax({
        type: "POST",
        url: "/act/surf/surfbus_point.php",
        data: params,
        success: function(data) {
            objStop.text(data);
        }
    })
}

//타채널 알림톡 발송
function fnResKakaoAdmin(){
    if($j("#username").val() == ""){
        alert("이름을 입력하세요.");
        return;
    }
    
    if($j("#userphone").val() == ""){
        alert("연락처를 입력하세요.");
        return;
    }

    if(!confirm("알림톡 발송을 하시겠습니까?")){
        return;
    }

    var params = "resparam=reskakao&username=" + $j("#username").val() + "&resbus=" + $j("#resbus").val() + "&userphone=" + $j("#userphone").val() + "&reschannel=" + $j("#reschannel").val() + "&resDate1=" + $j("#resDate1").val() + "&resDate2=" + $j("#resDate2").val() + "&resbusseat1=" + $j("#resbusseat1").val() + "&resbusseat2=" + $j("#resbusseat2").val();
    $j.ajax({
        type: "POST",
        url: "/act_2023/admin/bus/list_save.php",
        data: params,
        success: function (data) {
            if(data == "err"){
                alert("오류가 발생하였습니다.");
            }else{
                $j("#userphone").val("");
                $j("#username").val("");

                fnSearchAdmin('bus/list_search_channel.php', '#mngKakaoSearch', 'N');
            }
        }
    });
}

//타채널 예약건 독촉 발송
function fnBusChannelKakao(username, usertel){
    if(!confirm("독촉 발송 하시겠습니까?")){
        return;
    }

    var params = "resparam=reskakaode2&user_name=" + username + "&user_tel=" + usertel;
    $j.ajax({
        type: "POST",
        url: "/act_2023/admin/bus/list_save.php",
        data: params,
        success: function (data) {
            if(data == "err"){
                alert("오류가 발생하였습니다.");
            }else{
                fnSearchAdmin('bus/list_search_channel.php', '#mngKakaoSearch', 'N');
            }
        }
    });
}

function fnBusChannelDel(seq){
    if(!confirm("삭제 하시겠습니까?")){
        return;
    }

    var params = "resparam=reskakaodel&codeseq=" + seq;
    $j.ajax({
        type: "POST",
        url: "/act_2023/admin/bus/list_save.php",
        data: params,
        success: function (data) {
            if(data == "err"){
                alert("오류가 발생하였습니다.");
            }else{
                fnSearchAdmin('bus/list_search_channel.php', '#mngKakaoSearch', 'N');
            }
        }
    });
}

function fnBusPointModify(resnum) {
    window.open("/pointchange?num=&resNumber=" + resnum);
}

function fnChkBusAll(obj, gubun) {
    $j('input[id=chkbusNum' + gubun + ']').prop('checked', $j(obj).is(":checked"));
}

function fnBusPopupReset() {
    $j("#frmModify")[0].reset();

    $j("tr[rowadd=1]").remove();
}

function fnBusInsert() {
    $j.blockUI({
        message: $j('#res_modify'),
        focusInput: false,
        css: { width: '90%', textAlign: 'left', left: '5%', top: '14%' }
    });
}

function fnBusAdd(id) {
    var date = (new Date()).yyyymmdd(); //오늘 날짜
    var objTr = $j("tr[id=" + id + "]").eq(0);

    $j("tr[id=" + id + "]:last").after(objTr.clone());
    $j("tr[id=" + id + "]:last").css("display", "")
    $j("tr[id=" + id + "]:last").find('input[cal=date]').removeClass('hasDatepicker').removeAttr('id').datepicker({
        onClose: function(selectedDate) {
            var date = jQuery(this).datepicker('getDate');
            if (!(date == null)) {
                jQuery(this).next().select();
            }
        }
    });
    $j("tr[id=" + id + "]:last").attr("rowadd", "1");
}

function fnBusPointSel2(obj, objVlu, sname, ename, num) {
    var sPoint = "";
    var ePoint = "";

    var arrObjs = eval("busPoint.sPoint" + objVlu);
    var arrObje = eval("busPoint.ePoint" + objVlu.substring(0, 1));
    arrObjs.forEach(function(el) {
        if (sname == el.code) {
            sPoint += "<option value='" + el.code + "' selected>" + el.codename + "</option>";
        } else {
            sPoint += "<option value='" + el.code + "'>" + el.codename + "</option>";
        }
    });
    arrObje.forEach(function(el) {
        if (ename == el.code) {
            ePoint += "<option value='" + el.code + "' selected>" + el.codename + "</option>";
        } else {
            ePoint += "<option value='" + el.code + "'>" + el.codename + "</option>";
        }
    });

    if (num == 2) {
        obj = $j(obj).parents("tr");
    }

    obj.find("#res_spointname").html(sPoint);
    obj.find("#res_epointname").html(ePoint);
}

function fnSelChange(obj, num) {
    if ($j(obj).val() == "") {

    } else {
        $j(".allselect" + num).val($j(obj).val());
        $j(obj).val("");
    }
}

function fnBusModify(resseq) {
    var params = "resparam=busview&resseq=" + resseq;
    $j.ajax({
        type: "POST",
        url: "/act_2023/admin/bus/list_info.php",
        data: params,
        success: function(data) {
            fnBusPopupReset();

            fnBusInsert();

            var TotalPrice = 0,
                TotalDisPrice = 0,
                RtnTotalPrice = 0;
            for (let i = 0; i < data.length; i++) {
                if (i == 0) {
                    $j("#resseq").val(data[i].resseq);
                    $j("#user_name").val(data[i].user_name);
                    $j("#user_tel").val(data[i].user_tel);
                    $j("#resnum").val(data[i].resnum);
                    $j("#insdate").val(data[i].insdate);
                    $j("#confirmdate").val(data[i].confirmdate);
                    $j("#res_coupon").val(data[i].res_coupon);
                    $j("#res_price_coupon").val(data[i].res_price_coupon);
                    $j("#etc").val(data[i].etc);
                    $j("#memo").val(data[i].memo);
                    $j("#user_email").val(data[i].user_email);
                    var res_cooperate = "";
                    if (data[i].res_coupon == "JOABUS") {
                        res_cooperate = "조아서프";
                    } else if (data[i].res_coupon == "NAVER") {
                        res_cooperate = "네이버";
                    } else if (data[i].res_coupon == "KLOOK") {
                        res_cooperate = "KLOOK";
                    } else if (data[i].res_coupon == "NABUSB") {
                        res_cooperate = "예약";
                    } else if (data[i].res_coupon == "FRIP") {
                        res_cooperate = "프립";
                    } else if (data[i].res_coupon == "MYTRIP") {
                        res_cooperate = "마이리얼트립";
                    } else if (data[i].couponseq == 14) {
                        res_cooperate = "망고서프";
                    } else if (data[i].res_coupon != "") {
                        res_cooperate = "일반할인";
                    }

                    $j("#res_cooperate").val(res_cooperate);
                }

                fnBusAdd('trbus');

                var objTr = $j("tr[id=trbus]:last");
                objTr.find("#res_confirm").val(data[i].res_confirm);
                objTr.find("#res_confirmText").text(objTr.find("#res_confirm option:selected").text());
                objTr.find("#rtn_charge_yn").val(data[i].rtn_charge_yn);
                objTr.find("#res_seat").val(data[i].res_seat);
                objTr.find("input[calid=res_date]").val(data[i].res_date);
                objTr.find("#ressubseq").val(data[i].ressubseq);
                objTr.find("#res_busnum").val(data[i].res_busnum);
                fnBusPointSel2(objTr, data[i].res_busnum, data[i].res_spointname, data[i].res_epointname, 1);

                var res_price = parseInt(data[i].res_price, 10);
                var res_totalprice = parseInt(data[i].res_totalprice, 10);
                var rtn_totalprice = parseInt(data[i].rtn_totalprice, 10);
                if (data[i].res_confirm == 0) {

                } else if (data[i].res_confirm == 1) {

                } else if (data[i].res_confirm == 2) {
                    TotalPrice += res_price;
                    TotalDisPrice += res_totalprice;
                } else if (data[i].res_confirm == 6) {

                } else if (data[i].res_confirm == 8) {
                    TotalPrice += res_price;
                    TotalDisPrice += res_totalprice;
                } else if (data[i].res_confirm == 3) {
                    TotalPrice += res_price;
                    TotalDisPrice += res_totalprice;
                } else if (data[i].res_confirm == 4) {
                    TotalPrice += res_price;
                    TotalDisPrice += res_totalprice;
                    RtnTotalPrice += rtn_totalprice;
                } else if (data[i].res_confirm == 5) {
                    TotalPrice += res_price;
                    TotalDisPrice += res_totalprice;
                    RtnTotalPrice += rtn_totalprice;
                } else if (data[i].res_confirm == 7) {

                }
            }

            $j("#res_price").val(TotalDisPrice);
            $j("#res_disprice").val(TotalPrice - TotalDisPrice);
        }
    });
}

function fnBusDataAdd() {
    if ($j("#user_name").val() == "") {
        alert("예약자이름을 입력하세요~");
        return;
    }

    if ($j("#user_tel").val() == "") {
        alert("연락처를 입력하세요~");
        return;
    }

    if ($j("#insdate").val() == "") {
        alert("신청일을 입력하세요~");
        return;
    }

    if ($j("#confirmdate").val() == "") {
        alert("확정일을 입력하세요~");
        return;
    }


    for (let i = 1; i < $j("input[id=ressubseq]").length; i++) {
        if ($j("input[calid=res_date]").eq(i).val() == "") {
            alert(i + "열 이용 날짜를 선택해주세요~");
            return;
        }
        if ($j("select[id=res_spointname]").eq(i).val() == "N") {
            alert(i + "열 출발 정류장을 선택해주세요~");
            return;
        }

        if ($j("select[id=res_epointname]").eq(i).val() == "N") {
            alert(i + "열 도착 정류장을 선택해주세요~");
            return;
        }
    }


    if (!confirm("수정 하시겠습니까?")) {
        return;
    }

    var calObj = $j("calBox[sel=yes]");
    var formData = $j("#frmModify").serializeArray();
    $j.post("/act_2023/admin/bus/list_save.php", formData,
        function(data, textStatus, jqXHR) {
            if (data == 0) {
                alert("정상적으로 처리되었습니다.");

                if (calObj.attr("value") == null) {
                    fnCalMoveAdminList($j(".tour_calendar_month").text().replace('.', ''), 99, 0);
                } else {
                    fnCalMoveAdminList($j(".tour_calendar_month").text().replace('.', ''), calObj.attr("value").split('-')[2], 0);
                }

                if ($j("input[name=buspoint]").length > 0) {
                    if ($j("input[name=buspoint]").filter(".buson").length > 0) {
                        $j("input[name=buspoint]").filter(".buson").click();
                    }
                }

                fnSearchAdmin('bus/list_search.php');
                fnBlockClose();
                fnBusPopupReset();
            } else {
                var arrRtn = data.split('|');
                if (arrRtn[0] == "err") {
                    alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요." + "\n\n" + arrRtn[1]);
                } else {
                    alert(arrRtn[1] + "호 " + arrRtn[2] + "번 좌석은 예약되어있습니다.\n\n다른 호차 및 좌석을 선택해주세요~");
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {});
}

function fnModifyInfo(type, seq, gubun) {
    if (type == "bus") {
        var params = "resparam=busmodify&ressubseq=" + seq;
        $j.ajax({
            type: "POST",
            url: "/act_2023/admin/bus/list_info.php",
            data: params,
            success: function(data) {
                if (data == "err") {
                    alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요.");
                } else {
                    $j("#gubun").val(gubun); //구분코드
                    $j("#resnum").val(data.resnum); //예약번호
                    $j("#ressubseq").val(data.ressubseq);
                    $j("#insdate").val(data.insdate);
                    $j("#confirmdate").val(data.confirmdate);
                    $j("#res_confirm").val(data.res_confirm);
                    $j("#res_date").val(data.res_date);
                    $j("#user_name").val(data.user_name);
                    $j("#user_tel").val(data.user_tel);
                    $j("#user_email").val(data.user_email);
                    $j("#rtn_charge_yn").val(data.rtn_charge_yn);
                    $j("#res_price_coupon").val(data.res_price_coupon); //쿠폰
                    $j("#res_price").val(data.res_price); //기본가격
                    $j("#res_busnum").val(data.res_busnum); //호차
                    fnBusPointSel(data.res_busnum, data.res_spointname, data.res_epointname);
                    $j("#res_seat").val(data.res_seat);
                    $j("#res_spointname").val(data.res_spointname); //출발 정류장
                    $j("#res_epointname").val(data.res_epointname); //도착 정류장

                    if (mobileuse == "") {
                        $j.blockUI({ message: $j('#res_busmodify'), css: { width: '650px', textAlign: 'left', left: '23%', top: '20%' } });
                    } else {
                        if ($j('#res_busmodify').length != 0) {
                            $j.blockUI({ message: $j('#res_busmodify'), css: { width: '90%', textAlign: 'left', left: '5%', top: '5%' } });
                        }
                    }
                }
            }
        });
    }
}