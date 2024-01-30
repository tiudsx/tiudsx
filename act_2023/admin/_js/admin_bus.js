$j(function() {
    $j('.btnsurfadd').on('click', function(e){
        
        var $self = $j(this);
        var id = $self.data("gubun");

        if(id != "trbus") return;

        var calObj = $j("calBox[sel=yes]");
        //$j("#hidselDate").val(calObj.attr("value"));
        //$j("#res_busdate").text(calObj.attr("value"));

        if($j("#hidselDate").val() == ""){
            alert("등록할 날짜를 달력에서 클릭하세요.");
            return;
        }

        //row 추가
        fnBusAdd(id);
    })
});

function fnBusCancelDel(obj){
    $j(obj).parent().parent().remove();
    return;
}

//셔틀버스 데이터 완전 삭제
function fnBusDataDel(){
    if (!confirm("등록내역을 완전 삭제하시겠습니까?")) {
        return;
    }

    var calObj = $j("calBox[sel=yes]");
    var formData = { "resparam": "busDatadel", "resnum": $j("#resnum").val() };
    $j.post("/act_2023/admin/bus/list_save.php", formData,
    function(data, textStatus, jqXHR) {
        if (data == 0) {
            alert("정상적으로 삭제되었습니다.");

            if (calObj.attr("value") == null) {
                fnCalMove_Bus($j(".tour_calendar_month").text().replace('.', ''), 99, 0);
            } else {
                fnCalMove_Bus($j(".tour_calendar_month").text().replace('.', ''), calObj.attr("value").split('-')[2], 0);
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

function fnBusPointModify(resnum) {
    window.open("/pointchange?num=&resNumber=" + resnum);
}

function fnChkBusAll(obj, gubun) {
    $j('input[id=chkbusNum' + gubun + ']').prop('checked', $j(obj).is(":checked"));
}

function fnChkBusAll_Kakao(obj, gubun) {
    $j('input[id=chkbusKakaoNum' + gubun + ']').prop('checked', $j(obj).is(":checked"));
}

function fnBusPopupReset() {
    $j("#frmModify")[0].reset();

    $j("tr[rowadd=1]").remove();
}

function fnBusCancelReset() {
    $j("#frmCancel")[0].reset();

    $j("tr[rowadd=1]").remove();
}

function fnBusInsert() {
    $j.blockUI({
        message: $j('#res_modify'),
        focusInput: false,
        css: { width: '90%', textAlign: 'left', left: '5%', top: '14%' }
    });
}

//달력 날짜 선택 - 일정등록
function fnBusMngList(selDate){
    $j.blockUI({ message: "<br><br><br><h1>데이터 조회 중...</h1>", focusInput: false, css: { width: '650px', height: "150px", textAlign: 'center', left: '23%', top: '20%' } });

    $j("#initText2").css("display", "none");
    var url = "busMng/list_info.php";
    var formData = { "resparam": "busmnglist", "selDate": selDate };

    $j.ajax({
        type: "POST",
        url: "/act_2023/admin/" + url,
        data: formData,
        success: function(data) {
            $j("tr[rowadd=1]").remove();
            $j("#res_busdate").text($j("#hidselDate").val());
            if(data == 0){
                //row 추가
                //fnBusAdd("trbus");
            }else{
                for (let i = 0; i < data.length; i++) {
                    fnBusAdd("trbus");

                    var objTr = $j("tr[id=trbus]:last");

                    objTr.find("#resseq").val(data[i].dayseq);
                    objTr.find("#res_busline").val(data[i].bus_line + "|" + data[i].shopseq); //행선지
                    objTr.find("#res_busgubun").val(data[i].bus_gubun); //노선
                    objTr.find("#res_busnum").val(data[i].bus_num); //호차
                    objTr.find("#res_seat").val(data[i].seat);
                    objTr.find("#res_gpsname").val(data[i].gpsname);
                    objTr.find("#res_useYN").val(data[i].useYN);
                    objTr.find("#res_channel").val(data[i].channel);
                    objTr.find("#res_price").val(data[i].price);
                }
                //console.log(data);
            }
            setTimeout('fnBlockClose();', 500);
        }
    });
}

//일정등록 행 추가
function fnBusAdd(id) {
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

//일정등록 행삭제
function fnBusDel(obj){
    if($j(obj).closest("#trbus").find("#resseq").val() == ""){
        $j(obj).parent().parent().remove();
        return;
    }

    if (!confirm("등록내역을 삭제하시겠습니까?")) {
        return;
    }

    var formData = { "resparam": "busMngdel", "resseq": $j(obj).closest("#trbus").find("#resseq").val() };
    $j.post("/act_2023/admin/busMng/list_save.php", formData,
    function(data, textStatus, jqXHR) {
        var arrRtn = data.split('|');
        if (arrRtn[0] == "err") {
            alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요." + "\n\n" + arrRtn[1]);
        } else {            
            fnBusMngList($j("#hidselDate").val());
            fnCalMove_BusMng($j(".tour_calendar_month").text().replace('.', ''), $j("#hidselDate").val().split('-')[2], -2);
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {});
}


function fnBusPointSel2(obj, objVlu, sname, ename, num) {
    var sPoint = "";
    var ePoint = "";


    var arrObjs = eval("busPoint.sPoint" + objVlu.substring(0, 2));
    var arrObje = eval("busPoint.ePoint" + objVlu.substring(0, 1) + "end");
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
    //alert("작업 예정");
    return;
    var params = "resparam=busview&resseq=" + resseq;
    $j.ajax({
        type: "POST",
        url: "/act_2023/admin/bus/list_info.php",
        data: params,
        success: function(data) {
            fnBusPopupReset();

            fnBusInsert();
            console.log(data);
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

                    //쿠폰채널
                    $j("#res_cooperate").val(data[i].res_couponname);
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
                    fnCalMove_Bus($j(".tour_calendar_month").text().replace('.', ''), 99, 0);
                } else {
                    fnCalMove_Bus($j(".tour_calendar_month").text().replace('.', ''), calObj.attr("value").split('-')[2], 0);
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
        }).fail(function(jqXHR, textStatus, errorThrown) {
            
        }
    );
}


function fnBusMngDataAdd() {
    if($j("input[id=resseq]").length <= 1)    {
        alert("날짜 선택 및 버스 추가하세요.");
        return;
    }

    for (let i = 1; i < $j("input[id=resseq]").length; i++) {
        if ($j("select[id=res_busline]").eq(i).val() == "") {
            alert(i + "열 행선지를 선택해주세요~");
            return;
        }
        if ($j("select[id=res_point]").eq(i).val() == "N") {
            alert(i + "열 노선을 선택해주세요~");
            return;
        }
        if ($j("select[id=res_busnum]").eq(i).val() == "N") {
            alert(i + "열 호차를 선택해주세요~");
            return;
        }
    }

    if (!confirm("저장 하시겠습니까?")) {
        return;
    }

    var calObj = $j("calBox[sel=yes]");
    var formData = $j("#frmModify").serializeArray();
    $j.post("/act_2023/admin/busMng/list_save.php", formData,
        function(data, textStatus, jqXHR) {
            if (data == 0) {
                alert("정상적으로 처리되었습니다.");

                fnBusMngList(calObj.attr("value"));
                fnCalMove_BusMng($j(".tour_calendar_month").text().replace('.', ''), calObj.attr("value").split('-')[2], -2);
                fnBlockClose();
            } else {
                var arrRtn = data.split('|');
                if (arrRtn[0] == "err") {
                    alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요." + "\n\n" + arrRtn[1]);
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {});
}

function fnBusMngCopy(selDate){
    if($j("input[id=resseq]").length <= 1) {
        alert("날짜 선택 및 버스 추가하세요.");
        return;
    }
    if($j("input[calid=res_date]").val() == ""){
        alert("복사할 날짜를 선택하세요.");
        return;        
    }
    
    if (!confirm("복사 하시겠습니까?")) {
        return;
    }
    
    var calObj = $j("calBox[sel=yes]");
    var formData = { "resparam": "busMngCopy", "hidselDate": selDate, "copyDate": $j("input[calid=res_date]").val() };
    $j.post("/act_2023/admin/busMng/list_save.php", formData,
        function(data, textStatus, jqXHR) {
            if (data == 0) {
                alert("정상적으로 처리되었습니다.");

                fnBusMngList(calObj.attr("value"));
                fnCalMove_BusMng($j(".tour_calendar_month").text().replace('.', ''), calObj.attr("value").split('-')[2], -2);
                fnBlockClose();
            } else {
                var arrRtn = data.split('|');
                if (arrRtn[0] == "err") {
                    alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요." + "\n\n" + arrRtn[1]);
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {});
}

function fnBusCancel() {
    if (!confirm("취소안내를 발송 하시겠습니까?")) {
        return;
    }

    var formData = $j("#frmCancel").serializeArray();
    $j.post("/act_2023/admin/bus/list_save.php", formData,
        function(data, textStatus, jqXHR) {
            if (data == 0) {
                alert("정상적으로 발송되었습니다.");
                
                //fnBusCancelReset();
            } else {
                var arrRtn = data.split('|');
                if (arrRtn[0] == "err") {
                    alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요." + "\n\n" + arrRtn[1]);
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {});
}

function fnKakaoInfo() {
    if($j("#kakao_sDate").val() == ""){
        alert("시작 날짜를 선택하세요.");
        return;
    }
    
    if($j("#kakao_eDate").val() == ""){
        alert("시작 날짜를 선택하세요.");
        return;
    }

    if (!confirm("카카오톡 안내를 발송 하시겠습니까?")) {
        return;
    }

    var formData = $j("#frmKakaoInfo").serializeArray();
    $j.post("/act_2023/admin/bus/list_save.php", formData,
        function(data, textStatus, jqXHR) {
            if (data == 0) {
                alert("정상적으로 발송되었습니다.");
                
                fnBusCancelReset();
            } else {
                var arrRtn = data.split('|');
                if (arrRtn[0] == "err") {
                    alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요." + "\n\n" + arrRtn[1]);
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {});
}

/**
 * 셔틀버스 예약 정보
 * @param {*} vlu 
 * @param {*} obj 
 * @param {*} folderName 
 */
function fnDayList(gubun, num, obj, folderName){
	$j("input[name=buspoint]").removeClass("buson");
	$j("input[name=buspoint]").css("background", "white");
	
    $j('#dayList').block({ message: "<br><h1>셔틀버스 좌석 조회 중...</h1><br><br>" }); 

    $j(obj).addClass("buson");
    $j(obj).css("background", "#2dc15e");
    $j("#bus_gubun").val(gubun);
    $j("#bus_num").val(num);

    var formData = $j("#frmDaySearch").serializeArray();

    $j.post("/act_2023/admin/" + folderName + "/list_mngsearch.php", formData,
        function(data, textStatus, jqXHR){
            $j("#dayList").html(data);
            $j('#dayList').unblock();
        }).fail(function(jqXHR, textStatus, errorThrown){
        
    });
}

//서핑버스 정산
function fnCalMoveAdminCal(selDate, day) {
    var nowDate = new Date();
    $j("#tab3").load("/act_2023/admin/bus/list_cal.php?selDate=" + selDate + "&selDay=" + day + "&t=" + nowDate.getTime());

}

/**
 * 달력 날짜 클릭
 * @param {*} obj 
 */
function fnDaySelected(obj, seq) {
    var selDate = obj.attributes.value.value;
    
    $j("#right_article3 calBox").not(".nocount").css("background", "white");
    $j("#right_article3 calBox").filter(".nocount").css("background", "#efefef");
    $j("calBox[sel=yes]").attr("sel", "no");
    $j(obj).css("background", "#c6c6ff");
    $j(obj).attr("sel", "yes");

    $j("#sDate").val(selDate);
    $j("#eDate").val(selDate);
    $j("#hidselDate").val(selDate);

    $j("#schText").val('');

    $j('input[id=chkbusNumY1]').prop('checked', true);
    $j('input[id=chkbusNumY2]').prop('checked', true);
    $j('input[id=chkbusNumD1]').prop('checked', true);
    $j('input[id=chkbusNumD2]').prop('checked', true);
    $j('#chkBusY1').prop('checked', true);
    $j('#chkBusY2').prop('checked', true);
    $j('#chkGubun').prop('checked', false);

    $j("#divResList").load("/act_2023/admin/bus/list_mng.php?selDate=" + selDate + "&seq=" + seq);
    $j("#initText2").css("display", "none");

    $j("input[id=chkResConfirm]").prop("checked", false);

    var arrGubun = $j(obj).attr("gubunchk").split(',');
    for (var i = 0; i < arrGubun.length; i++) {
        $j("input[id=chkResConfirm][value=" + arrGubun[i] + "]").prop('checked', true);
    }

    fnSearchAdmin("bus/list_search.php");
}

/**
 * 달력 월 이동 : 양양, 동해 셔틀버스 예약관리
 * @param {*} selDate 
 * @param {*} shopseq 
 */
function fnCalMove_Bus(selDate, shopseq) {
    var nowDate = new Date();

    $j("#divResList").html("");
    $j("#initText2").css("display", "");

    $j("#right_article3").load("/act_2023/admin/bus/_calendar.php?selDate=" + selDate + "&shopseq=" + shopseq + "&t=" + nowDate.getTime());
}

/**
 * 달력 월 이동 : 버스등록
 * @param {*} selDate 
 * @param {*} day 
 * @param {*} seq 
 */
function fnCalMove_BusMng(selDate, day, seq) {
    var nowDate = new Date();

    if (seq == -2 || seq == -3) { //서핑버스
        $j("#divResList").html("");
        $j("#initText2").css("display", "");

        if (seq == -2) { //서핑버스 등록관리
            var calurl = "busMng/_calendar.php";
        }else if (seq == -3) { //서핑버스 등록관리
            var calurl = "busDrive/_calendar.php";
        }
    }

    $j("#right_article3").load("/act_2023/admin/" + calurl + "?selDate=" + selDate + "&selDay=" + day + "&seq=" + seq + "&t=" + nowDate.getTime());
}

/**
 * 타채널 좌석선점 저장
 */
function fnSeatTemp_Save(){
    //var chkVluY = $j("input[id=tempSeat]:checked").map(function() { return $j(this).val(); }).get();
    
    if (!confirm("좌석선점을 하시겠습니까?")) {
        return;
    }

    var formData = $j("#frmDayConfirm").serializeArray();
    $j.post("/act_2023/admin/bus/list_save.php", formData,
        function(data, textStatus, jqXHR) {
            $j("input[name=buspoint]").filter(".buson").click();
        }).fail(function(jqXHR, textStatus, errorThrown) {
        alert(textStatus);
    });
}

/**
 * 타채널 좌석선점 체크박스
 */
function fnSeatTemp_Chk(obj, type){
    const vlu = $j(obj).val();

    if(type == "N" && $j("#tempSeatY_" + vlu).is(":checked")){ //일반 체크 여부 확인
        $j("#tempSeatY_" + vlu).prop('checked', false);
    }else if(type == "Y" && $j("#tempSeatN_" + vlu).is(":checked")){
        $j("#tempSeatN_" + vlu).prop('checked', false);
    }
}