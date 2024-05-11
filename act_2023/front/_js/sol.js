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

                    rtnVlu += "<tr trid='stay'><td>" 
                    + arrData[0] 
                    + "</td><td>" + arrData[1] + " ~ <br>" + arrData[2] 
                    + "</td><td>" + arrData[3].substring(0, 1) + "층 " 
                    + arrData[3].substring(2) + "호</td><td>" 
                    + arrData[4] + "번 침대</td><td>" + arrData[5] + "</td></tr>";
                }

                $j("#tbStay").append(rtnVlu);
                //alert("객실조회가 완료되었습니다.\n\n호실,침대번호,도어락 비밀번호를 확인 후 입실해주세요~");
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

function fnSolInfo(obj, num){
    $j("#btnList input").attr("class", "btn_default");
    $j(obj).attr("class", "btnsurfadd");

    $j("div[name=tabinfo]").css("display", "none");
    $j("div[name=tabinfo]").eq(num).css("display", "");
}

function shareMessage(resNumber, bankUserName) {
	let strMsg = "안녕하세요.\n솔게하&솔서프에서 안내드립니다.";
	strMsg += "\n\n" + bankUserName + "님께서 예약하신 이용안내 정보를 공유해드립니다.\n\n예약정보에서 내용 확인 후 이용 부탁드려요~ :)";
	Kakao.Share.sendDefault({
		objectType: 'text',
		text: strMsg,
		link: {
			// [내 애플리케이션] > [플랫폼] 에서 등록한 사이트 도메인과 일치해야 함
			mobileWebUrl: 'https://actrip.co.kr',
			webUrl: 'https://actrip.co.kr',
		},
		buttons: [{
			title: '예약정보',
			link: {
				mobileWebUrl: 'https://actrip.co.kr/sol_kakao?chk=1&seq=' + resNumber,
				webUrl: 'https://actrip.co.kr/sol_kakao?chk=1&seq=' + resNumber,
			},
		}],
	});
}