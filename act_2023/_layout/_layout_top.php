<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
    
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/default.css">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/gnbstyle.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/jquery-ui.css" />

<script type="text/javascript" src="/act_2023/front/_js/jquery-ui.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/front/_js/jquery.blockUI.js"></script>

<header id="headerWrap">
	<div class="headerBox">
	<?if($couponseq == ""){?>
		<h1 id="logo">
			<a href="/"><img src="/act_2023/images/logo140.jpg" alt="액트립 로고"></a>
		</h1>
		<div class="shopIcon"><a href="/ordersearch"><img src="/act_2023/images/icon/checkg.svg" alt=""><p>예약조회</p></a></div>
	<?}else{?>
		<h1 id="logo">
			<a><img src="/act_2023/images/logo140.jpg" alt="액트립 로고"></a>
		</h1>
	<?}?>
	</div>
</header>