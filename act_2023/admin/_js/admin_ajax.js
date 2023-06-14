$j(function () {
	
});

function fnChkRev(type) {

	//alert(type);

	var params = "resparam=test";
	$j.ajax({
		type: "POST",
		url: "/act_2023/admin/bus/list_ajax.php",
		data: params,
		success: function (data) {
			if (data == "err") {
				alert("오류가 발생하였습니다.");
			} else {
				//alert(data);
			}
		}
	});

}
