$j(function() {
    $j("ul.tabs li").not("#click").click(function() {
        $j("ul.tabs li").not("#click").removeClass("active").css("color", "#333");
        $j(this).addClass("active").css("color", "darkred");
        $j("div[class=tab_content]").css('display', 'none');
        var activeTab = $j(this).attr("rel");

        $j("#" + activeTab).css('display', 'block');
    });
});

function fnBlockClose() {
    $j.unblockUI();
}

/**
 * 예약조회
 * @param {*} url 
 * @param {*} objID 
 * @param {*} objForm 
 */
function fnSearchAdmin(url, objID, objForm) {
    $j.blockUI({ message: "<br><br><br><h1>데이터 조회 중...</h1>", focusInput: false, css: { width: '650px', height: "150px", textAlign: 'center', left: '23%', top: '20%' } });

    if(objID == null) objID = "#mngSearch";
    if(objForm == null) objForm = "#frmSearch";

    var formData = null;
    if(objForm != 'N'){
        formData = $j("#frmSearch").serializeArray();
    }

    $j.post("/act_2023/admin/" + url, formData,
        function(data, textStatus, jqXHR) {
            $j(objID).html(data);
            setTimeout('fnBlockClose();', 500);
        }).fail(function(jqXHR, textStatus, errorThrown) {
        setTimeout('fnBlockClose();', 500);
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

    if (seq == 0 || seq == -3) { //서핑버스 관리자
        $j('input[id=chkbusNumY1]').prop('checked', true);
        $j('input[id=chkbusNumY2]').prop('checked', true);
        $j('input[id=chkbusNumD1]').prop('checked', true);
        $j('input[id=chkbusNumD2]').prop('checked', true);
        $j('#chkBusY1').prop('checked', true);
        $j('#chkBusY2').prop('checked', true);
        $j('#chkGubun').prop('checked', false);

        var folderName = "bus";
        if(seq == -3){
            folderName = "busDrive";
        }
        $j("#divResList").load("/act_2023/admin/" + folderName + "/list_mng.php?selDate=" + selDate);
        $j("#initText2").css("display", "none");
        var url = folderName + "/list_search.php";
    } else if (seq == -2) { //서핑버스 등록
        fnBusMngList(selDate);
        return;
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

function fnDateReset() {
    $j("#sDate").val('');
    $j("#eDate").val('');
}

function fnCalMoveAdminList(selDate, day, seq) {
    var nowDate = new Date();

    if (seq == 0 || seq == -2 || seq == -3) { //서핑버스
        $j("#divResList").html("");
        $j("#initText2").css("display", "");

        if (seq == 0) { //양양, 동해 셔틀버스
            var calurl = "bus/_calendar.php";
        }else if (seq == -2) { //서핑버스 등록관리
            var calurl = "busMng/_calendar.php";
        }else if (seq == -3) { //서핑버스 등록관리
            var calurl = "busDrive/_calendar.php";
        }
    } else if (seq == -1) { //입점샵 전체
        var url = "act_admin/res_surflist_search.php";
        var calurl = "act_admin/res_surfcalendar.php";
    } else { //입점샵 일반
        var url = "shop/res_surflist_search.php";
        var calurl = "shop/res_surfcalendar.php";
    }

    $j("#right_article3").load("/act_2023/admin/" + calurl + "?selDate=" + selDate + "&selDay=" + day + "&seq=" + seq + "&t=" + nowDate.getTime());
}

function fnChkAll(obj, objid) {
    if ($j(obj).is(":checked")) {
        $j('input[id=' + objid + ']').prop('checked', true);
    } else {
        $j('input[id=' + objid + ']').prop('checked', false);
    }
}

function spacetrim(obj){
    var trim = $j.trim($j(obj).val()).replace(/ /g, '');
    $j(obj).val(trim);
}