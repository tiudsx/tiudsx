function fnCouponCheck(obj){
	if($j("#shopseq").val() == 15){
		var couponType = "BBQ";
	}else{
		var couponType = "SUR";
	}
	var cp = fnCoupon(couponType, "load", $j("#coupon").val());
	if(cp > 0){
		$j("#coupondis").css("display", "");
		$j("#couponcode").val($j("#coupon").val())
		$j("#couponprice").val(cp);

		if(cp <= 100){ //퍼센트 할인
			$j("#coupondis").html("<br>적용쿠폰코드 : " + $j("#coupon").val() + "<br>총 결제금액에서 "+ cp + "% 할인");
		}else{ //금액할인
			$j("#coupondis").html("<br>적용쿠폰코드 : " + $j("#coupon").val() + "<br>총 결제금액에서 "+ commify(cp) + "원 할인");
		}
	}else{
		if($j("#coupon").val() == "ATBLOG"){
			$j("#coupondis").html("<br>적용쿠폰코드 : " + $j("#coupon").val() + "<br>체험단 할인코드");

			$j("#coupondis").css("display", "");
			$j("#couponcode").val($j("#coupon").val())
		}else{
			$j("#coupondis").css("display", "none");
			$j("#coupondis").html("");
			$j("#couponcode").val("")
		}

		$j("#couponprice").val(0);
	}
	$j("#coupon").val("");

	fnTotalPrice();
}