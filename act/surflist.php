<? include 'db.php'; ?>

<?
$reqCode = ($_REQUEST["code"] == "") ? "jukdo" : $_REQUEST["code"];
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
            
            <link rel="stylesheet" href="/act/css/surflist.css">
            <? include '_layout_category.php'; ?>


<?
$select_query = "SELECT lat, lng FROM AT_CODE  WHERE code = '$reqCode'";
$result = mysqli_query($conn, $select_query);
$rowMain = mysqli_fetch_array($result);

$lat = $rowMain["lat"];
$lng = $rowMain["lng"];
$shopLocation = "";
$i = 0;
//서핑샵 정보 가져오기
$select_query = "SELECT a.* FROM AT_PROD_MAIN a INNER JOIN AT_PROD_AD b
                    ON a.seq = b.seq
                        AND b.adtype IN ('surfarea', 'surfpoint')
                        AND b.category IN ('$areacodesel', '$reqCode')
                        AND b.use_yn = 'Y'
                    WHERE a.code = 'surf'
                        AND a.use_yn = 'Y'
                        AND a.view_yn = 'Y'
                    ORDER BY b.ordernum";
$result_shopadlist = mysqli_query($conn, $select_query);
$shopadcount = mysqli_num_rows($result_shopadlist);

$notSeq = "";
?>
            <section id="surfShop">
                <h2><img src="https://surfenjoy.cdn3.cafe24.com/act_title/surfshop.png" alt="액트립 숙소"></h2>
                <span class="coupon"><a href="https://cafe.naver.com/actrip/2097" target="_blank"><img src="images/icon/coupon.svg" alt="">액트립 제휴쿠폰 모음<i class="fas fa-angle-right"></i></a></span>
            </section>

            <?if($shopadcount > 0){?>
            <section id="popShop">
                <h2><img src="images/icon/moon.svg" alt=""> 액트립 추천</h2>
                <ul class="listShop">
                    <li class="listShopbox">

                    <?while ($row = mysqli_fetch_assoc($result_shopadlist)){
                        $seq = $row["seq"];
                        $notSeq .= $seq.',';
                        $shop_img = explode('|', $row["shop_img"]);
                        $shop_addr = $row["shopaddr"];
                        $shoplat = $row["shoplat"];
                        $shoplng = $row["shoplng"];
                        $categoryname = $row["categoryname"];
                        $shopname = $row["shopname"];
                        $link_yn  = $row["link_yn"];
                        $link_url  = $row["link_url"];

                        $shopLocation .= "MARKER_SPRITE_POSITION2.shop$seq = [0, 0, '$shoplat', '$shoplng', '$shop_addr', '', $i, $seq, '$shop_img[0]', '$categoryname', '$shopname'];\n";
                        $i++;
                        ?>
                        <ul class="listItem">
                            <li class="thumbnail">
                                <?if($link_yn == "Y"){?>
                                <a href="<?=$link_url?>" target="_blank"><img src="<?=$shop_img[0]?>" alt=""></a>
                                <?}else{?>
                                <a href="/surfview?seq=<?=$seq?>"><img src="<?=$shop_img[0]?>" alt=""></a>
                                <?}?>
                                <span>
                                    <img src="images/icon/parking.svg" alt="">
                                    <img src="images/icon/wifi.svg" alt="">
                                    <!-- <img src="images/icon/house.svg" alt=""> -->
                                    <!-- <img src="images/icon/pet.svg" alt=""> -->
                                    <img src="images/icon/toilet.svg" alt="">
                                </span>
                            </li>
                            <li class="contents">
                                <?if($link_yn == "Y"){?>
                                <h3><a href="<?=$link_url?>"><?=$shopname?> <i class="fas fa-angle-right"></i></a></h3>
                                <?}else{?>
                                <h3><a href="/surfview?seq=<?=$seq?>"><?=$shopname?> <i class="fas fa-angle-right"></i></a></h3>
                                <?}?>
                                <span><a href="javascript:fnMapView('shop<?=$seq?>');"><img src="images/icon/map.svg" alt="">위치</a></span>
                                <span>구매 <?=number_format($row["sell_cnt"])?>개</span>
                                
                                <?
								$shop_info = explode('@', $row["sub_title"]);
								foreach($shop_info as $value){
									echo '<p>✓ '.$value.'</p>';
                                }
                                ?>

                            </li>
                            <li class="price">
                                <?
                                $shop_price = explode('@', $row["sub_info"]);
                                $arrlecture = explode('|', $shop_price[0]);
                                $arrrental = explode('|', $shop_price[1]);
                                ?>
                                <span class="lecture">
                                    <p><span><?=$arrlecture[0]?></span></p>
                                    <p>
                                    <?if($arrlecture[1] != 0) echo number_format($arrlecture[1]).'원';?>
                                    </p>
                                    <p><?=number_format($arrlecture[2])?>원</p>
                                </span>
                                <?if($arrrental[0] == "숙박"){?>
                                <span class="rental">
                                    <p><span><?=$arrrental[1]?></span></p>
                                    <p>
                                    <?if($arrrental[2] != 0) echo number_format($arrrental[2]).'원';?>
                                    </p>
                                    <p><?=number_format($arrrental[3])?>원</p>
                                </span>
                                <?}else if($arrrental[0] == "장비렌탈"){?>
                                <span class="rental">
                                    <p><span><?=$arrrental[0]?></span></p>
                                    <p>
                                    <?if($arrrental[1] != 0) echo number_format($arrrental[1]).'원';?>
                                    </p>
                                    <p><?=number_format($arrrental[2])?>원</p>
                                </span>
                                <?}?>
                                
                            </li>
                            <?if($row["sub_tag"] != ""){?>
                            <li class="event"><span>이벤트</span><?=$row["sub_tag"]?></li>
                            <?}?>
                        </ul>
                    <?}?>
                    
                    </li>
                </ul>
            </section>
            <?}
