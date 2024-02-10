<?
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$param = ($_REQUEST["resparam"] == "") ? "none" : $_REQUEST["resparam"];
$resNumber = ($_REQUEST["resNumber"] == "") ? "none" : $_REQUEST["resNumber"];

$shopseq = 7;

$arr_start = array(); //출발
$arr_return = array(); //복귀

if($resNumber != "none"){
    $select_query = "SELECT * FROM AT_RES_SUB WHERE resnum = $resNumber";
    $result_setlist = mysqli_query($conn, $select_query);

    while ($row = mysqli_fetch_assoc($result_setlist)){
        $shopseq = $row["seq"];
        $bus_gubun = $row["bus_gubun"];
        $bus_num = $row["bus_num"];

        $arrdate = fnBusNum2023($bus_gubun.$bus_num);
    }
    
}

if($shopseq == 7){ //양양 셔틀버스
    $pointurl = "_view_tab3_yy.php";
}else if($shopseq == 14){ //동해 셔틀버스
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
?>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/default.css">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/surfview.css">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/bus.css">

<div id="wrap">
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
                <div class="fixed1" style="top: 49px;">
                    <div class="vip-tabnavi">
                        <ul>
                            <li class="on"><a>정류장안내</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div id="view_tab2" style="padding-bottom:20px;">
                <? include $pointurl; ?>
            </div>
        </section>
    </div>
</div>

<script>
    var shopseq = <?=$shopseq?>;
</script>
<script type="text/javascript" src="/act_2023/front/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/front/_js/bus.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/front/_js/busday.js?v=<?=time()?>"></script>

<script>
    fnBusPointList();

    $j(document).ready(function() {
        setTimeout('$j("input[type=button]").eq(0).click();', 500);
    });
</script>