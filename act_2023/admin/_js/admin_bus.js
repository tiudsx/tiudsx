$j(function() {
    $j('.btnsurfadd').on('click', function(e){
        
        var $self = $j(this);
        var id = $self.data("gubun");

        var calObj = $j("calBox[sel=yes]");
        $j("#hidselDate").val(calObj.attr("value"));
        $j("#res_busdate").val(calObj.attr("value"));

        if($j("#hidselDate").val() == ""){
            alert("등록할 날짜를 달력에서 클릭하세요.");
            return;
        }

        //row 추가
        fnBusAdd(id);
    })
});

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
            fnCalMoveAdminList($j(".tour_calendar_month").text().replace('.', ''), $j("#hidselDate").val().split('-')[2], -2);
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {});
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
function fnBusChannelKakao(kakao_msgid){
    if(!confirm("독촉 발송 하시겠습니까?")){
        return;
    }

    var params = "resparam=reskakaode2&kakao_msgid=" + kakao_msgid;
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


function fnBusMngDataAdd(gubun) {
    if($j("input[id=res_busdate]").length <= 1)    {
        alert("날짜 선택 및 버스 추가하세요.");
        return;
    }

    for (let i = 1; i < $j("input[id=res_busdate]").length; i++) {
        if ($j("select[id=res_busgubun]").eq(i).val() == "") {
            alert(i + "열 버스번호를 선택해주세요~");
            return;
        }
        if ($j("select[id=res_point]").eq(i).val() == "N") {
            alert(i + "열 노선을 선택해주세요~");
            return;
        }
    }

    var text1 = "등록을 하시겠습니까?";
    var text2 = "등록이 완료되었습니다.";
    if (gubun == "modify") {
        text1 = "수정을 하시겠습니까?";
        text2 = "수정이 완료되었습니다.";
    }

    if (!confirm(text1)) {
        return;
    }

    var calObj = $j("calBox[sel=yes]");
    var formData = $j("#frmModify").serializeArray();
    $j.post("/act_2023/admin/busMng/list_save.php", formData,
        function(data, textStatus, jqXHR) {
            if (data == 0) {
                alert("정상적으로 처리되었습니다.");

                fnBusMngList(calObj.attr("value"));
                fnCalMoveAdminList($j(".tour_calendar_month").text().replace('.', ''), calObj.attr("value").split('-')[2], -2);
                fnBlockClose();
            } else {
                var arrRtn = data.split('|');
                if (arrRtn[0] == "err") {
                    alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요." + "\n\n" + arrRtn[1]);
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {});
}

function fnDayList(vlu, obj, folderName){
	$j("input[name=buspoint]").removeClass("buson");
	$j("input[name=buspoint]").css("background", "white");
	if(vlu == "ALL"){
		$j("#dayList").html('<div style="text-align:center;font-size:14px;padding:50px;"><b>버스종류를 선택하세요.</b></div>');
	}else{
		$j('#dayList').block({ message: "<br><h1>셔틀버스 좌석 조회 중...</h1><br><br>" }); 

		$j(obj).addClass("buson");
		$j(obj).css("background", "#2dc15e");
		$j("#busNum").val(vlu);

		var formData = $j("#frmDaySearch").serializeArray();

		$j.post("/act_2023/admin/" + folderName + "/list_mngsearch.php", formData,
			function(data, textStatus, jqXHR){
			   $j("#dayList").html(data);
			   $j('#dayList').unblock();
			}).fail(function(jqXHR, textStatus, errorThrown){
		 
		});
	}
}