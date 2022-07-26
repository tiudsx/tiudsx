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

    var params = "resparam=reskakao&username=" + $j("#username").val() + "&userphone=" + $j("#userphone").val() + "&reschannel=" + $j("#reschannel").val() + "&resDate1=" + $j("#resDate1").val() + "&resDate2=" + $j("#resDate2").val() + "&resbusseat1=" + $j("#resbusseat1").val() + "&resbusseat2=" + $j("#resbusseat2").val();
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