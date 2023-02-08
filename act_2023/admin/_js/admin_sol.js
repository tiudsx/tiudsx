$j(function() {
    $j('.btnsurfadd').on('click', function(e){
        
        var $self = $j(this);
        var id = $self.data("gubun");

        if(id == "trbbq"){
            if($j("input[calid=res_bbqdate]").eq(1).val() == null || $j("input[calid=res_bbqdate]").eq(1).val() == ""){
                alert("첫번째열 바베큐 이용날짜를 선택하세요.");
                return;
            }

            $j("input[calid=res_bbqdate]").val($j("input[calid=res_bbqdate]").eq(1).val());
            $j("input[calid=res_bbqdate]").eq(0).val("");
            return;
        }

        //row 추가
        fnSolAdd(null, id, "");
    })
});

function fnSolpopupReset() {
    $j("#frmModify")[0].reset();
    $j("#SolAdd").css("display", "");
    $j("#SolModify").css("display", "none");

    $j("tr[rowadd=1]").remove();
}

function fnSolInsert() {
    $j('#res_modify').toggle();
}

function fnSolAdd(obj, id, num) {
    var date = (new Date()).yyyymmdd(); //오늘 날짜

    id = id + num;
    var objTr = $j("tr[id=" + id + "]").eq(0);

    $j("tr[id=" + id + "]:last").after(objTr.clone());
    $j("tr[id=" + id + "]:last").css("display", "")

    //숙소, 성별 디폴트 체크
    $j("tr[id=" + id + "]:last").find('#res_stayshopChk').prop("checked", true)
    $j("tr[id=" + id + "]:last").find('#res_staysexChk').prop("checked", true)
    
    //숙소, 성별 name 변경
    $j("tr[id=" + id + "]:last").find('input[name=res_stayshopChk]').attr("name", "res_stayshopChk_" + $j("tr[id=" + id + "]").length);
    $j("tr[id=" + id + "]:last").find('input[name=res_staysexChk]').attr("name", "res_staysexChk_" + $j("tr[id=" + id + "]").length);

    $j("tr[id=" + id + "]:last").find('input[cal=date]').removeClass('hasDatepicker').removeAttr('id').datepicker({
        //minDate : plusDate(date, -1)
        onClose: function(selectedDate) {
            var date = jQuery(this).datepicker('getDate');
            if (!(date == null)) {
                jQuery(this).next().select();
            }
        }
    });

    if(num == ""){
        $j("tr[id=" + id + "]:last").find('input[cal=sol_sdate]').removeClass('hasDatepicker').removeAttr('id').datepicker({
            beforeShow: function(date) {
                var date = jQuery(this).next().datepicker('getDate');

                if (!(date == null)) {
                    date.setDate(date.getDate() - 1); // Add 7 days
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

                    if (jQuery(this).next().val() == "") {
                        jQuery(this).next().val(plusDate(date.yyyymmdd(), 1));
                    }
                }
            },
            onSelect: function(dateText, inst) {
                fnSolAddInit($j(this));
            }

        });

        $j("tr[id=" + id + "]:last").find('input[cal=sol_edate]').removeClass('hasDatepicker').removeAttr('id').datepicker({
            beforeShow: function(date) {
                var date = jQuery(this).prev().datepicker('getDate');

                if (!(date == null)) {
                    date.setDate(date.getDate() + 1); // Add 7 days
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

                    if (jQuery(this).prev().val() == "") {
                        jQuery(this).prev().val(plusDate(date.yyyymmdd(), -1));
                    }
                }
            },
            onSelect: function(dateText, inst) {
                fnSolAddInit($j(this));
            }
        });
    }
    $j("tr[id=" + id + "]:last").attr("rowadd" + num, "1");
}

//객실 초기화
function fnSolAddInit(obj) {
    var objId = obj.parent().parent();
    objId.find("#res_stayroom").val("");
    objId.find("#res_staynum option").remove();
    objId.find("#res_staynum").append("<option value=''>-------</optoin>");
}

