<? include 'db.php'; ?>

<?
$reqCode = ($_REQUEST["code"] == "") ? "eatjukdo" : $_REQUEST["code"];
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
            
            <? include '_layout_eatcategory.php'; ?>


<?
$select_query = "SELECT lat, lng FROM AT_CODE  WHERE code = '$reqCode'";
$result = mysqli_query($conn, $select_query);
$rowMain = mysqli_fetch_array($result);

$lat = $rowMain["lat"];
$lng = $rowMain["lng"];
$eatLocation = "";
$i = 0;

$select_query = "SELECT * FROM AT_PROD_MAIN 
                    WHERE code = 'eat'
                        AND category = '$reqCode'
                        AND use_yn = 'Y'
                        AND view_yn = 'Y'
                        AND sub_info != ''
                    ORDER BY rand()";
$result_shoplist = mysqli_query($conn, $select_query);
$shopcount = mysqli_num_rows($result_shoplist);
?>

            <section id="taste">
                <h2><img src="https://surfenjoy.cdn3.cafe24.com/act_title/taste.png" alt="액트립맛도락"></h2>
                <span class="coupon"><a href="https://cafe.naver.com/actrip/2097" target="_blank"><img src="images/icon/coupon.svg" alt="">액트립 제휴쿠폰 모음<i class="fas fa-angle-right"></i></a></span>
            </section>
            <section id="popRest">
                <h2># 쿠폰제휴</h2>
                <ul class="listRest">
                    <li class="listRestbox">
                    <?while ($row = mysqli_fetch_assoc($result_shoplist)){
                        $seq = $row["seq"];
                        $eatlat = $row["shoplat"];
                        $eatlng = $row["shoplng"];
                        $shopname = $row["shopname"];
                        $shoptype = str_replace("@", ",", $row["sub_tag"]);
                        $shopmenu = "<b>대표메뉴</b>  ".$row["sub_title"];
                        $shopmenu .= "<br>&nbsp;&nbsp;&nbsp;<img src=\"/act/images/icon/phone.svg\" class=\"phoneimg\"> ".$row["tel_admin"];
                        $shopevent = "<img src=\"/act/images/icon/pin.svg\" class=\"phoneimg\">".$row["shopaddr"]."<br>&nbsp;&nbsp;&nbsp;<span class=\"spaninfo\">쿠폰제시</span>".$row["sub_info"];

                        $eatLocation .= "MARKER_SPRITE_POSITION2.eat$seq = [0, 0, '$eatlat', '$eatlng', '$shopmenu', '$shopevent', $i, '$shopname [$shoptype]'];\n";
                        $i++;
                        $shop_img = explode('|', $row["shop_img"]);
                        $spoon = explode('@', $row["sub_tag"]);

                        $noimg = "";
                        if($shop_img[0] == "") $noimg = "noThumb";
                        ?>
                        <ul class="listItem <?=$noimg?>">
                            <li class="thumbnail">
                                <a><img src="<?=$shop_img[0]?>" alt=""></a></li>
                            <li class="contents">
                                <h3><a><?=$shopname?> <i class="fas fa-angle-right"></i></a></h3>
                                <span><img src="images/icon/spoon.svg" alt=""><?=$spoon[0]?><?if(count($spoon) == 2) echo '<img src="images/icon/scooter.svg" alt="">'.$spoon[1]?></span>
                                <p onclick="fnMapView('eat<?=$seq?>');" style="cursor:pointer"><img src="images/icon/pin.svg" alt=""><?=$row["shopaddr"]?></p>
                                <p><img src="images/icon/phone.svg" alt=""><?=$row["tel_admin"]?></p>
                                <p>대표메뉴<span><?=$row["sub_title"]?></span></p>
                            </li>
                            <?if($row["sub_info"] != ""){?>
                            <li class="event"><a href="https://cafe.naver.com/ArticleList.nhn?search.clubid=29998302&search.menuid=27&search.boardtype=L" style="color:#fff;" target="_blank"><span>쿠폰제시</span><?=$row["sub_info"]?></a></li>
                            <?}?>
                        </ul>
                    <?}?>
                    </li>
                </ul>
            </section>
<?
$select_query = "SELECT * FROM AT_PROD_MAIN 
                    WHERE code = 'eat'
                        AND category = '$reqCode'
                        AND use_yn = 'Y'
                        AND view_yn = 'Y'
                        AND sub_info = ''
                        AND sub_tag NOT IN ('카페')
                    ORDER BY rand()";
