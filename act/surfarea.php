<? include 'db.php'; ?>

<?
//정렬순서
$select_query = "SELECT a.code as area, a.codename as areaname, b.code, b.codename, b.lat, b.lng
    FROM AT_CODE a INNER JOIN AT_CODE b
        ON a.uppercode = 'surf'
            AND a.code = b.uppercode
            AND a.use_yn = 'Y'
            AND b.use_yn = 'Y'
        ORDER BY a.ordernum, b.ordernum";

$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

$precode = "";
$areahtml = "";
while ($row = mysqli_fetch_assoc($result_setlist)){
    if($precode != $row["area"]){
        if($precode != "") $areahtml .= "</ul>";

        $areahtml .= "<ul><li class='regionTit'><a href='/surflist?code=".$row["code"]."'>".$row["areaname"]."</a></li>";
    }

    $areahtml .= "<li><a href='/surflist?code=".$row["code"]."'>".$row["codename"]."</a></li>";
    $precode = $row["area"];
}
$areahtml .= "</ul>";

//서핑샵 정보 가져오기
$select_query = "SELECT a.*, b.ad_title1 FROM AT_PROD_MAIN a INNER JOIN AT_PROD_AD b
                    ON a.seq = b.seq
                        AND b.adtype = 'surfcate'
                        AND b.use_yn = 'Y'
                    WHERE a.code = 'surf'
                        AND a.use_yn = 'Y'
                        AND a.view_yn = 'Y'
                        AND a.seq NOT IN (185, 5)
                    ORDER BY rand()";

//임시 인기서핑샵 목록                    
$select_query = "SELECT *, categoryname as ad_title1 FROM AT_PROD_MAIN WHERE code = 'surf' AND use_yn = 'Y' AND view_yn = 'Y' ORDER BY rand() LIMIT 6;";
//AND seq NOT IN (185, 5) 
$result_shopadlist = mysqli_query($conn, $select_query);
$shopadcount = mysqli_num_rows($result_shopadlist);

$select_query = "SELECT * FROM AT_PROD_AD 
                    WHERE adtype = 'surfbeach'
                        AND use_yn = 'Y'
                    ORDER BY rand()";
$result_shopadbeach = mysqli_query($conn, $select_query);
?>

<!-- area menu script -->
<script type="text/javascript">
    $j(document).ready(function() {
    });
</script>

<div id="wrap">
<? include '_layout_top.php'; ?>
<link rel="stylesheet" type="text/css" href="/act/css/surfshop.css">

    <div class="top_area_zone">
        <section id="shopCat">
            <div class="shopCatTit">
                <h2>서핑강습/렌탈</h2>
                <p>지역·해변별 서핑샵 찾기 <i class="fas fa-caret-down"></i></p>
            </div>
            <div class="shopCatList">
                <?=$areahtml?>
                <!--ul class="regionReady">
                    <li><a href="#">준비중..</a></li>
                    <li>
                        <p>더 다양한 샵에서 만나요!</p>
                    </li>
                </ul-->
            </div>
        </section>
        <section id="popShop">
            <h2><i class="far fa-thumbs-up"></i> 인기서핑샵</h2>
            <div class="popShopSldr">
                <div class="swiper-wrapper">
                <?
                $i = 0;
                while ($row = mysqli_fetch_assoc($result_shopadlist)){
                    $shop_img = explode('|', $row["shop_img"]);

                    $shop_price = explode('@', $row["sub_info"]);
                    $arrlecture = explode('|', $shop_price[0]);

                    if($i == 1){
                ?>
                    <!-- <div class="swiper-slide">
                        <a href="/surfview?seq=5">
                            <img src="https://surfenjoy.cdn3.cafe24.com/act_content/soleast/sol.east_thumbnail_200x200.jpg" alt="">
                            <p>대진</p>
                            <p>솔.동해서핑점</p>
                            <p>50,000원</p>
                        </a>
                    </div> -->
                <?
                    }
                ?>
                    <div class="swiper-slide">
                        <a href="/surfview?seq=<?=$row["seq"]?>">
                            <img src="<?=$shop_img[0]?>" alt="">
                            <p><?=$row["ad_title1"]?></p>
                            <p><?=$row["shopname"]?></p>
                            <p><?=number_format($arrlecture[2])?>원</p>
                        </a>
                    </div>
                <?
                $i++;
                }?>
                </div>
            </div>
        </section>
        <section id="popBeach">
            <h2>추천해변</h2>
            <ul>
            <?while ($row = mysqli_fetch_assoc($result_shopadbeach)){?>
                <li><a href="/surflist?code=<?=$row["category"]?>"><span>#</span><?=$row["ad_title1"]?></a></li>
            <?}?>
            </ul>
        </section>

    </div>
</div>

<? include '_layout_bottom.php'; ?>

<!-- Initialize Swiper -->
<script>
    var swiper = new Swiper('.popShopSldr', {
        slidesPerView: 3,
        spaceBetween: 10,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        autoplay: {
            delay: 1500,
            disableOnInteraction: false,
        }
    });
</script>