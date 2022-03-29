<? include 'db.php'; ?>

<link rel="stylesheet" type="text/css" href="/act/css/main.css?v=2">
<div id="wrap">

<!--visible / hidden-->
<div id="gpe_divpop1" style="top: 70px;padding:0 10px 0 10px; visibility: hidden;">
<form name="gpe_form1"><input type="hidden" name="error_return_url" value="/index"><input type="hidden" name="act" value=""><input type="hidden" name="mid" value="index"><input type="hidden" name="vid" value=""> 
	<div class="pop_area_out">
		<div class="pop_area_in">
			<div class="pop_middle">
				<a href="http://surfenjoy.com/surfres?seq=64"><img src="http://surfenjoy.cdn3.cafe24.com/shop/surfenjoy_new_4.jpg?v=4" alt="당찬패키지" class="placeholder"></a>
			</div>
			<div class="pop_bott">
				<div class="pop_bott_1day"><input name="event1" type="checkbox" value="checkbox2"></div>
				<div class="pop_bott_1day_txt">오늘 이 창 안뛰우기</div>
				<div class="pop_bott_close"><a href="javascript:gpe_closeWin1(1);"></a></div>
			</div>
		</div>
	</div>
</form>
</div>

<div id="gpe_divpop2" style="top: 190px;padding:0 10px 0 10px; visibility: hidden;">
	<div class="pop_area_out">
		<div class="pop_area_in">
			<div class="pop_middle">
				<img id="couponBg" src="images/coupon/couponBus.jpg?v=1" class="placeholder">
				<p id="coupontext"></p>   
			</div>
			<div class="pop_bott">
				<div class="pop_bott_close"><a href="javascript:gpe_closeCoupon();"></a></div>
			</div>
		</div>
	</div>
</div>

<script src="/act/js/popup.js"></script>
<script> 
	// var eventCookie=gpe_getCookie1("act_pop1");
	// if ( eventCookie != "no1" ){  
	// 	document.all['gpe_divpop1'].style.visibility = "visible";
	// } else if(eventCookie == "no1") {
	// 	document.getElementById('gpe_divpop1').style.display='none'; 
	// }

	
	var coupon = gpe_getCookie1("act_pop2");
	if(coupon){
		var couponObj = decodeURIComponent(coupon).split('|');
		var couponCode = couponObj[0];	//쿠폰 코드
		var couponType = couponObj[1];	//쿠폰 이미지 종류
		
		document.getElementById('coupontext').innerText = couponCode;
		if(couponType == "BUS"){
			document.getElementById('couponBg').src = "/act/images/coupon/couponBus.jpg?v=1";
		}else if(couponType == "SUR"){
			document.getElementById('couponBg').src = "/act/images/coupon/couponSurf.jpg";
		}else if(couponType == "BBQ"){
			document.getElementById('couponBg').src = "/act/images/coupon/couponBbq.jpg";
		}
		
		document.all['gpe_divpop2'].style.visibility = "visible";
		gpe_setCookie1( "act_pop2", coupon , -1 ); 
	}
</script>

