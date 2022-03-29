//셀렉트 박스 상태 변경
function fnChangeModify(obj, confirmVlu) {
    if (mobileuse == "m") {
        var trObj = $j(obj).parents("tbody");
    } else {
        var trObj = $j(obj).parent().parent();
    }
    if (confirmVlu == $j(obj).val()) {
        trObj.find("#chkCancel").prop("checked", false);
    } else {
        trObj.find("#chkCancel").prop("checked", true);
    }

    var MainNumber = $j(obj).attr("resnum");
    if ($j("select[resnum=" + MainNumber + "] option:selected[value=6]").length == 0) {
        $j("#tr" + MainNumber).css("display", "none");
    } else {
        $j("#tr" + MainNumber).css("display", "");
    }
}

//상태 변경처리 - 건당
function fnConfirmUpdate(obj, num) {
    $j("#frmConfirmSel").html($j("#hidInitParam").html());

    if (num == 1) {
        var tbObj = $j("#frmConfirm");
    } else {
        var tbObj = $j(obj).parent().parent();
    }

    var chkObj = tbObj.find("input[id=chkCancel]");
    // if(tbObj.find("input[id=chkCancel]:checked").length == 0){
    // 	alert("승인처리 설정이 안된 항목이 있습니다.");
    // 	return;
    // }
    var chk_cnt = tbObj.find("input[id=chkCancel]:not(:checked)").length;
    var res_cnt = tbObj.find("select[id=selConfirm] option:selected[value=3]").length;
    var sel_cnt = tbObj.find("select[id=selConfirm] option:selected[value=8]").length;
    var scancel_cnt = tbObj.find("select[id=selConfirm] option:selected[value=6]").length;

    if (num < 3) {
        //if(chk_cnt > 0){
        if (sel_cnt > 0) {
            alert("승인처리 변경이 안된 항목이 있습니다.");
            return;
        }
    }

    if (scancel_cnt > 0) {
        if ($j("#tr" + tbObj.find("input[id=MainNumber]").val()).find("#memo").val() == "") {
            alert("취소사유를 작성해주세요~");
            return;
        }
    }

    if (!confirm("상태변경 하시겠습니까?")) {
        return;
    }

    if (res_cnt == tbObj.find("input[id=chkCancel]:checked").length) { // 전체 확정 처리
        var res_confirm = 3;
    } else { //부분 확정처리
        var res_confirm = 2;
    }

    var chkBox = '';
    for (var i = 0; i < chkObj.length; i++) {
        if (chkObj.eq(i).is(":checked")) {
            chkBox += '<input type="checkbox" id="chkCancel" name="chkCancel[]" checked="checked" value="' + chkObj.eq(i).val() + '" />';

            if (tbObj.find("select[id=selConfirm]").eq(i).val() == 3) {
                selres_confirm = res_confirm;
            } else {
                selres_confirm = tbObj.find("select[id=selConfirm]").eq(i).val();
            }
            chkBox += '<input type="text" id="selConfirm" name="selConfirm[]" value="' + selres_confirm + '" />';
        }
    }
    chkBox += '<input type="text" id="MainNumber" name="MainNumber" value="' + tbObj.find("input[id=MainNumber]").val() + '" />';
    chkBox += '<textarea id="memo" name="memo">' + tbObj.find("textarea[id=memo]").val() + '</textarea>';
    if (num == 3) {
        chkBox += '<input type="text" id="shopseq" name="shopseq" value="' + tbObj.find("input[id=shopseq]").val() + '" />';
    }

    $j("#frmConfirmSel").append(chkBox);

    // $j("#frmConfirmSel").attr("action", "/act/admin/shop/res_kakao_save.php").submit();
    var formData = $j("#frmConfirmSel").serializeArray();

    $j.post("/act/admin/shop/res_kakao_save.php", formData,
        function(data, textStatus, jqXHR) {
            if (data == 0) {
                alert("정상적으로 처리되었습니다.");
                if (num == 1) {
                    setTimeout('location.reload();', 500);
                } else if (num == 0) {
                    fnCalMoveAdmin($j(".tour_calendar_month").text().replace('.', ''), 0, $j("#shopseq").val());
                } else if (num == 2) {
                    //fnCalMoveAdminList($j(".tour_calendar_month").text().replace('.', ''), 0, $j("#shopseq").val());
                    fnSearchAdmin("shop/res_surflist_search.php");
                } else if (num == 3) {
                    //fnCalMoveAdminList($j(".tour_calendar_month").text().replace('.', ''), 0, -1);
                    fnSearchAdmin("act_admin/res_surflist_search.php");
                }

            } else {
                alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요.");
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {

    });
}

function fnConfirmUpdateList(obj, num, resnum) {
    $j("#frmConfirmSel").html($j("#hidInitParam").html());

    var tbObj = $j("select[resnum=" + resnum + "]");
    var chkObj = $j("input[resnum=" + resnum + "]");
    //var chkObj = tbObj.find("input[id=chkCancel]");

    //var chk_cnt = tbObj.find("input[id=chkCancel]:not(:checked)").length;
    var res_cnt = tbObj.find("option:selected[value=3]").length;
    var sel_cnt = tbObj.find("option:selected[value=8]").length;
    var scancel_cnt = tbObj.find("option:selected[value=6]").length;

    if (num > 1) {
        if (sel_cnt > 0) {
            alert("승인처리 변경이 안된 항목이 있습니다.");
            return;
        }

        if (scancel_cnt > 0) {
            if ($j("#tr" + resnum).find("#memo").val() == "") {
                alert("취소사유를 작성해주세요~");
                return;
            }
        }
    }

    if (!confirm("상태변경 하시겠습니까?")) {
        return;
    }

    if (res_cnt == chkObj.filter(":checked").length) { // 전체 확정 처리
        var res_confirm = 3;
    } else { //부분 확정처리
        var res_confirm = 2;
    }

    var chkBox = '';
    for (var i = 0; i < chkObj.length; i++) {
        if (chkObj.eq(i).is(":checked")) {
            chkBox += '<input type="checkbox" id="chkCancel" name="chkCancel[]" checked="checked" value="' + chkObj.eq(i).val() + '" />';

            if (tbObj.eq(i).val() == 3 && num > 1) {
                selres_confirm = res_confirm;
            } else {
                selres_confirm = tbObj.eq(i).val();
            }
            chkBox += '<input type="text" id="selConfirm" name="selConfirm[]" value="' + selres_confirm + '" />';
        }
    }
    chkBox += '<input type="text" id="MainNumber" name="MainNumber" value="' + resnum + '" />';
    chkBox += '<textarea id="memo" name="memo">' + $j("#tr" + resnum).find("#memo").val() + '</textarea>';
    if (num == 3) {
        chkBox += '<input type="text" id="shopseq" name="shopseq" value="' + $j("#shopseq").val() + '" />';
    }

    $j("#frmConfirmSel").append(chkBox);

    // $j("#frmConfirmSel").attr("action", "/act/admin/shop/res_kakao_save.php").submit();
    var formData = $j("#frmConfirmSel").serializeArray();

    if (num == 2 || num == 3) { //서핑샵
        $postUrl = "/act/admin/shop/res_kakao_save.php";
    } else if (num == 1) { //서핑버스
        $postUrl = "/act/admin/bus/res_bus_save.php";
    }
    console.dir(formData);
    $j.post($postUrl, formData,
        function(data, textStatus, jqXHR) {
            console.dir(data);
            if (data == 0) {
                alert("정상적으로 처리되었습니다.");
                if (num == 1) {
                    fnCalMoveAdminList($j(".tour_calendar_month").text().replace('.', ''), 0, 0);
                    fnSearchAdmin("bus/" + mobileuse + "res_buslist_search.php");
                } else if (num == 2) {
                    fnCalMoveAdminList($j(".tour_calendar_month").text().replace('.', ''), 0, $j("#shopseq").val()); //달력갱신
                    fnSearchAdmin("shop/res_surflist_search.php"); //예약목록 갱신
                } else if (num == 3) {
                    fnCalMoveAdminList($j(".tour_calendar_month").text().replace('.', ''), 0, -1);
                    fnSearchAdmin("act_admin/res_surflist_search.php");
                }
            } else {
                alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요.");
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {

    });
}

//상태 변경처리 - 건당
function fnConfirmUpdateBus(obj) {
    $j("#frmConfirmSel").html($j("#hidInitParam").html());

    var tbObj = $j(obj).parent().parent();
    var chkObj = tbObj.find("input[id=chkCancel]");
    if (!confirm("상태변경 하시겠습니까?")) {
        return;
    }

    var chkBox = '';
    for (var i = 0; i < chkObj.length; i++) {
        if (chkObj.eq(i).is(":checked")) {
            chkBox += '<input type="checkbox" id="chkCancel" name="chkCancel[]" checked="checked" value="' + chkObj.eq(i).val() + '" />';

            selres_confirm = tbObj.find("select[id=selConfirm]").eq(i).val();
            chkBox += '<input type="text" id="selConfirm" name="selConfirm[]" value="' + selres_confirm + '" />';
        }
    }
    chkBox += '<input type="text" id="MainNumber" name="MainNumber" value="' + tbObj.find("input[id=MainNumber]").val() + '" />';
    chkBox += '<textarea id="memo" name="memo">' + tbObj.find("textarea[id=memo]").val() + '</textarea>';

    $j("#frmConfirmSel").append(chkBox);

    var formData = $j("#frmConfirmSel").serializeArray();

    $j.post("/act/admin/bus/res_bus_save.php", formData,
        function(data, textStatus, jqXHR) {
            if (data == 0) {
                alert("정상적으로 처리되었습니다.");

                //fnCalMoveAdminList($j(".tour_calendar_month").text().replace('.', ''), 0, 0);
                fnSearchAdmin("bus/" + mobileuse + "res_buslist_search.php");
            } else {
                alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요.");
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {

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

    if (seq == 0) { //서핑버스 관리자
        $j('input[id=chkbusNumY1]').prop('checked', true);
        $j('input[id=chkbusNumY2]').prop('checked', true);
        $j('input[id=chkbusNumD1]').prop('checked', true);
        $j('input[id=chkbusNumD2]').prop('checked', true);
        $j('#chkBusY1').prop('checked', true);
        $j('#chkBusY2').prop('checked', true);
        $j('#chkBusD1').prop('checked', true);
        $j('#chkBusD2').prop('checked', true);
        $j('#chkGubun').prop('checked', false);

        $j("#divResList").load("/act/admin/bus/" + mobileuse + "res_busmng.php?selDate=" + selDate);
        $j("#initText2").css("display", "none");
        var url = "bus/" + mobileuse + "res_buslist_search.php";
    } else if (seq == -1) {
        var url = "act_admin/res_surflist_search.php";
    } else {
        var url = "shop/res_surflist_search.php";
    }

    $j("input[id=chkResConfirm]").prop("checked", false);

    var arrGubun = $j(obj).attr("gubunchk").split(',');
    for (var i = 0; i < arrGubun.length; i++) {
        $j("input[id=chkResConfirm][value=" + arrGubun[i] + "]").prop('checked', true);
    }

    fnSearchAdmin(url);
}

//달력 날짜 클릭 - 카카오
function fnPassengerAdminKakao(obj, seq) {
    var selDate = obj.attributes.value.value;
    $j("#right_article3 calBox").css("background", "white");
    $j("calBox[sel=yes]").attr("sel", "no");
    $j(obj).css("background", "#efefef");
    $j(obj).attr("sel", "yes");

    $j("#sDate").val(selDate);
    $j("#eDate").val(selDate);
    $j("#hidselDate").val(selDate);

    $j("#schText").val('');
    var url = "shop/res_kakao_all.php";
    $j("input[id=chkResConfirm]").prop("checked", false);

    var arrGubun = $j(obj).attr("gubunchk").split(',');
    for (var i = 0; i < arrGubun.length; i++) {
        $j("input[id=chkResConfirm][value=" + arrGubun[i] + "]").prop('checked', true);
    }

    fnSearchAdminKakao(url);
}

function fnCalMoveAdminList(selDate, day, seq) {
    var nowDate = new Date();

    //$j("input[id=chkResConfirm]").prop("checked", false);
    if (seq == 0) { //서핑버스
        $j("#divResList").html("");
        $j("#initText2").css("display", "");
        var url = "bus/" + mobileuse + "res_buslist_search.php";
        if (mobileuse == "m") {
            // var calurl = "bus/mres_buslist_2_calendar.php";
            var calurl = "shop/res_surfcalendar.php";
        } else {
            var calurl = "shop/res_surfcalendar.php";
        }

        // $j("input[id=chkResConfirm]:eq(0)").prop("checked", true);
        // $j("input[id=chkResConfirm]:eq(1)").prop("checked", true);
        // $j("input[id=chkResConfirm]:eq(2)").prop("checked", true);
        // $j("input[id=chkResConfirm]:eq(5)").prop("checked", true);
    } else if (seq == -1) { //입점샵 전체
        var url = "act_admin/res_surflist_search.php";
        var calurl = "act_admin/res_surfcalendar.php";

        // $j("input[id=chkResConfirm]:eq(0)").prop("checked", true);
        // $j("input[id=chkResConfirm]:eq(1)").prop("checked", true);
        // $j("input[id=chkResConfirm]:eq(2)").prop("checked", true);
        // $j("input[id=chkResConfirm]:eq(4)").prop("checked", true);
        // $j("input[id=chkResConfirm]:eq(6)").prop("checked", true);
        // $j("input[id=chkResConfirm]:eq(8)").prop("checked", true);

        //fnSearchAdmin("act_admin/res_surflist_search.php");
    } else if (seq == -2) { //솔 목록
        var url = "sol/res_sollist_search.php";
        var calurl = "sol/res_calendar.php";
    } else { //입점샵 일반
        var url = "shop/res_surflist_search.php";
        var calurl = "shop/res_surfcalendar.php";

        // $j("input[id=chkResConfirm]:eq(1)").prop("checked", true);
    }

    $j("#right_article3").load("/act/admin/" + calurl + "?selDate=" + selDate + "&selDay=" + day + "&seq=" + seq + "&t=" + nowDate.getTime());
    //$j("#mngSearch").load("/act/admin/" + url + "?selDate=" + selDate + "&selDay=" + day + "&seq=" + seq + "&t=" + nowDate.getTime());

    var nowYear = selDate.substring(0, 4);
    var nowMon = selDate.substring(4, 6);
    var lastDate = new Date(nowYear, nowMon, "");

    //$j("#sDate").val(nowYear + '-' + nowMon + '-01');
    //$j("#eDate").val(nowYear + '-' + nowMon + '-' + lastDate.getDate());
    //$j("#schText").val('');

    //fnSearchAdmin(url);
}

function fnCalMoveAdminListKakao(selDate, day, seq) {
    var nowDate = new Date();
    $j("#right_article3").load("/act/admin/shop/res_kakaocalendar.php?selDate=" + selDate + "&selDay=" + day + "&seq=" + seq + "&t=" + nowDate.getTime());
}

//카카오톡 예약관리 목록
function fnCalMoveAdmin(selDate, day, seq) {
    var nowDate = new Date();
    $j("#rescontent").load("/act/admin/shop/res_kakao_all.php?selDate=" + selDate + "&selDay=" + day + "&seq=" + seq + "&t=" + nowDate.getTime());
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

function fnSearchAdminKakao(url) {
    $j.blockUI({ message: "<br><br><br><h1>데이터 조회 중...</h1>", focusInput: false, css: { width: '300px', height: "150px", textAlign: 'center', left: '13%', top: '30%' } });

    var formData = $j("#frmSearch").serializeArray();
    $j.post("/act/admin/" + url, formData,
        function(data, textStatus, jqXHR) {
            $j("#rescontent").html(data);
            setTimeout('fnModifyClose();', 500);
        }).fail(function(jqXHR, textStatus, errorThrown) {
        setTimeout('fnModifyClose();', 500);
    });
}

function fnSearchAdminSol(url, objid) {
    var formData = $j("#" + objid).prev().serializeArray();
    $j.post("/act/admin/" + url, formData,
        function(data, textStatus, jqXHR) {
            $j("#" + objid).html(data);
        }).fail(function(jqXHR, textStatus, errorThrown) {

    });
}

// row 클릭
function fnListViewKakao(obj) {
    if (mobileuse == "m") {
        var objNext = $j(obj).next().next();
        $j("tr[name='btnTrList']").removeClass('selTr');
        $j("tr[name='btnTrList']").next().removeClass('selTr');
        if (objNext.css("display") == "none") {
            $j(obj).addClass('selTr');
            $j(obj).next().addClass('selTr');

            $j("tr[name='btnTrList']").next().next().css("display", "none");
            objNext.css("display", "");
        } else {

            objNext.css("display", "none");
        }
    } else {
        var objNext = $j(obj).next();
        $j("tr[name='btnTrList']").removeClass('selTr');
        if (objNext.css("display") == "none") {
            $j(obj).addClass('selTr');

            $j("tr[name='btnTrList']").next().css("display", "none");
            objNext.css("display", "");
        } else {

            objNext.css("display", "none");
        }
    }
}

function fnSoldout() {
    var formData = $j("#frmSold").serializeArray();

    if ($j("#strDate").val() == "") {
        alert("시작날짜를 선택하세요.");
        return;
    }

    if ($j("#strDateE").val() == "") {
        alert("종료날짜를 선택하세요.");
        return;
    }

    if ($j("#selItem:checked").length == 0) {
        alert("항목을 하나이상 선택하세요.");
        return;
    }

    if (!($j("#chkSexM").is(':checked') || $j("#chkSexW").is(':checked'))) {
        alert("성별 중 하나이상 선택하세요.");
        return;
    }

    if (!confirm("선택항목을 매진 처리 하시겠습니까?")) {
        return;
    }

    $j.post("/act/admin/shop/res_kakao_save.php", formData,
        function(data, textStatus, jqXHR) {
            if (data == 1) {
                alert("해당 날짜와 항목은 이미 매진처리 되었습니다.\n\n해당 항목을 삭제 후 추가해주세요.");
            } else if (data == 0) {
                alert("정상적으로 매진 처리되었습니다.");
                $j("#divSoldOutList").load("/act/admin/shop/res_surflist_soldout.php?chk=1");
            } else {
                alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요.");
            }

        }).fail(function(jqXHR, textStatus, errorThrown) {});
}

function fnSoldModify(seq) {
    if (!confirm("선택항목을 삭제 처리 하시겠습니까?")) {
        return;
    }

    var params = "resparam=soldoutdel&soldoutseq=" + seq;
    $j.ajax({
        type: "POST",
        url: "/act/admin/shop/res_kakao_save.php",
        data: params,
        success: function(data) {
            if (data == 0) {
                alert("정상적으로 매진 처리되었습니다.");
                $j("#divSoldOutList").load("/act/admin/shop/res_surflist_soldout.php?chk=1");
            } else {
                alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요.");
            }
        }
    });
}

function fnCalSearch(url) {
    var formData = $j("#frmCal").serializeArray();

    $j.post("/act/admin/" + url, formData,
        function(data, textStatus, jqXHR) {
            $j("#divCalList").html(data);
        }).fail(function(jqXHR, textStatus, errorThrown) {

    });
}

function fnDateReset() {
    $j("#sDate").val('');
    $j("#eDate").val('');
}

function fnModifyInfo(type, seq, gubun, obj) {
    // $j("tr[name='btnTr']").removeClass('selTr');
    // $j("tr[name='btnTrPoint']").removeClass('selTr');
    // $j(obj).parent().parent().addClass('selTr');

    // if($j(obj).parent().parent().next().attr('name') == 'btnTrPoint'){
    // 	$j(obj).parent().parent().next().addClass('selTr');
    // }

    // $j("#tab3").load(folderBusRoot + "/Admin_BusModify.php?subintseq=" + seq + '&gubun=' + gubun);
    // $j(".tab_content").hide();
    // $j("#tab3").fadeIn();
    if (type == "surf") {

    } else if (type == "bus") {
        var params = "resparam=busmodify&ressubseq=" + seq;
        $j.ajax({
            type: "POST",
            url: "/act/admin/bus/res_bus_info.php",
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

function fnBusPointSel(objVlu, sname, ename) {
    var sPoint = "";
    var ePoint = "";

    var arrObjs = eval("busPoint.sPoint" + objVlu);
    var arrObje = eval("busPoint.ePoint" + objVlu.substring(0, 1));
    arrObjs.forEach(function(el) {
        if (sname == "") {
            sPoint += "<option value='" + el.code + "'>" + el.codename + "</option>";
        } else {
            sPoint += "<option value='" + el.code + "' selected>" + el.codename + "</option>";
        }
    });
    arrObje.forEach(function(el) {
        if (sname == "") {
            ePoint += "<option value='" + el.code + "'>" + el.codename + "</option>";
        } else {
            ePoint += "<option value='" + el.code + "' selected>" + el.codename + "</option>";
        }
    });

    $j("#res_spointname").html(sPoint);
    $j("#res_epointname").html(ePoint);
}

function fnModifyClose() {
    $j.unblockUI();
}

function fnDataModify() {
    if ($j("#insdate").val() == "") {
        alert("신청일을 입력하세요~");
        return;
    }
    if ($j("#confirmdate").val() == "") {
        alert("확정일을 입력하세요~");
        return;
    }
    if ($j("#res_date").val() == "") {
        alert("이용일을 입력하세요~");
        return;
    }
    if ($j("#user_name").val() == "") {
        alert("이름을 입력하세요~");
        return;
    }
    if ($j("#res_spointname").val() == "N") {
        alert("출발 정류장을 선택하세요~");
        return;
    }
    if ($j("#res_epointname").val() == "N") {
        alert("도착 정류장을 선택하세요~");
        return;
    }
    if (!confirm("정보수정을 하시겠습니까?")) {
        return;
    }

    var calObj = $j("calBox[sel=yes]");
    var formData = $j("#frmModify").serializeArray();
    // $j("#frmModify #userid").val(userid);

    $j.post("/act/admin/bus/res_bus_save.php", formData,
        function(data, textStatus, jqXHR) {
            if (data == 0) {
                alert("정상적으로 처리되었습니다.");
                //$j("#divResList").load("/act/admin/bus/res_busmng.php?selDate=" + $j("#hidselDate").val());

                if (calObj.attr("value") == null) {
                    fnCalMoveAdmin($j(".tour_calendar_month").text().replace('.', ''), 99);
                } else {
                    fnCalMoveAdmin($j(".tour_calendar_month").text().replace('.', ''), calObj.attr("value").split('-')[2]);
                }

                if ($j("input[name=buspoint]").length > 0) {
                    if ($j("input[name=buspoint]").filter(".buson").length > 0) {
                        $j("input[name=buspoint]").filter(".buson").click();
                    }
                }

                fnSearchAdmin('bus/res_buslist_search.php');
            } else {
                alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요.");
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {

    });
}

var shopList2 = {
    "surfeast1": {},
    "surfeast2": {},
    "surfeast3": {},
    "surfjeju": {},
    "surfsouth": {},
    "surfwest": {},
    "bbqparty": {},
    "etc": {}
};
var shopList3 = {};

function cateList(obj, objName) {
    $j("#" + objName + "2").html('<option value="ALL">== 전체 ==</option>');
    $j("#" + objName + "3").html('<option value="ALL">== 전체 ==</option>');
    if ($j(obj).val() != "ALL") {
        $j.each(shopList2[$j(obj).val()], function(key, vlu) {
            $j("#" + objName + "2").append('<option value="' + key + '">' + vlu + '</option>');
        });
    }
}

function cateList2(obj, objName) {
    $j("#" + objName + "3").html('<option value="ALL">== 전체 ==</option>');
    if ($j(obj).val() != "ALL") {
        $j.each(shopList3[$j(obj).val()], function(key, vlu) {
            $j("#" + objName + "3").append('<option value="' + key + '">' + vlu + '</option>');
        });
    }
}

//서핑샵 변경
function fnChangeShop() {
    var shopseq = $j("#selShop").val();

    location.href = "/shopadmin?seq=" + shopseq;
}

//서핑버스 정산
function fnCalMoveAdminCal(selDate, day) {
    var nowDate = new Date();
    $j("#tab3").load("/act/admin/bus/res_bus_cal.php?selDate=" + selDate + "&selDay=" + day + "&t=" + nowDate.getTime());

}

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

function fnChkAll(obj, objid) {
    if ($j(obj).is(":checked")) {
        $j('input[id=' + objid + ']').prop('checked', true);
    } else {
        $j('input[id=' + objid + ']').prop('checked', false);
    }
}

function fnChkBusAll(obj, gubun) {
    $j('input[id=chkbusNum' + gubun + ']').prop('checked', $j(obj).is(":checked"));
}

function fnSurfInsert() {
    $j.blockUI({
        message: $j('#res_modify'),
        focusInput: false,
        css: { width: '90%', textAlign: 'left', left: '5%', top: '14%' }
    });
}

function fnSurfModify(resseq) {
    var params = "resparam=surfview&resseq=" + resseq;
    $j.ajax({
        type: "POST",
        url: "/act/admin/sol/res_sollist_info.php",
        data: params,
        success: function(data) {
            // fnSolpopupReset();

            fnSurfInsert();

            //row 초기화
            $j("tr[rowadd=1]").remove();

            var RtnTotalPrice = 0,
                RtnTotalPrice2 = 0;
            for (let i = 0; i < data.length; i++) {
                if (i == 0) {
                    $j("#resseq").val(data[i].resnum);
                    $j("#shopseq2").val(data[i].seq);
                    // $j("#res_adminname").val(data[i].admin_user);
                    $j("#user_name").val(data[i].user_name);
                    var arrTel = data[i].user_tel.split("-");
                    $j("#user_tel1").val(arrTel[0]);
                    $j("#user_tel2").val(arrTel[1]);
                    $j("#user_tel3").val(arrTel[2]);
                    $j("#shopname").val(data[i].shopname);
                    $j("#etc").val(data[i].etc);
                    $j("#memo2").val(data[i].memo);
                    $j("#res_coupon").val(data[i].res_coupon);
                }

                var TimeDate = "";
                if ((data[i].sub_title == "lesson" || data[i].sub_title == "pkg") && data[i].sub_title != "") {
                    TimeDate = '강습시간 : ' + data[i].res_time;
                }

                var ResNum = "";
                if (data[i].res_m > 0) {
                    ResNum = "남:" + data[i].res_m + "명";
                }
                if (data[i].res_m > 0 && data[i].res_w > 0) {
                    ResNum += ",";
                }
                if (data[i].res_w > 0) {
                    ResNum += "여:" + data[i].res_w + "명";
                }

                var ResOptInfo = "";
                var optinfo = data[i].optsubname;
                if (data[i].sub_title == "lesson") {
                    var arrdate = data[i].res_date.split("-"); // 들어온 날짜를 년,월,일로 분할해 변수로 저장합니다.
                    var s_Y = arrdate[0]; // 지정된 년도 
                    var s_m = arrdate[1]; // 지정된 월
                    var s_d = arrdate[2]; // 지정된 요일

                    var stayPlus = data[i].stay_day; //숙박 여부
                    //이전일 요일구하기
                    // $preDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." -1 day"));
                    // $nextDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." +1 day"));
                    if (stayPlus == 0) {
                        ResOptInfo = "숙박일 : " + data[i].res_date + "(1박)";
                    } else if (stayPlus == 1) {
                        ResOptInfo = "숙박일 : " + plusDate(data[i].res_date, -1) + "(1박)";
                    } else if (stayPlus == 2) {
                        ResOptInfo = "숙박일 : " + plusDate(data[i].res_date, -1) + "(2박)";
                    } else {

                    }
                } else if (data[i].sub_title == "rent") {

                } else if (data[i].sub_title == "pkg") {
                    ResOptInfo = optinfo;
                } else if (data[i].sub_title == "bbq") {

                }

                var rtn_totalprice = parseInt(data[i].rtn_totalprice, 10);
                var RtnBank = '';
                if (data[i].res_confirm == 4) { //환불요청금액
                    RtnTotalPrice += rtn_totalprice;
                } else if (data[i].res_confirm == 5) { //환불완료금액
                    RtnTotalPrice2 += rtn_totalprice;
                }

                if (data[i].res_confirm == 4 || data[i].res_confirm == 5) {
                    var RtnPrice = commify(rtn_totalprice) + '원';

                    if (data[i].rtn_bankinfo == null || data[i].rtn_bankinfo == "") {
                        data[i].rtn_bankinfo = "";
                    } else {
                        data[i].rtn_bankinfo = data[i].rtn_bankinfo.replace(/\|/g, "&nbsp;");
                    }
                    RtnBank = '<span>' + data[i].rtn_bankinfo + '<br>환불액 : ' + RtnPrice + '</span></td>';
                }

                var ResConfirm0 = '';
                var ResConfirm1 = '';
                var ResConfirm2 = '';
                var ResConfirm3 = '';
                var ResConfirm4 = '';
                var ResConfirm5 = '';
                var ResConfirm6 = '';
                var ResConfirm7 = '';
                var ResConfirm8 = '';

                if (data[i].res_confirm == 0) ResConfirm0 = 'selected';
                if (data[i].res_confirm == 1) ResConfirm1 = 'selected';
                if (data[i].res_confirm == 2) ResConfirm2 = 'selected';
                if (data[i].res_confirm == 3) ResConfirm3 = 'selected';
                if (data[i].res_confirm == 4) ResConfirm4 = 'selected';
                if (data[i].res_confirm == 5) ResConfirm5 = 'selected';
                if (data[i].res_confirm == 6) ResConfirm6 = 'selected';
                if (data[i].res_confirm == 7) ResConfirm7 = 'selected';
                if (data[i].res_confirm == 8) ResConfirm8 = 'selected';

                var statehtml = "" +
                    "<select id='selConfirm' name='selConfirm[]' resnum='$MainNumber' class='select' style='padding:1px 2px 4px 2px;' onchange='fnChangeModify(this, $ResConfirm);'>" +
                    "	<option value='0' " + ResConfirm0 + ">미입금</option>" +
                    "	<option value='1' " + ResConfirm1 + ">예약대기</option>" +
                    "	<option value='2' " + ResConfirm2 + ">임시확정</option>" +
                    "	<option value='3' " + ResConfirm3 + ">확정</option>" +
                    "	<option value='4' " + ResConfirm4 + ">환불요청</option>" +
                    "	<option value='5' " + ResConfirm5 + ">환불완료</option>" +
                    "	<option value='6' " + ResConfirm6 + ">임시취소</option>" +
                    "	<option value='7' " + ResConfirm7 + ">취소</option>" +
                    "	<option value='8' " + ResConfirm8 + ">입금완료</option>" +
                    "</select>";

                var rowhtml = "" +
                    "<tr rowadd='1'>" +
                    "	<td><input type='hidden' id='MainNumber' name='MainNumber' value='" + data[i].res_date + "'>" +
                    "		<label>" +
                    "		<input type='checkbox' id='chkCancel' name='chkCancel[]' checked='true' value='" + data[i].ressubseq + "' style='vertical-align:-3px;display:none;' />" + data[i].res_date + "</label>" +
                    "	</td>" +
                    "	<td>" + data[i].optname + "</td>" +
                    "	<td><span class='resoption' style='color:black;'>" + TimeDate + " (" + ResNum + ")</span>" +
                    "		<span class='resoption' style='color:black;'>" + ResOptInfo + "</span></td>" +
                    "	<td>" + statehtml + "</td>" +
                    "	<td>" + RtnBank + "</td>" +
                    "</tr>";

                $j("#trlist").append(rowhtml);
            }

            if (RtnTotalPrice > 0 || RtnTotalPrice2 > 0) {
                var rtn1 = "<span style='color:red;'>환불요청 : <b>" + commify(RtnTotalPrice) + "</b>원</span><br>";
                var rtn2 = "<span style='color:#808080;'>환불완료 : <b>" + commify(RtnTotalPrice2) + "</b>원</span>";

                var rowhtml = "" +
                    "<tr rowadd='1'>" +
                    "	<td colspan='3'></td>" +
                    "	<th>총 환불금액</th>" +
                    "	<td>" + rtn1 + rtn2 + "</td>" +
                    "</tr>";
                $j("#trlist").append(rowhtml);
            }
        }
    });
}


function fnSurfDataAdd(gubun) {
    //공백 제거
    // fnFormTrim("#frmModify");

    if ($j("#user_name").val() == "") {
        alert("예약자이름을 입력하세요~");
        return;
    }

    if ($j("#user_tel1").val() == "" || $j("#user_tel2").val() == "" || $j("#user_tel3").val() == "") {
        alert("연락처를 입력하세요~");
        return;
    }

    //$j("#resparam").val(gubun);

    var text1 = "예약상태 변경처리 하시겠습니까?";
    var text2 = "변경처리가 완료되었습니다.";

    if (!confirm(text1)) {
        return;
    }

    //frmModify
    var formData = $j("#frmModify").serializeArray();
    $j.post("/act/admin/shop/res_kakao_save.php", formData,
        function(data, textStatus, jqXHR) {
            if (data == 0) {
                alert(text2);

                fnCalMoveAdminList($j(".tour_calendar_month").text().replace('.', ''), 0, -1);
                fnSearchAdmin("act_admin/res_surflist_search.php");
                fnModifyClose();
            } else {
                var arrRtn = data.split('|');
                if (arrRtn[0] == "err") {
                    alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요.");
                } else {}
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {});
}