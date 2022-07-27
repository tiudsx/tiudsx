$j(function() {
    $j("ul.tabs li").click(function() {
        $j("ul.tabs li").removeClass("active").css("color", "#333");
        $j(this).addClass("active").css("color", "darkred");
        //$j(".tab_content").hide();
        $j("div[class=tab_content]").css('display', 'none');
        var activeTab = $j(this).attr("rel");
        //$j("#" + activeTab).fadeIn();

        $j("#" + activeTab).css('display', 'block');
    });

});

function fnCalMoveAdminList(selDate, day, seq) {
    var nowDate = new Date();

    $j("#divResList").html("");
    $j("#initText2").css("display", "");
    var calurl = "bus_frip/res_surfcalendar.php";

    $j("#right_article3").load("/act/admin/" + calurl + "?selDate=" + selDate + "&selDay=" + day + "&seq=" + seq + "&t=" + nowDate.getTime());

    var nowYear = selDate.substring(0, 4);
    var nowMon = selDate.substring(4, 6);
    var lastDate = new Date(nowYear, nowMon, "");
}

function fnKakaoSearchAdmin(url) {
    $j.blockUI({ message: "<br><br><br><h1>데이터 조회 중...</h1>", focusInput: false, css: { width: '650px', height: "150px", textAlign: 'center', left: '23%', top: '20%' } });

    var formData = null;//$j("#frmSearch").serializeArray();
    $j.post("/act/admin/" + url, formData,
        function(data, textStatus, jqXHR) {
            $j("#mngKakaoSearch").html(data);
            setTimeout('fnModifyClose();', 500);
        }).fail(function(jqXHR, textStatus, errorThrown) {
        setTimeout('fnModifyClose();', 500);
    });
}

function fnSearchAdmin(url) {
    $j.blockUI({ message: "<br><br><br><h1>데이터 조회 중...</h1>", focusInput: false, css: { width: '650px', height: "150px", textAlign: 'center', left: '23%', top: '20%' } });

    var formData = $j("#frmSearch").serializeArray();
    $j.post("/act/admin/" + url, formData,
        function(data, textStatus, jqXHR) {
            $j("#mngSearch").html(data);
            setTimeout('fnModifyClose();', 500);
        }).fail(function(jqXHR, textStatus, errorThrown) {
        setTimeout('fnModifyClose();', 500);
    });
}

function fnModifyClose() {
    $j.unblockUI();
}

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

    //니지모리 왕복으로만..
    var params = "resparam=reskakao&username=" + $j("#username").val() + "&userphone=" + $j("#userphone").val() + "&reschannel=" + $j("#reschannel").val() + "&resDate1=" + $j("#resDate1").val() + "&resDate2=" + $j("#resDate1").val() + "&resbusseat1=" + $j("#resbusseat1").val() + "&resbusseat2=" + $j("#resbusseat1").val();

    //var params = "resparam=reskakao&username=" + $j("#username").val() + "&userphone=" + $j("#userphone").val() + "&reschannel=" + $j("#reschannel").val() + "&resDate1=" + $j("#resDate1").val() + "&resDate2=" + $j("#resDate2").val() + "&resbusseat1=" + $j("#resbusseat1").val() + "&resbusseat2=" + $j("#resbusseat2").val();
    $j.ajax({
        type: "POST",
        url: "/act/admin/bus/res_bus_save.php",
        data: params,
        success: function (data) {
            if(data == "err"){
                alert("오류가 발생하였습니다.");
            }else{
                $j("#userphone").val("");
                $j("#username").val("");

                fnKakaoSearchAdmin('bus_frip/res_kakao_search.php');
            }
        }
    });
}

function fnBusCouponDel(seq){
    if(!confirm("삭제 하시겠습니까?")){
        return;
    }

    var params = "resparam=reskakaodel&codeseq=" + seq;
    $j.ajax({
        type: "POST",
        url: "/act/admin/bus/res_bus_save.php",
        data: params,
        success: function (data) {
            if(data == "err"){
                alert("오류가 발생하였습니다.");
            }else{
                fnKakaoSearchAdmin('bus_frip/res_kakao_search.php');
            }
        }
    });
}

//달력 날짜 클릭
function fnPassengerAdmin(obj, seq) {
    var selDate = obj.attributes.value.value;
    $j("#right_article3 calBox").css("background", "white");
    $j("calBox[sel=yes]").attr("sel", "no");
    $j(obj).css("background", "#efefef");
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
    $j('#chkBusD1').prop('checked', true);
    $j('#chkBusD2').prop('checked', true);
    $j('#chkGubun').prop('checked', false);

    $j("#divResList").load("/act/admin/bus_frip/res_busmng.php?selDate=" + selDate);
    $j("#initText2").css("display", "none");
    var url = "bus_frip/res_buslist_search.php";

    $j("input[id=chkResConfirm]").prop("checked", false);

    var arrGubun = $j(obj).attr("gubunchk").split(',');
    for (var i = 0; i < arrGubun.length; i++) {
        $j("input[id=chkResConfirm][value=" + arrGubun[i] + "]").prop('checked', true);
    }

    fnSearchAdmin(url);
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

function fnBusModify(resseq) {
    var params = "resparam=busview&resseq=" + resseq;
    $j.ajax({
        type: "POST",
        url: "/act/admin/bus_frip/res_bus_info.php",
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
                    // if (data[i].admin_user == "" || data[i].admin_user == null) {
                    //     $j("#res_adminname").val("이승철");
                    // } else {
                    //     $j("#res_adminname").val(data[i].admin_user);
                    // }
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
    $j.post("/act/admin/bus_frip/res_bus_save.php", formData,
        function(data, textStatus, jqXHR) {
            if (data == 0) {
                alert("정상적으로 처리되었습니다.");

                fnCalMoveAdminList($j(".tour_calendar_month").text().replace('.', ''), 0, 0);

                if ($j("input[name=buspoint]").length > 0) {
                    if ($j("input[name=buspoint]").filter(".buson").length > 0) {
                        $j("input[name=buspoint]").filter(".buson").click();
                    }
                }

                fnSearchAdmin('bus_frip/res_buslist_search.php');
                fnModifyClose();
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

function fnSelChange(obj, num) {
    if ($j(obj).val() == "") {

    } else {
        $j(".allselect" + num).val($j(obj).val());
        $j(obj).val("");
    }
}

function fnBusPointModify(resnum) {
    window.open("/pointchangeFrip?num=&resNumber=" + resnum);
}