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