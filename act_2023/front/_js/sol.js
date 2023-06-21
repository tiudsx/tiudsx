function fnStaySearch(resseq) {
    var params = "resparam=solstay&resseq=" + resseq;
    $j.ajax({
        type: "POST",
        url: "/act_2023/admin/sol/list_info.php",
        data: params,
        success: function(data) {
            if (data == 1) {
                alert("오후 3시 이후에 객실 조회가 가능합니다.");
            } else if (data == 2) {
                alert("예약하신 이용 당일에 조회가 가능합니다.");
            } else {
                var rtnVlu = "";
                for (let i = 0; i < data.length; i++) {
                    $j("tr[trid='stay']").remove();
                    var arrData = data[i].split("|");

                    rtnVlu += "<tr trid='stay'><td>" + arrData[0] + "</td><td>" + arrData[1] + " ~ <br>" + arrData[2] + "</td><td>" + arrData[3] + "호</td><td>" + arrData[4] + "번 침대</td><td>" + arrData[5] + "</td></tr>";
                }

                $j("#tbStay").append(rtnVlu);
                alert("객실조회가 완료되었습니다.\n\n호실,침대번호,도어락 비밀번호를 확인 후 입실해주세요~");
                $j(".SolLayer").css("display", "none");
                $j("#staysearch").css("display", "none");
                $j("#staysearch2").css("display", "");
            }
        }
    });
}

function fnResViewSol(bool, objid, topCnt, obj) {
    $j(".vip-tabnavi li").removeClass("on");
    $j(obj).addClass("on");

    if (bool) {

    } else {
        $j("div[tabid='viewtab']").css("display", "none");
        $j(objid).css("display", "");
    }

    fnMapView(objid, topCnt);
}