//바베큐 날짜 초기화
function fnSolDateDel(obj) {
    $j(obj).prev().val("");
}

function fnSolDel(obj) {
    $j(obj).parent().parent().remove();
}

function fnSolStaySel(obj) {
    var objId = $j(obj).closest("#trstay");
    
    objId.find("#res_stayshop").val($j(obj).val());
    if ($j(obj).val() == "N") {
        objId.find("input[calid=res_staysdate]").val("");
        objId.find("input[calid=res_stayedate]").val("");

        objId.find("#res_stayroom").val("");
        objId.find("#res_staynum option").remove();
        objId.find("#res_staynum").append("<option value=''>-------</optoin>");
    }
}

function fnSolSexSel(obj) {
    var objId = $j(obj).closest("#trstay");

    objId.find("#res_staysex").val($j(obj).val());
}

function fnSolSurfSel(obj) {
    var objId = $j(obj).closest("#trsurf");

    if ($j(obj).val() == "") {
        objId.find("#res_surfM").val("0");
        objId.find("#res_surfW").val("0");
    }
}

function fnSolSurfRentSel(obj) {
    var objId = $j(obj).closest("#trsurf");

    if ($j(obj).val() == "N") {
        objId.find("#res_rentM").val("0");
        objId.find("#res_rentW").val("0");
    }
}

function fnSolModify(resseq, num) {
    if(num == null){
        num = "";
        fnMapView('#containerTab', 40);
    }

    var params = "resparam=solview&resseq=" + resseq;
    $j.ajax({
        type: "POST",
        url: "/act_2023/admin/sol/list_info.php",
        data: params,
        success: function(data) {
            if(num == ""){
                fnSolpopupReset();

                if($j('#res_modify').css("display") == "none"){
                    fnSolInsert();
                }
            }else{
                $j("tr[rowadd" + num + "=1]").remove();

                $j.blockUI({
                    message: $j('#res_modify' + num),
                    focusInput: false,
                    css: { width: '90%', textAlign: 'left', left: '5%', top: '12%' }
                });
            }

            $j("#SolAdd").css("display", "none");
            $j("#SolModify").css("display", "");

            for (let i = 0; i < data.length; i++) {
                if (i == 0) {
                    if(num == ""){
                        $j("#insdate").text(data[i].insdate);
                        $j("#resseq").val(data[i].resseq);
                    }

                    $j("#res_adminname" + num).val(data[i].admin_user);
                    $j("#user_name" + num).val(data[i].user_name);
                    $j("#user_tel" + num).val(data[i].user_tel);
                    $j("#res_company" + num).val(data[i].res_company);
                    $j("#res_confirm" + num).val(data[i].res_confirm);
                    $j("#memo2" + num).val(data[i].memo2);
                }

                if (data[i].res_type == "stay") { //숙박&바베큐
                    fnSolAdd(null, 'trstay', num);

                    var objTr = $j("tr[id=trstay" + num + "]:last");

                    if(num == ""){
                        objTr.find("#stayseq").val(data[i].ressubseq);
                        objTr.find("#staytype").val("U");
                        objTr.find("#res_stayM").val(data[i].stayM);

                        if(data[i].staysex == "여"){
                            objTr.find("#res_staysexChk1").prop("checked", true);
                        } 
                    }
                    
                    objTr.find("#res_staysex" + num).val(data[i].staysex);     


                    if (data[i].prod_name != "N") {
                        if(num == ""){
                            objTr.find("#res_stayshopChk1").prop("checked", true);
                        }

                        objTr.find("#res_stayshop" + num).val(data[i].prod_name);
                        objTr.find("input[calid=res_staysdate" + num + "]").val(data[i].sdate);
                        objTr.find("input[calid=res_stayedate" + num + "]").val(data[i].edate);

                        if (data[i].stayroom != "") {
                            objTr.find("#res_stayroom" + num).val(data[i].stayroom);

                            if(num == ""){
                                objTr.find("#res_stayroom").attr("sel", data[i].stayroom);

                                fnRoomNum(objTr.find("#res_stayroom"), data[i].staynum);
                            }else{
                                var roombad = "번 (2층)";
                                if ((data[i].staynum % 2) == 1) {
                                    roombad = "번 (1층)";
                                }
                                objTr.find("#res_staynum" + num).val(data[i].staynum + roombad);
                            }
                        }
                    }
                    if (data[i].resdate != "0000-00-00") {
                        objTr.find("input[calid=res_bbqdate" + num + "]").val(data[i].resdate);
                    }
                } else { //강습&렌탈
                    fnSolAdd(null, 'trsurf', num);

                    var objTr = $j("tr[id=trsurf" + num + "]:last");
                    if(num == ""){
                        objTr.find("#surfseq").val(data[i].ressubseq);
                        objTr.find("#surftype").val("U");
                    }
                    objTr.find("input[calid=res_surfdate" + num + "]").val(data[i].resdate);

                    if (data[i].prod_name != "N") {
                        objTr.find("#res_surfshop" + num).val(data[i].prod_name);
                        objTr.find("#res_surftime" + num).val(data[i].restime);
                        objTr.find("#res_surfM" + num).val(data[i].surfM);
                        objTr.find("#res_surfW" + num).val(data[i].surfW);
                    }
                    if (data[i].surfrent != "N") {
                        objTr.find("#res_rent" + num).val(data[i].surfrent);
                        objTr.find("#res_rentM" + num).val(data[i].surfrentM);
                        objTr.find("#res_rentW" + num).val(data[i].surfrentW);
                    }
                }
            }
        }
    });
}