$notSeq .= '0';
$select_query = "SELECT * FROM AT_PROD_MAIN 
                    WHERE code = 'surf'
                        AND category = '$reqCode'
                        AND use_yn = 'Y'
                        AND view_yn = 'Y'
                        AND seq NOT IN ($notSeq)
                    ORDER BY rand()";
$result_shoplist = mysqli_query($conn, $select_query);
$shopcount = mysqli_num_rows($result_shoplist);
            ?>
            <section id="allShop">
                <h2>#서핑샵 찾아보기</h2>
                <ul class="listShop">
                    <li class="listShopbox">
                    <?while ($row = mysqli_fetch_assoc($result_shoplist)){
                        $seq = $row["seq"];
                        $shop_img = explode('|', $row["shop_img"]);
                        $shop_addr = $row["shopaddr"];
                        $shoplat = $row["shoplat"];
                        $shoplng = $row["shoplng"];
                        $categoryname = $row["categoryname"];
                        $shopname = $row["shopname"];
                        $link_yn  = $row["link_yn"];
                        $link_url  = $row["link_url"];

                        $shopLocation .= "MARKER_SPRITE_POSITION2.shop$seq = [0, 0, '$shoplat', '$shoplng', '$shop_addr', '', $i, $seq, '$shop_img[0]', '$categoryname', '$shopname'];\n";
                        $i++;
                        ?>
                        <ul class="listItem">
                            <li class="thumbnail">
                                <?if($link_yn == "Y"){?>
                                <a href="<?=$link_url?>" target="_blank"><img src="<?=$shop_img[0]?>" alt=""></a>
                                <?}else{?>
                                <a href="/surfview?seq=<?=$seq?>"><img src="<?=$shop_img[0]?>" alt=""></a>
                                <?}?>
                                <span>
                                    <img src="images/icon/parking.svg" alt="">
                                    <img src="images/icon/wifi.svg" alt="">
                                    <!-- <img src="images/icon/house.svg" alt=""> -->
                                    <!-- <img src="images/icon/pet.svg" alt=""> -->
                                    <img src="images/icon/toilet.svg" alt="">
                                </span>
                            </li>
                            <li class="contents">
                                <?if($link_yn == "Y"){?>
                                <h3><a href="<?=$link_url?>"><?=$shopname?> <i class="fas fa-angle-right"></i></a></h3>
                                <?}else{?>
                                <h3><a href="/surfview?seq=<?=$seq?>"><?=$shopname?> <i class="fas fa-angle-right"></i></a></h3>
                                <?}?>
                                <span><a href="javascript:fnMapView('shop<?=$seq?>');"><img src="images/icon/map.svg" alt="">위치</a></span>
                                <span>구매 <?=number_format($row["sell_cnt"])?>개</span>
                                
                                <?
								$shop_info = explode('@', $row["sub_title"]);
								foreach($shop_info as $value){
									echo '<p>✓ '.$value.'</p>';
                                }
                                ?>

                            </li>
                            <li class="price">
                                <?
                                $shop_price = explode('@', $row["sub_info"]);
                                $arrlecture = explode('|', $shop_price[0]);
                                $arrrental = explode('|', $shop_price[1]);
                                ?>
                                <span class="lecture">
                                    <p><span><?=$arrlecture[0]?></span></p>
                                    <p>
                                    <?if($arrlecture[1] != 0) echo number_format($arrlecture[1]).'원';?>
                                    </p>
                                    <p><?=number_format($arrlecture[2])?>원</p>
                                </span>
                                <?if($arrrental[0] == "숙박"){?>
                                <span class="rental">
                                    <p><span><?=$arrrental[1]?></span></p>
                                    <p>
                                    <?if($arrrental[2] != 0) echo number_format($arrrental[2]).'원';?>
                                    </p>
                                    <p><?=number_format($arrrental[3])?>원</p>
                                </span>
                                <?}else if($arrrental[0] == "장비렌탈"){?>
                                <span class="rental">
                                    <p><span><?=$arrrental[0]?></span></p>
                                    <p>
                                    <?if($arrrental[1] != 0) echo number_format($arrrental[1]).'원';?>
                                    </p>
                                    <p><?=number_format($arrrental[2])?>원</p>
                                </span>
                                <?}?>
                                
                            </li>
                            <?if($row["sub_tag"] != ""){?>
                            <li class="event"><span>이벤트</span><?=$row["sub_tag"]?></li>
                            <?}?>
                        </ul>
                    <?}?>
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
            <iframe scrolling="no" frameborder="0" class="ifrmMap" id="ifrmMap" name="ifrmMap" style="width:100%;height:100%;" src="/act/surf/surfmap.html"></iframe>
        </div>
    </div>
</div>

<? include '_layout_bottom.php'; ?>

<script>
var mapView = 0;
var sLng = "<?=$lat?>";
var sLat = "<?=$lng?>";
var MARKER_SPRITE_X_OFFSET = 29,
    MARKER_SPRITE_Y_OFFSET = 50,
    MARKER_SPRITE_POSITION2 = {}
<?=$shopLocation?>
</script>