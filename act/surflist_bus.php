<?php 
include 'db.php';

$resNumber = $_REQUEST["resNumber"];
?>

<script>
$j(document).ready(function(){
    //alert("코로나 확산으로 인하여, 액트립 셔틀버스 운행은 조기 중단합니다.\n\n다음시즌에 더욱 좋은모습으로 찾아뵙겠습니다.");
});
</script>

<script src="js/surfordersearch.js"></script>

<div id="wrap">
    <? include '_layout_top.php'; ?>

    <link rel="stylesheet" type="text/css" href="css/surfbus.css">

    <div class="top_area_zone">
        <section id="bus">
            <h2><img src="https://surfenjoy.cdn3.cafe24.com/act_title/bus.png" alt="액트립 서핑버스"></h2>
            <span class="coupon"><a href="https://cafe.naver.com/actrip/2097" target="_blank"><img src="images/icon/coupon.svg" alt="">액트립 제휴쿠폰 모음<i class="fas fa-angle-right"></i></a></span>
        </section>
        <section id="busCat">
            <div class="busCatList">
                <ul>
                    <li class="yybus"><a href="/surfbus_yy"><img src="images/button/yybus.jpg" alt="서울양양셔틀버스"></a></li>
                    <li class="dhbus"><a href="/surfbus_dh"><img src="images/button/dhbus.jpg" alt="서울동해셔틀버스"></a></li>
                </ul>
            </div>
        </section>
    </div>
</div>

<? include '_layout_bottom.php'; ?>