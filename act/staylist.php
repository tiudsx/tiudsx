<? include 'db.php'; ?>

<?
$reqCode = ($_REQUEST["code"] == "") ? "stayjukdo" : $_REQUEST["code"];
?>

<!-- area menu script -->
<script type="text/javascript">
    var type = "";
    var btnheight = "";

    $j(document).ready(function() {
        //지도로 보기 - 슬라이드
        $j("#slide1").click(function() {
            fnMapView('');
        });
    });    
</script>

<div id="wrap">
<? include '_layout_top.php'; ?>

    <div class="top_area_zone">
        <div id="listWrap">
            
            <? include '_layout_staycategory.php'; ?>

<?
$select_query = "SELECT lat, lng FROM AT_CODE  WHERE code = '$reqCode'";
$result = mysqli_query($conn, $select_query);
$rowMain = mysqli_fetch_array($result);

$lat = $rowMain["lat"];
$lng = $rowMain["lng"];
$stayLocation = "";
$i = 0;

$select_query = "SELECT * FROM AT_PROD_MAIN 
                    WHERE code = 'stay'
                        AND category = '$reqCode'
                        AND use_yn = 'Y'
                        AND view_yn = 'Y'
                        AND sub_title = '펜션'
                    ORDER BY rand()";
$result_shoplist = mysqli_query($conn, $select_query);
$shopcount = mysqli_num_rows($result_shoplist);

?>
            <section id="taste">
                <h2><img src="https://surfenjoy.cdn3.cafe24.com/act_title/accommodation.png" alt="액트립 숙소"></h2>
                <span class="coupon"><a href="https://cafe.naver.com/actrip/2097" target="_blank"><img src="images/icon/coupon.svg" alt="">액트립 제휴쿠폰 모음<i class="fas fa-angle-right"></i></a></span>
            </section>
            <section id="popAcm">
                <h2># 펜션 정보</h2>
                <ul class="listAcm">
                    <li class="listAcmbox">
                    <?while ($row = mysqli_fetch_assoc($result_shoplist)){
                        $seq = $row["seq"];
                        $staylat = $row["shoplat"];
                        $staylng = $row["shoplng"];
                        $shopname = $row["shopname"];
                        $shoptype = $row["sub_title"];
                        $shopmenu = "<br>&nbsp;&nbsp;&nbsp;<img src=\"/act/images/icon/phone.svg\" class=\"phoneimg\"> ".$row["tel_admin"];
                        $shopevent = "<img src=\"/act/images/icon/pin.svg\" class=\"phoneimg\">".$row["shopaddr"];

                        $stayLocation .= "MARKER_SPRITE_POSITION2.stay$seq = [0, 0, '$staylat', '$staylng', '$shopmenu', '$shopevent', $i, '$shopname [$shoptype]'];\n";
                        $i++;
                        $shop_img = explode('|', $row["shop_img"]);
                        $price = explode('@', $row["sub_tag"]);

                        $noimg = "";
                        if($shop_img[0] == "") $noimg = "noThumb";

                        $pricetext = '';
                        foreach ($price as $key => $value) {
                            $arrPrice = explode('|', $value);
                            $pricetext .= $arrPrice[0].'<span>'.$arrPrice[1].'</span>';
                        }
                        ?>
                        <ul class="listItem <?=$noimg?>">
                            <li class="thumbnail">
                                <a><img src="<?=$shop_img[0]?>" alt=""></a></li>
                            <li class="contents">
                                <h3><a><?=$row["shopname"]?> <i class="fas fa-angle-right"></i></a></h3>
                                <span><?=$row["sub_title"]?></span>
                                <p><label onclick="fnMapView('stay<?=$seq?>');" style="cursor:pointer"><img src="images/icon/pin.svg" alt=""><?=$row["shopaddr"]?></label><br><img src="images/icon/phone.svg" alt=""><?=$row["tel_admin"]?></p>
                                <p><?=$pricetext?></p>
                            </li>
                        </ul>
                    <?}?>
                    </li>
                </ul>
            </section>

<?
$select_query = "SELECT * FROM AT_PROD_MAIN 
WHERE code = 'stay'
    AND category = '$reqCode'
    AND use_yn = 'Y'
    AND view_yn = 'Y'
    AND sub_title IN ('모텔', '호텔')
