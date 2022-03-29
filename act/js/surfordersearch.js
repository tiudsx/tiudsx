function fnOrderSearch(num) {
    if ($j.trim($j("#resNumber").val()) == "") {
        alert("예약번호를 입력하세요.");
        return;
	}

	location.href = "/orderview?resNumber=" + $j.trim($j("#resNumber").val().replace(/ /g, ''));
    //$j("#surfSelOk").load("/act/order/surforder_ok.php?num=" + num + "&resNumber=" + $j.trim($j("#resNumber").val().replace(/ /g, '')));
}

function fnOrderDisplay(gubun) {
	if(gubun == 1){
		$j("#surfSel").css("display", "none");
		$j("#surfSelOk").css("display", "");
	}else{
		$j("#surfSel").css("display", "");
		$j("#surfSelOk").css("display", "none");		
	}
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

	var msg = "신청 하시겠습니까?";
    if (confirm(msg)) {
		//$j("#frmCancel").attr("action", "/act/surf/surf_return.php").submit();
		$j('.top_area_zone').block({ message: "취소 및 환불신청 접수 중입니다." }); 
		var formData = $j("#frmCancel").serializeArray();

		$j.post("/act/surf/surf_return.php", formData,
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
		//fnOrderSearch(num);
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
		$j.post("/act/surf/surf_return.php", formData,
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
		// var formData = $j("#frmPoint").serializeArray();

		// $j.post("/act/surf/surf_return.php", formData,
		// 	function(data, textStatus, jqXHR){
		// 		setTimeout("location.href='/ordersearch?resNumber=" + $j("#MainNumber").val() + "';", 700);
		// 	}
		// )
		// .fail(function() {
		// 	setTimeout("$j('.top_area_zone').unblock();", 1500);
		// })
		// .always(function() {
		// 	setTimeout("$j('.top_area_zone').unblock();", 1500);
		// });

		$j("#frmPoint").attr("action", "/act/surf/surf_return.php").submit();
    }
}