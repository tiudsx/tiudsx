function shareMessage(resNumber, bankUserName) {
	let strMsg = "안녕하세요.\n액트립에서 안내드립니다.";
	strMsg += "\n\n" + bankUserName + "님께서 예약하신 셔틀버스 정보를 공유해드립니다.\n\n예약정보에서 내용 확인 후 이용 부탁드려요~ :)";
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
				mobileWebUrl: 'https://actrip.co.kr/order_kakao?resNumber=' + resNumber,
				webUrl: 'https://actrip.co.kr/order_kakao?resNumber=' + resNumber,
			},
		}],
	});
}

function fnOrderSearch(num) {
    if ($j.trim($j("#resNumber").val()) == "") {
        alert("예약번호를 입력하세요.");
        return;
	}

	location.href = "/orderview?resNumber=" + $j.trim($j("#resNumber").val().replace(/ /g, ''));
}

//취소/환불 신청
function fnRefund(gubun) {
	if($j("input[id=chkCancel]:checked").length == 0){
		alert("선택된 항목이 없습니다.");
		return;
	}

	if($j("#hidtotalPrice").val() > 0){
		if($j("#bankName").val() == ""){
			alert("은행이름을 입력하세요.");
			return;
		}

		if($j("#bankNum").val() == ""){
			alert("계좌번호를 입력하세요.");
			return;
		}
	}

	var msg = "취소/환불 신청 하시겠습니까?";
    if (confirm(msg)) {
		$j('.top_area_zone').block({ message: "취소 및 환불신청 접수 중입니다." }); 
		var formData = $j("#frmCancel").serializeArray();

		$j.post("/act_2023/front/order/order_return.php", formData,
			function(data, textStatus, jqXHR){
				setTimeout("fnRtnMove('" + data + "', " + gubun + ");", 700);
			}
		)
		.fail(function() {
			setTimeout("$j('.top_area_zone').unblock();", 1500);
		})
		.always(function() {
			setTimeout("$j('.top_area_zone').unblock();", 1500);
		});
    }
}

function fnRtnMove(data, num){
	if(fnRtnText(data, 0)){
		location.reload();
		if(gubun == 1){
			//window.location.href = "/";
		}else{
			//location.reload();
		}
	}
}

//환불 수수료 계산
function fnCancelSum(obj, gubun, MainNumber){
	var chkVlu = $j("input[id=chkCancel]:checked").map(function () { return $j(this).val(); }).get();

	$j("#tdCancel1").html("0");
	$j("#tdCancel2").html("0");
	$j("#tdCancel3").html("0");
	$j("#hidtotalPrice").val("0");
	$j("#gubun").val(gubun);
	$j("#MainNumber").val(MainNumber);

	if(chkVlu == ""){
		$j('#bankName').val('');
		$j('#bankUserName').val('');
		$j('#bankNum').val('');
		$j('#returnBank').css('display', 'none');
	}else{
		var resParam = "RtnPrice";
		var formData = {"resparam":resParam, "gubun":gubun, "subintseq":"'" + chkVlu + "'"};
		$j.post("/act_2023/front/order/order_return.php", formData,
			function(data, textStatus, jqXHR){
			   if(data == "0"){
				   alert("환불 수수료 계산 중 오류가 발생하였습니다.\n\n다시 체크 하시거나 관리자에게 문의주세요.");
				   $j("input[id=chkCancel]").prop("checked", false);
			   }else{
				   var arrData = data.split('|');

					$j("#tdCancel1").html(commify(arrData[0]));
					$j("#tdCancel2").html(commify(arrData[1]));
					$j("#tdCancel3").html(commify(arrData[2]));
					$j("#hidtotalPrice").val(arrData[2]);

					if(arrData[2] > 0){
						$j('#returnBank').css('display', '');
					}else{
						$j('#bankName').val('');
						$j('#bankUserName').val('');
						$j('#bankNum').val('');
						$j('#returnBank').css('display', 'none');
					}

			   }
			}).fail(function(jqXHR, textStatus, errorThrown){
		 
		});
	}
}

function fnPointChangeSave() {
	var msg = "정류장 변경 신청을 하시겠습니까?";
    if (confirm(msg)) {
		$j('.top_area_zone').block({ message: "정류장 변경 신청 접수 중입니다." }); 

		$j("#frmPoint").attr("action", "/act_2023/front/order/order_return.php").submit();
    }
}