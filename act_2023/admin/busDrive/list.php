<?php 
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/logininfo.php';
$shopseq = -3;
?>
<link rel="stylesheet" type="text/css" href="/act_2023/admin/_css/admin_bus.css">
<link rel="stylesheet" type="text/css" href="/act_2023/admin/_css/admin_common.css">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/surfview.css">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/jquery-ui.css" />

<script type="text/javascript" src="/act_2023/front/_js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/act_2023/front/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/_js/busday.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/admin_bus.js?v=<?=time()?>"></script>

<div class="bd_tl" style="width:100%;">
	<h1 class="ngeb clear"><i class="bg_color"></i>양양 셔틀버스 예약현황</h1>
</div>

<div class="container" id="contenttop">
<!-- .tab_container -->
<div id="containerTab" class="areaRight">
    <div id="right_article3" class="right_article4">
		<?include "_calendar.php"?>
    </div>

    <ul class="tabs">
        <li class="active" rel="tab1">예약현황</li>
    </ul>

	<!-- #container -->
    <div class="tab_container">
        <!-- #tab1 -->
        <div id="tab1" class="tab_content">
			<div style="text-align:center;font-size:14px;padding:50px;" id="initText2">
				<b>날짜를 선택하세요.</b>
			</div>
			<div id="divResList"></div>
		</div>
    </div>
    <!-- .tab_container -->
</div>
<!-- #container -->
</div>

<input type="hidden" id="hidselDate" value="">
<iframe id="ifrmResize" name="ifrmResize" style="width:800px;height:400px;display:none;"></iframe>