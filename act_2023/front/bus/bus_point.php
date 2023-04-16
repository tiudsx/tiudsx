<?
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$param = ($_REQUEST["resparam"] == "") ? "surfbus_yy" : $_REQUEST["resparam"];

if($param == "surfbus_yy"){ //양양 셔틀버스
    $shopseq = 7;
    $pointurl = "_view_tab3_yy.php";
}else{ //동해 셔틀버스
    $shopseq = 14;
    $pointurl = "_view_tab3_dh.php";
}

$select_query = "SELECT * FROM AT_PROD_MAIN WHERE seq = $shopseq AND use_yn = 'Y'";
$result = mysqli_query($conn, $select_query);
$rowMain = mysqli_fetch_array($result);

$bustitle = $rowMain["shopname"];
$bussubinfo = $rowMain["sub_info"];
$busData = explode("|", $rowMain["sub_tag"]);
$busgubun = $busData[0];
$sbusDate = $busData[1];

//연락처 모바일 여부
if(Mobile::isMobileCheckByAgent()) $inputtype = "number"; else $inputtype = "text";
?>
<div id="wrap">
    <? include __DIR__.'/../../_layout/_layout_top.php'; ?>

    <link rel="stylesheet" type="text/css" href="/act_2023/_css/surfview.css">
    <link rel="stylesheet" type="text/css" href="/act_2023/_css/bus.css">
    <link rel="stylesheet" type="text/css" href="/act_2023/_css/jquery-ui.css" />

    <div class="top_area_zone">
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1><?=str_replace('액트립 ', '', $bustitle)?> 정류장 안내</h1>
                <a class="reviewlink">
                    <span class="reviewcnt">구매 <b><?=number_format($rowMain["sell_cnt"])?></b>개</span>
                </a>
                <div class="shopsubtitle"><?=$bussubinfo?></div>
            </div>
        </section>

        <section class="notice">
            <div class="vip-tabwrap">
                <div id="tabnavi" class="fixed1" style="top: 49px;">
                    <div class="vip-tabnavi">
                        <ul>
                            <li class="on"><a>정류장안내</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div id="view_tab2" style="min-height: 800px;">
                <? include $pointurl; ?>
            </div>
        </section>
    </div>
</div>

<? include __DIR__.'/../../_layout/_layout_bottom.php'; ?>

<script>
    var busDateinit = "2020-04-01";

    $j(document).ready(function() {
        setTimeout('$j("input[type=button]").eq(0).click();', 500);
    });
</script>

<script type="text/javascript" src="/act_2023/_js/jquery-ui.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/_js/bus.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/_js/busday.js?v=<?=time()?>"></script>