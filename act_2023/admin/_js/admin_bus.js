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

function fnBusCancel() {
    if (!confirm("취소안내를 발송 하시겠습니까?")) {
        return;
    }

    var formData = $j("#frmCancel").serializeArray();
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

//클룩, 프립 데이터 맵핑
function fnChannel(obj){
    $j("#resbus option").show();
	if (obj.value == "11" || obj.value == "17" || obj.value == "20" || obj.value == "21" || obj.value == "22" || obj.value == "16" || obj.value == "7" ){
        $j("#fripMapping").show();
    }else{
        $j("#fripMapping").hide();
    }

    if(obj.value == "17" || obj.value == "20"){ //양양
        $j("#resbus option").eq(1).hide();
        $j("#resbus").val("YY");
    }else if(obj.value == "21" || obj.value == "22" || obj.value == "23"){ //동해
        $j("#resbus option").eq(0).hide();
        $j("#resbus").val("DH");
    }
}

//클룩, 프립 데이터 맵핑
function fnGetJson(obj){
    var vlu = $j("#reschannel").val();
    if($j("#html_1").val() == ""){
        alert("데이터 맵핑 자료를 넣으세요.");
        return;
    }

    if(!confirm("맵핑 후 알림톡 전송을 하시겠습니까?")){
        return;
    }

	if (vlu == "11" || vlu == "17" || vlu == "20" || vlu == "21" || vlu == "22"){
        fnMakeJsonFrip(); //프립
    }else if(vlu == "16"){
        fnMakeJsonKlook(); //클룩
	} else if (vlu == "7") {
		fnMakeJsonNaver(); //네이버쇼핑
	}
	
}

//프립 데이터 맵핑
function fnMakeJsonFrip() {
    //복사된 html을 가공 table[class='el-table__body']
    var strHtml = $j("#html_1").val().replace(/<!---->/gi,"");
    //$j("#divCopy").html($j("#divCopy").find("table[class='el-table__body']").html());

    //Json 인스턴스
    var objList = new Array();
    var objTitle = "";
    var objValue = new Object();

    var strTitle = strHtml.substring(strHtml.indexOf('mx-4 text-sm') + 14, strHtml.lastIndexOf('row-padding'));
    $j("#divCopy").html(strTitle.substring(0, strTitle.lastIndexOf('</div>') + 6));
    //상품명
    $j("#divCopy .row-padding").each(function(){
        if($j(this).find(".col-md-2").text() == "상품"){
            objTitle = $j(this).find(".col-md-2").next().text();
        }
    });

    $j("#divCopy").html(strHtml.substring(strHtml.indexOf('<table'), strHtml.lastIndexOf('</table>') + 8));
    //html 생성
    $j("#divCopy .el-table__row").each(function(){

        objValue = new Object();

        objValue.title = objTitle;
        if(objTitle.indexOf("서프팩토리") > 0){ // 서프팩토리

        }else if(objTitle.indexOf("마린서프") > 0){ // 마린서프

        }else if(objTitle.indexOf("인구서프") > 0){ // 인구서프

        }else if(objTitle.indexOf("1박2일") > 0){ // 힐링서프

        }else if(objTitle.indexOf("서울→양양") > 0){ // 서울>양양

        }else if(objTitle.indexOf("양양→서울") > 0){ // 양양>서울

        }else if(objTitle.indexOf("서울→동해(금진,대진)") > 0){ // 서울>동해

        }else if(objTitle.indexOf("동해(금진,대진)→서울") > 0){ // 동해>서울

        }

        objValue.name = $j(this).find("td").eq(1).find(".cell").text(); //이름
        objValue.genser = $j(this).find("td").eq(2).find(".cell").text(); //성별
        objValue.tel = $j(this).find("td").eq(3).find(".cell").text(); //연락처
        objValue.item = $j(this).find("td").eq(4).find(".cell").text(); //아이템명

        var objinfo = $j(this).find("td").eq(5).find(".cell span"); //추가정보    
        var addinfo = "";
        for (i = 0; i < objinfo.length; i++) {
            addinfo += objinfo[i].innerText + "|";
            
        }
        objValue.addinfo = addinfo;

        objValue.state = $j(this).find("td").eq(6).find(".cell").text(); //예약상태
        
        if ($j(this).find("button").length > 0) {
            objValue.btn = $j(this).find("button").text(); //액션
        }
        else{
            objValue.btn = 'none'; //액션
        }

        objList.push(objValue);
    });

    $j("#html_2").val(JSON.stringify(objList));

    //당일치기
    //[{"title":"[동해] 에메랄드빛 바다에서 당일치기 서핑해요! #서프팩토리","name":"망두1004","genser":" - ","tel":"01033657826","item":"[얼리버드] (여) 강습(보드+슈트) + 왕복셔틀","addinfo":"셔틀버스 노선 : 사당선 (셔틀버스 추가 배차될 경우 종로선 운행합니다)|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[동해] 에메랄드빛 바다에서 당일치기 서핑해요! #서프팩토리","name":"Nietzsche","genser":" 여성 ","tel":"01042077240","item":"[얼리버드] (여) 강습(보드+슈트) + 왕복셔틀","addinfo":"셔틀버스 노선 : 사당선 (셔틀버스 추가 배차될 경우 종로선 운행합니다)|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[동해] 에메랄드빛 바다에서 당일치기 서핑해요! #서프팩토리","name":"박선영","genser":" 여성 ","tel":"01033749239","item":"[얼리버드] (여) 강습(보드+슈트) + 왕복셔틀","addinfo":"셔틀버스 노선 : 사당선 (셔틀버스 추가 배차될 경우 종로선 운행합니다)|","state":"예약 대기","btn":" 예약건 취소 처리 "}]


    //힐링 캠프
    //[{"title":"[동해] 힐링 서핑캠프 #동거동락 #서핑트립 #1박2일 #MT","name":"욤희","genser":" - ","tel":"01037479816","item":"[얼리버드] 남/여|서핑강습(1회)+숙박+바베큐+왕복교통","addinfo":"이름을 입력해주세요 . : 한나영|성별이 어떻게 되시나요? : 여성|일요일 서핑강습 여부 : 미참여 (자유일정)|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[동해] 힐링 서핑캠프 #동거동락 #서핑트립 #1박2일 #MT","name":"영숙22","genser":" 여성 ","tel":"01027768162","item":"[얼리버드] 남/여|서핑강습(1회)+숙박+바베큐+왕복교통","addinfo":"이름을 입력해주세요 . : 허아현|성별이 어떻게 되시나요? : 여성|일요일 서핑강습 여부 : 미참여 (자유일정)|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[동해] 힐링 서핑캠프 #동거동락 #서핑트립 #1박2일 #MT","name":"권가은","genser":" 여성 ","tel":"01090281451","item":"[얼리버드] 남/여|서핑강습(1회)+숙박+바베큐+왕복교통","addinfo":"성별이 어떻게 되시나요? : 여성|일요일 서핑강습 여부 : 미참여|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[동해] 힐링 서핑캠프 #동거동락 #서핑트립 #1박2일 #MT","name":"연이2","genser":" 여성 ","tel":"01075539495","item":"[얼리버드] 남/여|서핑강습(1회)+숙박+바베큐+왕복교통","addinfo":"성별이 어떻게 되시나요? : 여성|일요일 서핑강습 여부 : 미참여|","state":"예약 대기","btn":" 예약건 취소 처리 "}]

    //셔틀버스
    //[{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"프립대원","genser":" 남성 ","tel":"01099850475","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"프립대원","genser":" 남성 ","tel":"01099850475","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"별님❤","genser":" 여성 ","tel":"01092942064","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"별님❤","genser":" 여성 ","tel":"01092942064","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"김윤희","genser":" 여성 ","tel":"01047495581","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"취소 완료","btn":"none"},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"김윤희","genser":" 여성 ","tel":"01047495581","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"취소 완료","btn":"none"},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"슈붕슈붕","genser":" 여성 ","tel":"01022481787","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"취소 완료","btn":"none"},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"슈붕슈붕","genser":" 여성 ","tel":"01022481787","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"취소 완료","btn":"none"}]
    console.log(objList);
}

//클룩 데이터 맵핑
function fnMakeJsonKlook() {
    $j("#divCopy").html($j("#html_1").val());
    $j("#divCopy").html($j("#divCopy").find(".booking-list-result").html());

    var $state = "";    //예약 상태영역
    var $info = "";     //예약 상세영역

    //Json 인스턴스
    var objList = new Array();
    var objValue = new Object();
    var colKey = "";
    var colValue = "";
    
    var colNameTitle = {
        "상품명":"prod_name",
        "패키지명":"prod_pkg",
        "단위":"ea",
        "이용시간":"bus_date",
        "전화번호":"user_tel",
        //"전화번호_2":"user_tel",
        "성":"user_name1",
        "이름":"user_name2",
        "성명":"user_fullname"
    }

    $j("#divCopy").find(".booking-item").each(function(){

        var objValue = new Object();

        //#region 예약 상태정보
        $state = $j(this).find(".boooking-general-info-operation");

        //예약확인ID
        objValue.res_id = $state.find(".info-item").eq(0).find("span").eq(1).text();
        //예약시간
        //objValue.res_time = $state.find(".info-item").eq(1).text().split(":")[1].trim().substring(0,10);
        //예약상태
        objValue.state = $state.find(".info-item").eq(2).find(".ant-tag").eq(0).text();

        //예약색상
        objValue.rgb = $state.find(".booking-color-tag").css("background-color");
        //#regionend

        /******************************************************************************/

        //#region 예약 상세정보
        $info = $j(this).find(".booking-info");

        $info.find("ul").each(function(){
            $j(this).find("li").each(function(){

                colKey = $j(this).find("p").eq(0).text().replace(":","").replace(/ /g, ''); //json Text
                colValue = $j(this).find("p").eq(1).text(); //json Value

                // if (objValue.user_tel_sub != undefined && colKey == "전화번호") {
                //     colKey = "전화번호_2";
                // }

                if (colNameTitle[colKey] != undefined) {
                    if(colNameTitle[colKey]  == "prod_name"){
                        if(colValue == "서울 - 양양 편도 or 왕복 서핑버스 (서피비치)"){
                            
                        }else if(colValue == "강원도 양양 셔틀버스 + 서핑 강습"){
                            
                        }else if(colValue == "양양 서핑 셔틀버스 + 강습"){
                            
                        }
                    }

                    if(objValue[colNameTitle[colKey]] == null){
                        objValue[colNameTitle[colKey]] = colValue;
                    }
                }
                
            });
        });

        objValue["username"] = "";      //고객명
        objValue["usertel"] = "";       //고객 연락처
        objValue["resbusseat2"] = "";      //인원수
        objValue["usedate"] = "";       //사용일
        objValue["bustypetext"] = "";   //버스상품명
        objValue["bustypevalue"] = "";  //버스상품타입  1:출발,2:복귀
        objValue["etc1"] = "";          //임시데이터 유무
        objValue["etc2"] = "";          //확정데이터 유무
        objValue["etc3"] = "";          //처리

        //#regionend

        objList.push(objValue);

    });

    $j("#html_2").val(JSON.stringify(objList));

    var i = 1;
	$j("#tbCopyList").html($j("#tbCopyList2").html());


    //예약 중복체크, 명단표 생성 로직
    fnChkRev(objList);
    return;

    objList.forEach(function(el) {
        var busGubun = "";
        var prod_pkg = $j.trim(el.prod_pkg)

        var resDate1 = "";
        var resDate2 = "";
        var resbusseat1 = "0";
		var resbusseat2 = "0";
		

        if(prod_pkg == "서울 사당 - 양양 (편도/ 토요일, 일요일)"){
            busGubun = "양양행(사당선)";
            resDate1 = el.bus_date;
            resbusseat1 = el.ea.replace("인원 x ", "").replace("인원수 x ", "");
        }else if(prod_pkg == "서울 종로 - 양양 (편도/토요일)"){
            busGubun = "양양행(종로선)";
            resDate1 = el.bus_date;
            resbusseat1 = el.ea.replace("인원 x ", "").replace("인원수 x ", "");
        }else if(prod_pkg == "[15시] 양양 - 서울  (편도)"){
            busGubun = "[15시] 양양>서울";
            resDate2 = el.bus_date;
            resbusseat2 = el.ea.replace("인원 x ", "").replace("인원수 x ", "");
        }else if(prod_pkg == "[17시] 양양 - 서울 (편도/ 토요일, 일요일)"){
            busGubun = "[17] 양양>서울";
            resDate2 = el.bus_date;
            resbusseat2 = el.ea.replace("인원 x ", "").replace("인원수 x ", "");
        }

        var user_name = el.user_fullname.replace(/ /gi, "");

        var tel = el.user_tel.replace("+82-", "").replace("82-", "");
        if(tel.substring(0, 1) != "0"){
            if(tel.length == 8){
                tel = "010" + tel;
            }else{
                tel = "0" + tel;
            }
        } 
        //tel = "01044370009";

        if(el.rgb == "rgb(255, 255, 255)"){
            var tbHtml = "<tr>"
                        + " <td>" + i + "</td>"
                        + " <td>" + busGubun + "</td>"
                        + " <td>" + user_name + "</td>"
                        + " <td>" + tel + "</td>"
                        + " <td>" + el.bus_date + " (" + el.ea.replace("인원 x ", "").replace("인원수 x ", "") + "명)</td>"
                        + " <td></td>"
						+ " <td></td>"
						+ " <td></td>"
                        + "</tr>";
            $j("#tbCopyList").append(tbHtml);
    
            i++;

            var resbus = "YY";
            var reschannel = "16";
			var params = "resparam=reskakao&username=" + user_name + "&resbus=" + resbus + "&userphone=" + tel + "&reschannel=" + reschannel + "&resDate1=" + resDate1 + "&resDate2=" + resDate2 + "&resbusseat1=" + resbusseat1 + "&resbusseat2=" + resbusseat2;

			/*알림톡 발송*/

            // $j.ajax({
            //     type: "POST",
            //     url: "/act_2023/admin/bus/list_save.php",
            //     data: params,
            //     success: function (data) {
            //         if(data == "err"){
            //             alert("오류가 발생하였습니다.");
            //         }
            //     }
            // });
        }
    });

    alert("처리 완료");

    fnSearchAdmin('bus/list_search_channel.php', '#mngKakaoSearch', 'N');

    // const result = objList.reduce((acc, v) => {
    //     return acc.includes(v) ? acc : [...acc, v];
    //   }, []);

    // console.log(result);

    // [{"res_id":"HCA813310","state":"확정됨","prod_name":"서울 - 양양 편도 or 왕복 서핑버스 (서피비치)","prod_pkg":"서울 사당 - 양양 (편도/ 토요일, 일요일) ","ea":"인원 x 1","bus_date":"2023-06-10","user_fullname":"한별 이","user_tel_sub":"82-01089199342","user_name1":"이","user_name2":"한별","user_tel":"+82-01089199342"}
    //,{"res_id":"ZMF207384","state":"취소됨","prod_name":"서울 - 양양 편도 or 왕복 서핑버스 (서피비치)","prod_pkg":"서울 사당 - 양양 (편도/ 토요일, 일요일) ","ea":"인원 x 2","bus_date":"2023-05-05","user_fullname":"박 세린","user_tel_sub":"***","user_name1":"박","user_name2":"세린","user_tel":"+82-01033836382"}
    //,{"res_id":"MAW511856","state":"취소됨","prod_name":"서울 - 양양 편도 or 왕복 서핑버스 (서피비치)","prod_pkg":"[17시] 양양 - 서울 (편도/ 토요일, 일요일)","ea":"인원수 x 2","bus_date":"2023-05-07","user_fullname":"박 세린","user_tel_sub":"***","user_name1":"박","user_name2":"세린","user_tel":"+82-01033836382"}
    //,{"res_id":"PRN058505","state":"확정됨","prod_name":"서울 - 양양 편도 or 왕복 서핑버스 (서피비치)","prod_pkg":"서울 사당 - 양양 (편도/ 토요일, 일요일) ","ea":"인원 x 2","bus_date":"2023-05-27","user_fullname":"-","user_tel_sub":"82-01049310092","user_tel":"+82-01049310092"}]
    console.log(objList);
}

//네이버쇼핑 맵핑
function fnMakeJsonNaver() {

	$j("#divCopy").html($j("#html_1").val());
	$j("#divCopy").html($j("#divCopy").find(".tui-grid-lside-area").html());

    alert("test??");

}

//서핑버스 정산
function fnCalMoveAdminCal(selDate, day) {
    var nowDate = new Date();
    $j("#tab3").load("/act_2023/admin/bus/list_cal.php?selDate=" + selDate + "&selDay=" + day + "&t=" + nowDate.getTime());

}