ORDER BY rand()";
$result_shoplist = mysqli_query($conn, $select_query);
$shopcount = mysqli_num_rows($result_shoplist);

if($shopcount > 0){
?>

            <section id="popAcm" class="popAcmTop">
                <h2># 호텔,모텔 정보</h2>
                <ul class="listAcm">
                    <li class="listAcmbox">
                    <?while ($row = mysqli_fetch_assoc($result_shoplist)){
                        $seq = $row["seq"];
                        $staylat = $row["shoplat"];
                        $staylng = $row["shoplng"];
                        $shopname = $row["shopname"];
                        $shoptype = $row["sub_title"];
                        $shopmenu = "<br>&nbsp;&nbsp;&nbsp;<img src=\"/act/images/icon/phone.svg\" class=\"phoneimg\"> ".$row["tel_admin"];
                        $shopevent = "<img src=\"/act/images/icon/pin.svg\" class=\"phoneimg\">".$row["shopaddr"];

                        $stayLocation .= "MARKER_SPRITE_POSITION2.stay$seq = [0, 0, '$staylat', '$staylng', '$shopmenu', '$shopevent', $i, '$shopname [$shoptype]'];\n";
                        $i++;
                        $shop_img = explode('|', $row["shop_img"]);
                        $price = explode('@', $row["sub_tag"]);

                        $noimg = "";
                        if($shop_img[0] == "") $noimg = "noThumb";

                        $pricetext = '';
                        foreach ($price as $key => $value) {
                            $arrPrice = explode('|', $value);
                            $pricetext .= $arrPrice[0].'<span>'.$arrPrice[1].'</span>';
                        }
                        ?>
                        <ul class="listItem <?=$noimg?>">
                            <li class="thumbnail">
                                <a><img src="<?=$shop_img[0]?>" alt=""></a></li>
                            <li class="contents">
                                <h3><a><?=$row["shopname"]?> <i class="fas fa-angle-right"></i></a></h3>
                                <span><?=$row["sub_title"]?></span>
                                <p><label onclick="fnMapView('stay<?=$seq?>');" style="cursor:pointer"><img src="images/icon/pin.svg" alt=""><?=$row["shopaddr"]?></label><br><img src="images/icon/phone.svg" alt=""><?=$row["tel_admin"]?></p>
                                <p><?=$pricetext?></p>
                            </li>
                        </ul>
                    <?}?>
                    </li>
                </ul>
            </section>

<?
}

$select_query = "SELECT * FROM AT_PROD_MAIN 
WHERE code = 'stay'
    AND category = '$reqCode'
    AND use_yn = 'Y'
    AND view_yn = 'Y'
    AND sub_title = '민박'
ORDER BY rand()";
$result_shoplist = mysqli_query($conn, $select_query);
$shopcount = mysqli_num_rows($result_shoplist);

if($shopcount > 0){
?>

            <section id="popAcm" class="popAcmTop">
                <h2># 민박 정보</h2>
                <ul class="listAcm">
                    <li class="listAcmbox">
                    <?while ($row = mysqli_fetch_assoc($result_shoplist)){
                        $seq = $row["seq"];
                        $staylat = $row["shoplat"];
                        $staylng = $row["shoplng"];
                        $shopname = $row["shopname"];
                        $shoptype = $row["sub_title"];
                        $shopmenu = "<br>&nbsp;&nbsp;&nbsp;<img src=\"/act/images/icon/phone.svg\" class=\"phoneimg\"> ".$row["tel_admin"];
                        $shopevent = "<img src=\"/act/images/icon/pin.svg\" class=\"phoneimg\">".$row["shopaddr"];

                        $stayLocation .= "MARKER_SPRITE_POSITION2.stay$seq = [0, 0, '$staylat', '$staylng', '$shopmenu', '$shopevent', $i, '$shopname [$shoptype]'];\n";
                        $i++;
                        $shop_img = explode('|', $row["shop_img"]);
                        $price = explode('@', $row["sub_tag"]);

                        $noimg = "";
                        if($shop_img[0] == "") $noimg = "noThumb";

                        $pricetext = '';
                        foreach ($price as $key => $value) {
                            $arrPrice = explode('|', $value);
                            $pricetext .= $arrPrice[0].'<span>'.$arrPrice[1].'</span>';
                        }
                        ?>
                        <ul class="listItem <?=$noimg?>">
                            <li class="thumbnail">
                                <a><img src="<?=$shop_img[0]?>" alt=""></a></li>
                            <li class="contents">
                                <h3><a><?=$row["shopname"]?> <i class="fas fa-angle-right"></i></a></h3>
                                <span><?=$row["sub_title"]?></span>
                                <p><label onclick="fnMapView('stay<?=$seq?>');" style="cursor:pointer"><img src="images/icon/pin.svg" alt=""><?=$row["shopaddr"]?></label><br><img src="images/icon/phone.svg" alt=""><?=$row["tel_admin"]?></p>
                                <p><?=$pricetext?></p>
                            </li>
                        </ul>
                    <?}?>
                    </li>
                </ul>
            </section>

<?
}

