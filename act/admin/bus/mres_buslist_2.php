<?php 
include __DIR__.'/../../db.php';
include __DIR__.'/../../common/logininfo.php';
$shopseq = 0;
?>

<link rel="stylesheet" type="text/css" href="/act/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/act/css/surfview.css">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_surf.css">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_common.css">
<script type="text/javascript" src="/act/js/admin_surf.js?v=1"></script>
<script type="text/javascript" src="/act/js/surfview_bus.js"></script>
<script type="text/javascript" src="/act/js/jquery.blockUI.js"></script>

<div class="bd_tl" style="width:100%;">
	<h1 class="ngeb clear"><i class="bg_color"></i>액트립 셔틀버스 예약현황</h1>
</div>

<script>
    var busDateinit = "2020-04-01";
    var mobileuse = "m";

    function fnCalMoveAdminList2(selDate, day, seq) {
        var nowDate = new Date();

        $j("#divResList").html("");
        $j("#initText2").css("display", "");
                
        $j("#right_article3").load("/act/admin/bus/mres_buslist_2_calendar.php?selDate=" + selDate + "&selDay=" + day + "&seq=" + seq + "&t=" + nowDate.getTime());

    }
</script>

<div class="container" id="contenttop">
<!-- .tab_container -->
<div id="containerTab" class="areaRight">
    <div id="right_article3" class="right_article4">
		<?include "mres_buslist_2_calendar.php"?>
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