<? include '_layout_top.php'; ?>

	<div class="top_area_zone">
		<div id="mainBox">
			<nav class="iconMenu">
				<ul>
					<li><a href="/surf"><img src="images/icon/isurf.png" alt="">
						</a>
					</li>
					<li><a href="/surfbus"><img src="images/icon/ibus.png" alt="">
						</a></li>
					<li><a href="/bbq"><img src="/act/images/icon/ibbq.png" alt=""></a></li>
					<!-- <li><a href="javascript:alert('서비스 준비중입니다.');"><img src="/act/images/icon/ibbq.png" alt=""></a></li> -->
					<li><a href="https://m.cafe.naver.com/ca-fe/web/cafes/29998302/menus/7" target="_blank"><img src="images/icon/ievent.png" alt="">
						</a></li>
					<li><a href="/staylist"><img src="images/icon/ibed.png" alt="">
						</a></li>
					<li><a href="/eatlist"><img src="images/icon/ifood.png" alt="">
						</a></li>
				</ul>
			</nav>
			<div class="sldBnr">
				<div class="swiper-container">
					<div class="swiper-wrapper">
						<div class="swiper-slide"><a href="https://smartstore.naver.com/actrip/products/5135333709" target="_blank"><img src="images/banner/main_banner1.jpg" alt=""></a></div>
						<div class="swiper-slide"><a href="https://cafe.naver.com/actrip/377" target="_blank"><img src="images/banner/levelup.jpg?v=1" alt=""></a></div>
						<div class="swiper-slide"><a href="https://cafe.naver.com/actrip/2097" target="_blank"><img src="images/banner/banefit.jpg" alt=""></a></div>
						<div class="swiper-slide"><a href="https://cafe.naver.com/actrip/376" target="_blank"><img src="images/banner/reviewSurf.jpg" alt=""></a></div>
						<div class="swiper-slide"><a href="https://cafe.naver.com/actrip/376" target="_blank"><img src="images/banner/reviewBus.jpg" alt=""></a></div>
					</div>
					<div class="swiper-pagination"></div>
				</div>
			</div>
			<section id="hashtag">
				<div class="hashtagInner">
					<h2>알면 알수록 좋은~</h2>
					<div class="tagBox">
						<a href="https://cafe.naver.com/actrip/2097" target="_blank"><span>액트립 할인쿠폰 <i class="fas fa-chevron-circle-right"></i></span><img src="images/mainImg/mainEvent.png" alt=""></a>
						<a href="/eatlist"><span>맛도락 제휴식당 <i class="fas fa-chevron-circle-right"></i></span><img src="images/mainImg/mainFood.png" alt=""></a>
						<a href="https://cafe.naver.com/ArticleList.nhn?search.clubid=29998302&search.menuid=40&search.boardtype=L" target="_blank"><span>이용후기 <i class="fas fa-chevron-circle-right"></i></span><img src="images/mainImg/mainReview.png" alt=""></a>
						<a href="https://cafe.naver.com/ArticleList.nhn?search.clubid=29998302&search.menuid=21&search.boardtype=W" target="_blank"><span>서핑정보/팁 <i class="fas fa-chevron-circle-right"></i></span><img src="images/mainImg/mainSurf.png" alt=""></a>
					</div>
					<div class="tag">
						<a>#액트립</a>
						<a>#여행은액티비티다</a>
						<a>#혜택빵빵</a>
						<a>#서핑배우기</a>
					</div>
				</div>
			</section>
			<section id="staticBnr">
				<a class="visual" href="/surfbus"><img src=images/banner/bnrBus.jpg></a>
			</section>
			<!-- <section id="popular">
				<header class="popTitle">
					<h2>인기 액티비티</h2>
				</header>
				<div class="actTab">
					<ul class="tabs">
						<li rel="tab1" class="active">추천</li>
						<li rel="tab2">양양</li>
						<li rel="tab3">고성</li>
						<li rel="tab4">동해</li>
					</ul>
					<div class="tabContainer">
						<div id="tab1" class="tabContent">
							<ul>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
							</ul>
						</div>
						<div id="tab2" class="tabContent">
							<ul>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
							</ul>
						</div>
						<div id="tab3" class="tabContent">
							<ul>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
							</ul>
						</div>
						<div id="tab4" class="tabContent">
							<ul>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
								<li><a href="#">
										<img src="images/hotel.jpg" alt=""></a>
									<dl>
										<dt>이름이들어감</dt>
										<dd>10,000원</dd>
									</dl>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</section> -->
			<section id="promo">
				<div class="promoInner">
					<h2>기획전</h2>
					<div class="promoBox">
						<a href="https://cafe.naver.com/actrip/381" target="_blank"><img src="images/mainImg/promobg.png" alt=""><span>서핑강습 할인쿠폰 <i class="fas fa-chevron-circle-right"></i><span class="subinst">예약 전에 꼭 할인혜택 체크!</span></span></a>
						<!-- <a href="https://cafe.naver.com/actrip/380" target="_blank"><img src="images/mainImg/promobg.png" alt=""><span>바베큐파티 할인쿠폰 <i class="fas fa-chevron-circle-right"></i><span class="subinst">맛있게 먹고 신나게 즐기자~</span></span></a> -->
						<a href="https://cafe.naver.com/actrip/379" target="_blank"><img src="images/mainImg/promobg.png" alt=""><span>서핑버스 할인쿠폰 <i class="fas fa-chevron-circle-right"></i><span class="subinst">비교불가! 빠르고 편리한 액트립 셔틀</span></span></a>
						<!-- <a href="/eatlist"><img src="images/mainImg/promobg.png" alt=""><span>양양 맛도락 여행 <i class="fas fa-chevron-circle-right"></i><span class="subinst">서핑 후엔 맛집에서 체력충전!</span></span></a> -->
					</div>
					<div style="padding-top:20px;"><a href="https://pf.kakao.com/_HxmtMxl" target="_blank"><img src="images/mainImg/kakaochat.jpg" alt="액트립 카카오채널" style="width:100%;"></a></div>
				</div>
			</section>

		</div>
	</div>
</div>

<? include '_layout_bottom.php'; ?>

<!-- Initialize Swiper -->
<script>
	var swiper = new Swiper('.swiper-container', {
		loop: true,
		autoplay: {
            delay: 2000,
            disableOnInteraction: false,
        },
		pagination: {
			el: '.swiper-pagination',
			dynamicBullets: true,
		},
	});

	$j(".swiper-container").hover(
		function() {
			swiper.autoplay.stop();
		}, 
		function() {
			swiper.autoplay.start();
	});
</script>
<!-- tab -->
<script>
	$j(function() {
		$j(".tabContent").hide();
		$j(".tabContent:first").show();

		$j("ul.tabs li").click(function() {
			$j("ul.tabs li").removeClass("active").css({
				"color": "#333",
				"font-weight": "100"
			});
			$j(this).addClass("active").css({
				"font-weight": "bold"
			});
			$j(".tabContent").hide()
			var activeTab = $j(this).attr("rel");
			$j("#" + activeTab).fadeIn()
		});
	});
</script>