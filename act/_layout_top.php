<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
    
<link rel="stylesheet" type="text/css" href="/act/css/default.css">
<link rel="stylesheet" type="text/css" href="/act/css/swiper.min.css">
<link rel="stylesheet" type="text/css" href="/act/css/gnbstyle.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css">
<link rel="stylesheet" type="text/css" href="/act/css/swiper.min.css">

	<header id="headerWrap">
		<div class="headerBox">
			<div class="menuBox">
				<div class="menu">
					<span></span>
				</div>
				<div class="gnbBox"></div>
				<nav class="navbar-menu">
					<div class="menu-listing">
						<ul class="menu01">
							<!--@if($is_logged)-->
							<li><a href="/index.php?mid=main&act=dispMemberLogout"><img src="/act/images/icon/user-solidg.svg" alt=""><p>로그아웃</p></a></li>
							<!--@else-->
							<li><a href="/index.php?mid=main&act=dispMemberLoginForm"><img src="/act/images/icon/user-solidg.svg" alt=""><p>로그인</p></a></li>
							<!--@end-->	
							<!-- <li><a href="#"><img src="/act/images/icon/shoppingg.svg" alt=""><p>장바구니</p></a></li> -->
							<li><a href="/ordersearch"><img src="/act/images/icon/checkg.svg" alt=""><p>예약조회</p></a></li>
						</ul>
						<ul class="menu02">
							<li><a href="/surf"><img src="/act/images/icon/isurf.png" alt=""></a></li>
							<li><a href="/surfbus"><img src="/act/images/icon/ibus.png" alt=""></a></li>
							<li><a href="/bbq"><img src="/act/images/icon/ibbq.png" alt=""></a></li>
							<!-- <li><a href="javascript:alert('서비스 준비중입니다.');"><img src="/act/images/icon/ibbq.png" alt=""></a></li> -->
							<li><a href="https://m.cafe.naver.com/ca-fe/web/cafes/29998302/menus/7" target="_blank"><img src="/act/images/icon/ievent.png" alt=""></a></li>
							<li><a href="/staylist"><img src="/act/images/icon/ibed.png" alt=""></a></li>
							<li><a href="/eatlist"><img src="/act/images/icon/ifood.png" alt=""></a></li>
						</ul>
						<ul class="menu03">
							<li><a href="https://pf.kakao.com/_HxmtMxl" target="_blank"><img src="/act/images/icon/cscenter.svg" alt="">고객센터</a></li>
							<li><a href="https://pf.kakao.com/_HxmtMxl" target="_blank"><img src="/act/images/talk2.png" alt="">카톡 1:1 문의<i class="fas fa-chevron-right"></i></a></li>
							<li>평일 09:00-18:00
							<br>주말 06:00-20:00</li>
						</ul>                          
						<ul class="menu04">
							<li><a href="https://cafe.naver.com/actrip" target="_blank">네이버카페</a></li>
							<li><a href="/notice">공지사항</a></li>
							<li><a href="/surfFAQ">자주묻는질문</a></li>
						</ul>
						<ul class="menu05">
							<li><a href="https://cafe.naver.com/actrip" target="_blank"><img src="/act/images/icon/cafe.svg" alt=""></a>
							<a href="https://blog.naver.com/surfenjoy" target="_blank"><img src="/act/images/icon/blog.svg" alt=""></a>
							<a href="https://www.instagram.com/actrip_surf" target="_blank"><img src="/act/images/icon/insta.svg" alt=""></a></li>
						</ul>
					</div>
				</nav>
			</div>
			<!-- jQuery cdn -->
			
			<h1 id="logo">
				<a href="/"><img src="/act/images/logo140.jpg" alt="액트립 로고"></a>
			</h1>
			<!-- <div class="shopIcon"><a href="#"><img src="/act/images/icon/shop.svg" alt="장바구니"></a></div> -->
		</div>
	</header>