function fnSolDataAdd(gubun) {
    //공백 제거
    // fnFormTrim("#frmModify");
    if ($j("#user_name").val() == "") {
        alert("예약자이름을 입력하세요~");
        return;
    }

    if ($j("#user_tel").val() == "") {
        alert("연락처를 입력하세요~");
        return;
    }

    if ($j("input[id=res_stayshop]").length == 1 && $j("select[id=res_surfshop]").length == 1) {
        alert("숙박 및 서핑강습 신청 정보가 없습니다.");
        return;
    } else {
        for (let i = 1; i < $j("input[id=res_stayshop]").length; i++) {
            //바베큐 날짜
            if ($j("input[calid=res_bbqdate]").eq(i).val() == "") {
                $j("input[id=res_bbqdate]").eq(i).val("");
            }else{
                $j("input[id=res_bbqdate]").eq(i).val($j("input[calid=res_bbqdate]").eq(i).val());
            }

            if ($j("input[id=res_stayshop]").eq(i).val() == "N" && $j("input[id=res_bbqdate]").eq(i).val() == "") {
                alert(i + "번째 숙박/파티 중 하나이상 선택해주세요~");
                return;
            }
            
            if ($j("input[id=res_stayshop]").eq(i).val() == "N") {
                $j("input[calid=res_staysdate]").eq(i).val("");
                $j("input[calid=res_stayedate]").eq(i).val("");
            } else {
                if ($j("input[calid=res_staysdate]").eq(i).val() == "" || $j("input[calid=res_stayedate]").eq(i).val() == "") {
                    alert("숙박 이용 날짜를 선택해주세요~");
                    return;
                }

                $j("input[id=res_staysdate]").eq(i).val($j("input[calid=res_staysdate]").eq(i).val());
                $j("input[id=res_stayedate]").eq(i).val($j("input[calid=res_stayedate]").eq(i).val());
            }
        }

        for (let i = 1; i < $j("select[id=res_surfshop]").length; i++) {
            if ($j("input[calid=res_surfdate]").eq(i).val() == "") {
                alert(i + "번째 강습/렌탈 이용 날짜를 선택해주세요~");
                return;
            }

            if ($j("select[id=res_surftime]").eq(i).val() == "" && $j("select[id=res_rent]").eq(i).val() == "N") {
                alert(i + "번째 강습/렌탈 중 하나이상 선택해주세요~");
                return;
            }

            if ($j("select[id=res_surftime]").eq(i).val() != "" && ($j("select[id=res_surfM]").eq(i).val() == "0" && $j("select[id=res_surfW]").eq(i).val() == "0")) {
                alert(i + "번째 강습신청 인원을 선택해주세요~");
                return;
            }

            if ($j("select[id=res_rent]").eq(i).val() != "N" && ($j("select[id=res_rentM]").eq(i).val() == "0" && $j("select[id=res_rentW]").eq(i).val() == "0")) {
                alert(i + "번째 렌탈신청 인원을 선택해주세요~");
                return;
            }
        }
    }

    //$j("#resparam").val(gubun);

    var text1 = "예약등록을 하시겠습니까?";
    var text2 = "예약등록이 완료되었습니다.";
    if (gubun == "modify") {
        text1 = "수정을 하시겠습니까?";
        text2 = "수정이 완료되었습니다.";
    } else {
        $j("#resseq").val("");
    }

    if (!confirm(text1)) {
        return;
    }

    //frmModify
    var formData = $j("#frmModify").serializeArray();
    $j.post("/act_2023/admin/sol/list_save.php", formData,
        function(data, textStatus, jqXHR) {
            if (data == 0) {
                alert(text2);
                //location.reload();

                var selDate = $j("#listdate").text(); //달력 선택 날짜
                fnSearchAdminListSol(selDate);
                fnCalMoveAdminListSol($j(".tour_calendar_month").text().replace(".", ""));fnSolInsert();
                fnSolpopupReset();
            } else {
                var arrRtn = data.split('|');
                if (arrRtn[0] == "err") {
                    alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요." + "\n\n" + arrRtn[1]);
                    $j("#memo2").val(arrRtn[1]);
                } else {
                    alert(arrRtn[1] + "호 " + arrRtn[2] + "번 침대는 예약되어있습니다.\n\n다른 침대 및 호실을 선택해주세요~");
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {});
}

function fnRoomNum(obj, val) {
    var sdate = $j(obj).parent().parent().find("input[calid=res_staysdate]").val();
    var edate = $j(obj).parent().parent().find("input[calid=res_stayedate]").val();

    if (sdate == "" || edate == "") {
        alert("이용일을 선택해주세요.");
        $j(obj).val("");
        return;
    }

    var objNext = $j(obj).next();
    objNext.find("option").remove();

    //alert(sdate + " / " + edate + " / " + $j(obj).val() + " / " + $j(obj).attr("sel") + " / " + objNext.attr("sel"))

    var roomnum = 0;
    switch ($j(obj).val()) {
        case "201":
            roomnum = 8;
            break;
        case "202":
            roomnum = 10;
            break;
        case "203":
            roomnum = 6;
            break;
        case "204":
            roomnum = 8;
            break;
        case "301":
            roomnum = 6;
            //roomnum = 12;
            break;
        case "302":
            roomnum = 8;
            break;
        case "303":
            roomnum = 10;
            break;
    }

    objNext.append("<option value=''>-------</optoin>");
    if (roomnum == 0) {} else {
        for (var i = 1; i <= roomnum; i++) {
            var roombad = "번 (2층)";
            if ((i % 2) == 1) {
                roombad = "번 (1층)";
            }

            var sel = "";
            if (i == val) {
                sel = "selected";
                obj.attr("sel", val);
                //roombad += " - 기존"
            }

            objNext.append("<option value='" + i + "' " + sel + ">" + i + roombad + "</optoin>");
        }
    }

    if (val == "" || val == null) {
        //fnRoomBed(sdate, edate, $j(obj), objNext);
    }
    fnRoomBed(sdate, edate, $j(obj), objNext);
}

var arrRoom = {};
var arrRoomSeq = {};
var arrRoomSubSeq = {};

function fnRoomBed(sdate, edate, obj, objNext) {
    arrRoom = {};
    arrRoomSeq = {};
    arrRoomSubSeq = {};

    var roomnum = obj.val();
    var params = "resparam=solroom&res_staysdate=" + sdate + "&res_stayedate=" + edate + "&res_stayroom=" + roomnum;
    var stayseq = $j(obj).parent().parent().find("#stayseq").val();

    $j.ajax({
        type: "POST",
        url: "/act_2023/admin/sol/list_info.php",
        data: params,
        async: false,
        success: function(data) {
            if (data != null) {
                //alert(JSON.stringify(data));
                for (let i = 0; i < data.length; i++) {
                    if (arrRoom[roomnum + "_" + data[i].staynum] == null) {
                        arrRoom[roomnum + "_" + data[i].staynum] = 0;
                    } else {
                        arrRoom[roomnum + "_" + data[i].staynum]++;
                    }

                    if (arrRoomSeq[roomnum + "_" + data[i].staynum + "_" + data[i].resseq] == null) {
                        arrRoomSeq[roomnum + "_" + data[i].staynum + "_" + data[i].resseq] = 0;
                        arrRoomSubSeq[roomnum + "_" + data[i].staynum + "_" + data[i].resseq] = data[i].ressubseq;
                    } else {
                        arrRoomSeq[roomnum + "_" + data[i].staynum + "_" + data[i].resseq]++;
                    }
                }
                //alert(data.length + "\n\n" + params + "\n\n" + JSON.stringify(data) + "\n\n" + JSON.stringify(arrRoom) + "\n\n" + JSON.stringify(arrRoomSubSeq))
                $j.each(arrRoom, function(i, item) {

                    var vlu = i.split("_");
                    var key = arrRoomSeq[vlu[0] + "_" + vlu[1] + "_" + $j("#resseq").val()];
                    var keySub = arrRoomSubSeq[vlu[0] + "_" + vlu[1] + "_" + $j("#resseq").val()];
                    //alert(i + " / " + item);
                    var resText = "";
                    if (key == null || (item > 0 && arrRoomSeq[i + "_" + $j("#resseq").val()] != item)) {
                        resText = "불가";
                    } else if (key == 0) {
                        if (keySub == stayseq) {
                            resText = "기존";
                        } else {
                            resText = "기존(가능)";
                        }
                    } else {
                        resText = "기존(가능)"; //기존_불가
                    }
                    objNext.find("option[value='" + vlu[1] + "']").text(objNext.find("option[value='" + vlu[1] + "']").text() + " - " + resText);

                    // if($j("#resseq").val() == data[i].resseq){
                    //     objNext.find("option[value='" + data[i].staynum + "']").text(objNext.find("option[value='" + data[i].staynum + "']").text() + " - 기존 ");
                    // }else{
                    //     objNext.find("option[value='" + data[i].staynum + "']").text(objNext.find("option[value='" + data[i].staynum + "']").text() + " - 불가 ");
                    // }
                });
            }
        }
    });

}

//달력 날짜 클릭
function fnPassengerAdminSol(obj) {
    var selDate = obj.attributes.value.value;
    $j("#right_article3 calBox").css("background", "white");
    $j("calBox[sel=yes]").attr("sel", "no");
    $j(obj).css("background", "#efefef");
    $j(obj).attr("sel", "yes");

    fnSearchAdminListSol(selDate);
}

function fnCalMoveAdminListSol(selDate, day) {
    var nowDate = new Date();

    $j("#right_article3").load("/act_2023/admin/sol/_calendar.php?selDate=" + selDate + "&selDay=" + day + "&t=" + nowDate.getTime());
}

function fnSearchAdminListSol(selDate) {
    $j("#mnglist").css("display", "inline-block");
    $j("#mnglistStay").css("display", "none");
    $j("#mnglistSurf").css("display", "none");

    var formData = { "selDate": selDate };
    $j.post("/act_2023/admin/sol/list_search.php", formData,
        function(data, textStatus, jqXHR) {
            $j("#mnglist").html(data);
            $j("#roomdate").text($j("#listdate").text());

            if ($j("#hidrowcnt").length > 0 && $j("#hidrowcnt").val() != "") {
                var arrRowCnt = $j("#hidrowcnt").val().split('|');
                var nextrowCnt = 2;
                for (let i = 0; i < (arrRowCnt.length - 1); i++) {
                    var rowCnt = arrRowCnt[i];

                    if ((i % 2) == 1) {
                        $j("#tbSolList tr").eq(nextrowCnt).attr("class", "selTr2");
                    }

                    if (rowCnt > 1) {
                        $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(0).attr("rowspan", rowCnt);
                        $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(1).attr("rowspan", rowCnt);
                        $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(2).attr("rowspan", rowCnt);
                        $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(15).attr("rowspan", rowCnt);
                        $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(16).attr("rowspan", rowCnt);
                        $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(17).attr("rowspan", rowCnt);
                        $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(18).attr("rowspan", rowCnt);
                        $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(19).attr("rowspan", rowCnt);
                        $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(20).attr("rowspan", rowCnt);
                        $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(21).attr("rowspan", rowCnt);


                        for (let x = 1; x < rowCnt; x++) {
                            nextrowCnt++;
                            $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(21).remove();
                            $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(20).remove();
                            $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(19).remove();
                            $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(18).remove();
                            $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(17).remove();
                            $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(16).remove();
                            $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(15).remove();
                            $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(2).remove();
                            $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(1).remove();
                            $j("#tbSolList tr").eq(nextrowCnt).find('td').eq(0).remove();

                            if ((i % 2) == 1) {
                                $j("#tbSolList tr").eq(nextrowCnt).attr("class", "selTr2");
                            }
                        }
                    }
                    nextrowCnt++;
                }
            }
            //1|2|1|1|1|2|

            $j("td[room]").html("");
            $j("td[room]").prev().css("color", "#c0c0c0");
            $j("td[room]").removeAttr("onclick");
            $j("td[room]").css("cursor", "");

            if ($j("td[stayinfo]").length > 0) {
                for (let i = 0; i < $j("td[stayinfo]").length; i++) {
                    var arrInfo = $j("td[stayinfo]").eq(i).attr("stayinfo").split('|');
                    if (arrInfo[2] == "솔게하") {
                        //stayinfo='$user_name|$user_name|$prod_name|$staysex|$stayroom|$staynum|".$row['eDateDiff']."|$eDay|$resseq|$res_confirm'
                        var tbID = arrInfo[4];
                        // if(arrInfo[4] == "301"){
                        //     if(arrInfo[5] < 7){
                        //         tbID = "3011";
                        //     }else{
                        //         tbID = "3012";
                        //     }
                        // }else{

                        // }

                        //((arrInfo[6] > 1) ? "(" + arrInfo[6] + "박)" : "")
                        $j("#" + tbID + arrInfo[5]).attr("onclick", "fnSolModify(" + arrInfo[8] + ");");

                        if (arrInfo[9] == "확정") {
                            $j("#" + tbID + arrInfo[5]).prev().css("color", "black");
                            $j("#" + tbID + arrInfo[5]).css("color", "black").css("cursor", "pointer");
                            $j("#" + tbID + arrInfo[5]).html((($j("#" + tbID + arrInfo[5]).text() == "" ? "" : $j("#" + tbID + arrInfo[5]).text() + "<br>")) + arrInfo[0] + "(" + arrInfo[3] + ") / " + arrInfo[7] + "일" + "(" + arrInfo[6] + "박)");
                        } else {
                            $j("#" + tbID + arrInfo[5]).css("cursor", "pointer");
                            $j("#" + tbID + arrInfo[5]).html((($j("#" + tbID + arrInfo[5]).text() == "" ? "" : $j("#" + tbID + arrInfo[5]).text() + "<br>")) + arrInfo[0] + "(" + arrInfo[3] + ") / 대기");
                        }
                    }
                }
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
        alert(textStatus);

    });
}

function fnSearchAdminListTabSol(selDate, url) {
    var formData = { "selDate": selDate };
    $j.post("/act_2023/admin/sol/" + url, formData,
        function(data, textStatus, jqXHR) {
            $j("#mnglist").html(data);
        }).fail(function(jqXHR, textStatus, errorThrown) {
        alert(textStatus);

    });
}

//카톡 단일건 발송
function fnKakaoSend(resseq) {
    if (!confirm("알림톡 발송을 하시겠습니까?")) {
        return;
    }

    var formData = { "resparam": "solkakao1", "resseq": resseq };
    $j.post("/act_2023/admin/sol/list_save.php", formData,
        function(data, textStatus, jqXHR) {
            alert("알림톡 발송이 완료되었습니다.");
            $j("calbox[sel='yes']").click();
        }).fail(function(jqXHR, textStatus, errorThrown) {
        alert(textStatus);

    });
}

function fnKakaoCheckSend() {
    var chkVluY = $j("input[id=chkresseq]:checked").map(function() { return $j(this).val(); }).get();
    if (chkVluY == "") {
        alert("카톡 발송할 예약건을 선택해주세요.");
        return;
    }

    if (!confirm("알림톡 발송을 하시겠습니까?")) {
        return;
    }

    var formData = [{ "name": "resparam", "value": "solkakaoAll" }];
    $j("input[id=chkresseq]:checked").each(function(idx) {
        formData.push({ "name": "chkresseq[]", "value": $j(this).val() });
    });

    $j.post("/act_2023/admin/sol/list_save.php", formData,
        function(data, textStatus, jqXHR) {
            alert("알림톡 발송이 완료되었습니다.");
            $j("calbox[sel='yes']").click();
        }).fail(function(jqXHR, textStatus, errorThrown) {
        alert(textStatus);

    });
}

function fnListTab(gubun, obj) {
    var selDate = $j("#listdate").text(); //달력 선택 날짜

    if (gubun == "all") { //전체 탭
        fnSearchAdminListSol(selDate);
    } else if (gubun == "stay") { //숙박 탭
        fnSearchAdminListTabSol(selDate, "list_search_stay.php");
    } else if (gubun == "surf") { //강습 탭
        fnSearchAdminListTabSol(selDate, "list_search_surf.php");
    }
}

//숙박,강습 정보 엑셀 다운
function fnExcelDown() {
    var selDate = $j("#listdate").text(); //달력 선택 날짜

    if (!confirm("선택날짜 : " + selDate + "\n\n해당날짜의 정보를 엑셀다운로드 하시겠습니까?")) {
        return;
    }

    location.href = "/act_2023/admin/sol/_exceldown.php?selDate=" + selDate;
}

function fnRentYN(obj, subseq) {
    if (!confirm("렌탈 사용여부를 변경하시겠습니까?")) {
        if ($j(obj).val() == "Y")
            $j(obj).val("N");
        else
            $j(obj).val("Y");
        return;
    }

    var formData = { "resparam": "solrentyn", "subseq": subseq, "rentyn": $j(obj).val() };
    $j.post("/act_2023/admin/sol/list_save.php", formData,
        function(data, textStatus, jqXHR) {
            var selDate = $j("#listdate").text(); //달력 선택 날짜
            fnSearchAdminListTabSol(selDate, "list_search_surf.php");
        }).fail(function(jqXHR, textStatus, errorThrown) {
        alert(textStatus);
    });
}

function fnAllChk(obj) {
    $j("input[id=chkresseq]:not(:disabled)").prop("checked", $j(obj).is(':checked'));
}