$result_shoplist = mysqli_query($conn, $select_query);
$shopcount = mysqli_num_rows($result_shoplist);
?>
            <section id="allRest">
                <h2 class="hidden">양양 맛도락 제휴</h2>
                <ul class="listRest">
                    <li class="listRestbox">
                    <?while ($row = mysqli_fetch_assoc($result_shoplist)){
                        $seq = $row["seq"];
                        $eatlat = $row["shoplat"];
                        $eatlng = $row["shoplng"];
                        $shopname = $row["shopname"];
                        $shoptype = str_replace("@", ",", $row["sub_tag"]);
                        $shopmenu = "<b>대표메뉴</b>  ".$row["sub_title"];
                        $shopmenu .= "<br>&nbsp;&nbsp;&nbsp;<img src=\"/act/images/icon/phone.svg\" class=\"phoneimg\"> ".$row["tel_admin"];
                        $shopevent = "<img src=\"/act/images/icon/pin.svg\" class=\"phoneimg\">".$row["shopaddr"];

                        $eatLocation .= "MARKER_SPRITE_POSITION2.eat$seq = [0, 0, '$eatlat', '$eatlng', '$shopmenu', '$shopevent', $i, '$shopname [$shoptype]'];\n";
                        $i++;
                        $shop_img = explode('|', $row["shop_img"]);
                        $spoon = explode('@', $row["sub_tag"]);

                        $noimg = "";
                        if($shop_img[0] == "") $noimg = "noThumb";
                        ?>
                        <ul class="listItem <?=$noimg?>">
                            <li class="thumbnail">
                                <a><img src="<?=$shop_img[0]?>" alt=""></a></li>
                            <li class="contents">
                                <h3><a><?=$shopname?> <i class="fas fa-angle-right"></i></a></h3>
                                <span><img src="images/icon/spoon.svg" alt=""><?=$spoon[0]?><?if(count($spoon) == 2) echo '<img src="images/icon/scooter.svg" alt="">'.$spoon[1]?></span>
                                <p onclick="fnMapView('eat<?=$seq?>');" style="cursor:pointer"><img src="images/icon/pin.svg" alt=""><?=$row["shopaddr"]?></p>
                                <p><img src="images/icon/phone.svg" alt=""><?=$row["tel_admin"]?></p>
                                <p>대표메뉴<span><?=$row["sub_title"]?></span></p>
                            </li>
                        </ul>
                    <?}?>
                </ul>
            </section>

            <?
$select_query = "SELECT * FROM AT_PROD_MAIN 
                    WHERE code = 'eat'
                        AND category = '$reqCode'
                        AND use_yn = 'Y'
                        AND view_yn = 'Y'
                        AND sub_info = ''
                        AND sub_tag IN ('카페')
                    ORDER BY rand()";
$result_shoplist = mysqli_query($conn, $select_query);
$shopcount = mysqli_num_rows($result_shoplist);

if($shopcount > 0){
?>
            <section id="allRest">
                <h2 class="hidden">양양 카페 제휴</h2>
                <ul class="listRest">
                    <li class="listRestbox">
                    <?while ($row = mysqli_fetch_assoc($result_shoplist)){
                        $seq = $row["seq"];
                        $eatlat = $row["shoplat"];
                        $eatlng = $row["shoplng"];
                        $shopname = $row["shopname"];
                        $shoptype = str_replace("@", ",", $row["sub_tag"]);
                        $shopmenu = "<b>대표메뉴</b>  ".$row["sub_title"];
                        $shopmenu .= "<br>&nbsp;&nbsp;&nbsp;<img src=\"/act/images/icon/phone.svg\" class=\"phoneimg\"> ".$row["tel_admin"];
                        $shopevent = "<img src=\"/act/images/icon/pin.svg\" class=\"phoneimg\">".$row["shopaddr"];

                        $eatLocation .= "MARKER_SPRITE_POSITION2.eat$seq = [0, 0, '$eatlat', '$eatlng', '$shopmenu', '$shopevent', $i, '$shopname [$shoptype]'];\n";
                        $i++;
                        $shop_img = explode('|', $row["shop_img"]);
                        $spoon = explode('@', $row["sub_tag"]);

                        $noimg = "";
                        if($shop_img[0] == "") $noimg = "noThumb";
                        ?>
                        <ul class="listItem <?=$noimg?>">
                            <li class="thumbnail">
                                <a><img src="<?=$shop_img[0]?>" alt=""></a></li>
                            <li class="contents">
                                <h3><a><?=$shopname?> <i class="fas fa-angle-right"></i></a></h3>
                                <span><img src="images/icon/spoon.svg" alt=""><?=$spoon[0]?><?if(count($spoon) == 2) echo '<img src="images/icon/scooter.svg" alt="">'.$spoon[1]?></span>
                                <p onclick="fnMapView('eat<?=$seq?>');" style="cursor:pointer"><img src="images/icon/pin.svg" alt=""><?=$row["shopaddr"]?></p>
                                <p><img src="images/icon/phone.svg" alt=""><?=$row["tel_admin"]?></p>
                                <p>대표메뉴<span><?=$row["sub_title"]?></span></p>
                            </li>
                        </ul>
                    <?}?>
                </ul>
            </section>
<?}?>
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
<?=$eatLocation?>
</script>