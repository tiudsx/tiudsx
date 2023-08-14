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

//예약조회
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

    if (seq == 0 || seq == -3 || seq == -2) { //서핑버스 관리자
        $j('input[id=chkbusNumY1]').prop('checked', true);
        $j('input[id=chkbusNumY2]').prop('checked', true);
        $j('input[id=chkbusNumD1]').prop('checked', true);
        $j('input[id=chkbusNumD2]').prop('checked', true);
        $j('#chkBusY1').prop('checked', true);
        $j('#chkBusY2').prop('checked', true);
        $j('#chkBusD1').prop('checked', true);
        $j('#chkBusD2').prop('checked', true);
        $j('#chkGubun').prop('checked', false);

        if(seq == 0){
            var folderName = "bus_mohaeng";
        }else  if(seq == -2){
            var folderName = "bus_dh";
        }else{
            var folderName = "busDrive_dh";
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
            $j("#res_busdate").val($j("#hidselDate").val());
            if(data == 0){
                //row 추가
                //fnBusAdd("trbus");
            }else{
                for (let i = 0; i < data.length; i++) {
                    fnBusAdd("trbus");

                    var objTr = $j("tr[id=trbus]:last");

                    $j("#hidselDate").val(data[i].bus_date);

                    objTr.find("#resseq").val(data[i].dayseq);
                    objTr.find("#res_busdate").val(data[i].bus_date);
                    objTr.find("#res_busgubun").val(data[i].bus_gubun + "|" + data[i].bus_num + "|" + data[i].bus_name);
                    objTr.find("#res_seat").val(data[i].seat);
                    objTr.find("#res_gpsname").val(data[i].gpsname);
                    objTr.find("#res_useYN").val(data[i].useYN);
                    objTr.find("#res_channel").val(data[i].channel);
                }
                console.log(data);
            }
            setTimeout('fnBlockClose();', 500);
        }
    });
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
        
        if (seq == 0) { //서핑버스
            var calurl = "bus_mohaeng/_calendar.php";
        }else if(seq == -2){
            var calurl = "bus_dh/_calendar.php";
        }else if (seq == -3) { //서핑버스 등록관리
            var calurl = "busDrive_dh/_calendar.php";
        }
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