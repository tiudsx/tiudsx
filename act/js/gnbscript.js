$j.ajaxSetup({
	async: false
});

/* Hamburger Menu */
$j(".menu").click(function() {
	$j(".menu").toggleClass("active");
	$j(".navbar-menu").toggleClass("active");
	
	if($j(".menu").hasClass("active")){
		$j('body').block({ message: null }); 
		$j(".layG_kakao").css("display", "none");
		$j(".con_footer").css("display", "none");
	}else{
		$j('body').unblock(); 
		$j(".layG_kakao").css("display", "");

		if($j("#view_tab3").css("display") == "block"){
			$j(".con_footer").css("display", "none");
		}else{
			$j(".con_footer").css("display", "block");			
		}
	}
});
/* End */

// 천단위마다 쉼표 넣기
function commify(n) {
    var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
    n += '';                          // 숫자를 문자열로 변환

    while (reg.test(n)) {
        n = n.replace(reg, '$1' + ',' + '$2');
    }

    return n;
}

function fnRtnText(data, type){
	if(data == "0"){
		alert("정상적으로 처리되었습니다.");
		return true;
	}else{
		alert("처리 중 에러가 발생하였습니다.\n\n관리자에게 문의하세요.");	   
		return false;
	}
 }