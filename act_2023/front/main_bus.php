<?php 
include __DIR__.'/../common/db.php';

$resNumber = $_REQUEST["resNumber"];
?>

<script>
$j(document).ready(function(){
    //alert("코로나 확산으로 인하여, 액트립 셔틀버스 운행은 조기 중단합니다.\n\n다음시즌에 더욱 좋은모습으로 찾아뵙겠습니다.");
});
</script>

<div id="wrap">
<? include __DIR__.'/../_layout/_layout_top.php'; ?>

    <link rel="stylesheet" type="text/css" href="/act_2023/_css/bus.css">

    <div class="top_area_zone">
        <section id="bus">
            <h2><img src="https://actrip.cdn1.cafe24.com/act_title/bus.png" alt="액트립 서핑버스"></h2>
        </section>
        <section id="busCat">
            <div class="busCatList">
                <ul>
                    <li class="yybus"><a href="/surfbus_yy"><img src="/act_2023/images/button/yybus.jpg" alt="서울양양셔틀버스"></a></li>
                    <li class="dhbus"><a href="/surfbus_dh"><img src="/act_2023/images/button/dhbus.jpg" alt="서울동해셔틀버스"></a></li>
                </ul>
            </div>
        </section>
    </div>
</div>

<? include __DIR__.'/../_layout/_layout_bottom.php'; ?>