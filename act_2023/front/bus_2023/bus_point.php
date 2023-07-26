<?
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$param = ($_REQUEST["resparam"] == "") ? "none" : $_REQUEST["resparam"];
$resNumber = ($_REQUEST["resNumber"] == "") ? "none" : $_REQUEST["resNumber"];

$shopseq = 7;

$arrSa = array(); //사당선
$arrJo = array(); //종로선
$arrS2 = array(); //서울행 오후
$arrS5 = array(); //서울행 저녁

$shopseq = 14; //동해
if($param == "none"){
    if($resNumber != "none"){
        $select_query = "SELECT * FROM AT_RES_SUB WHERE resnum = $resNumber";
        $result_setlist = mysqli_query($conn, $select_query);

        while ($row = mysqli_fetch_assoc($result_setlist)){
            //echo "<br>".$row["seq"]." / ".$row["res_bus"]." / ".$row["res_spoint"];
            $busGubun = $row["res_bus"];
            $arrdate = explode(" ",fnBusNum($busGubun));
            //echo "<br>".$arrdate[0]." / ".$arrdate[1]." / ".$row["res_spoint"];
    
            if($arrdate[1] == "사당선"){
                $arrSa[$row["res_spoint"]] = true;
            }else if($arrdate[1] == "종로선"){
                $arrJo[$row["res_spoint"]] = true;
            }else if($arrdate[1] == "오후"){
                $arrS2[$row["res_spoint"]] = true;
            }else if($arrdate[1] == "저녁"){
                $arrS5[$row["res_spoint"]] = true;
            }

            $shopseq = $row["seq"];
        }
        
    }

    $pointurl = fnBusUrl($shopseq, "point");
}else{
    $pointurl = fnBusUrl($param, "tab");
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
    <? include __DIR__.'/../../_layout/_channel_layout_top.php'; ?>

    <link rel="stylesheet" type="text/css" href="/act_2023/front/_css/surfview.css">
    <link rel="stylesheet" type="text/css" href="/act_2023/front/_css/bus.css">

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

<? include __DIR__.'/../../_layout/_channel_layout_bottom.php'; ?>

<script>
    $j(document).ready(function() {
        setTimeout('$j("input[type=button]").eq(0).click();', 500);
    });

	var busSeq = "<?=$shopseq?>";
    var busTypeTitle = "<?=fnBusUrl(fnBusUrl($shopseq, "url"), "type");?>";
</script>

<script type="text/javascript" src="/act_2023/front/_js/channel_bus.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/front/_js/channel_busday.js?v=<?=time()?>"></script>