$select_query = "SELECT * FROM AT_PROD_MAIN 
WHERE code = 'stay'
    AND category = '$reqCode'
    AND use_yn = 'Y'
    AND view_yn = 'Y'
    AND sub_title = '게스트하우스'
ORDER BY rand()";
$result_shoplist = mysqli_query($conn, $select_query);
$shopcount = mysqli_num_rows($result_shoplist);

if($shopcount > 0){
?>

            <section id="popAcm" class="popAcmTop">
                <h2># 게스트하우스 정보</h2>
                <ul class="listAcm">
                    <li class="listAcmbox">
                    <?while ($row = mysqli_fetch_assoc($result_shoplist)){
                        $seq = $row["seq"];
                        $staylat = $row["shoplat"];
                        $staylng = $row["shoplng"];
                        $shopname = $row["shopname"];
                        $shoptype = $row["sub_title"];
                        $shopmenu = "<br>&nbsp;&nbsp;&nbsp;<img src=\"/act/images/icon/phone.svg\" class=\"phoneimg\"> ".$row["tel_admin"];
                        $shopevent = "<img src=\"/act/images/icon/pin.svg\" class=\"phoneimg\">".$row["shopaddr"];

                        $stayLocation .= "MARKER_SPRITE_POSITION2.stay$seq = [0, 0, '$staylat', '$staylng', '$shopmenu', '$shopevent', $i, '$shopname [$shoptype]'];\n";
                        $i++;
                        $shop_img = explode('|', $row["shop_img"]);
                        $price = explode('@', $row["sub_tag"]);

                        $noimg = "";
                        if($shop_img[0] == "") $noimg = "noThumb";

                        $pricetext = '';
                        foreach ($price as $key => $value) {
                            $arrPrice = explode('|', $value);
                            $pricetext .= $arrPrice[0].'<span>'.$arrPrice[1].'</span>';
                        }
                        ?>
                        <ul class="listItem <?=$noimg?>">
                            <li class="thumbnail">
                                <a><img src="<?=$shop_img[0]?>" alt=""></a></li>
                            <li class="contents">
                                <h3><a><?=$row["shopname"]?> <i class="fas fa-angle-right"></i></a></h3>
                                <span><?=$row["sub_title"]?></span>
                                <p><label onclick="fnMapView('stay<?=$seq?>');" style="cursor:pointer"><img src="images/icon/pin.svg" alt=""><?=$row["shopaddr"]?></label><br><img src="images/icon/phone.svg" alt=""><?=$row["tel_admin"]?></p>
                                <p><?=$pricetext?></p>
                            </li>
                        </ul>
                    <?}?>
                    </li>
                </ul>
            </section>

<?}?>
            <section id="allAcm" style="display:none;">
                <h2 class="hidden">강원도 숙소 목록</h2>
                <ul class="listAcm">
                    <li class="listAcmbox">
                        
                    </li>
                </ul>
            </section>
        </div>
    </div>
</div>
<div class="con_footer">
    <div class="fixedwidth resbottom">
        <img src="https://surfenjoy.cdn3.cafe24.com/button/btnMap.png" id="slide1">
        <div id="sildeing" style="display:block;height:100%;padding-top:5px;">
            <iframe scrolling="no" frameborder="0" class="ifrmMap" id="ifrmMap" name="ifrmMap" style="width:100%;height:100%;" src="/act/surf/surfmap_etc.html"></iframe>
        </div>
    </div>
</div>

<? include '_layout_bottom.php'; ?>

<script>

var sLng = "<?=$lat?>";
var sLat = "<?=$lng?>";

var MARKER_SPRITE_POSITION2 = {}
<?=$stayLocation?>
</script>