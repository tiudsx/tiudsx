<? include 'db.php'; ?>

<?
$reqSeq = $_REQUEST["seq"];

if($reqSeq == ""){
    echo '<script>alert("잘못된 접속 경로입니다.");location.href="/surf";</script>';
	exit;
}
$select_query = "SELECT * FROM AT_PROD_MAIN WHERE seq = $reqSeq";
$result = mysqli_query($conn, $select_query);
$rowMain = mysqli_fetch_array($result);
$count = mysqli_num_rows($result);

if($count == 0){
    echo '<script>alert("서핑샵 정보가 없습니다.\n\n관리자에게 문의해주세요~");location.href="/surf";</script>';
    exit;
}

$reqCode = $rowMain["category"];
$shop_img = explode('|', $rowMain["shop_img"]);
$coupon_yn = $rowMain["coupon_yn"];

// 옵션 매진여부 확인
$select_query = "SELECT a.*, b.optcode, b.optname FROM `AT_PROD_OPT_SOLDOUT` as a INNER JOIN AT_PROD_OPT as b
					ON a.seq = b.seq
						AND a.optseq = b.optseq
					WHERE a.seq = $reqSeq AND b.use_yn = 'Y' ORDER BY a.soldout_date, a.optseq";
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count > 0){
	$SoldoutList = "";
	$Presoldoutdate = "";
	$x = 0;

	while ($rowSold = mysqli_fetch_assoc($result_setlist)){
		$soldoutdate = $rowSold['soldout_date'];

		if($soldoutdate != $Presoldoutdate && $x > 0){
			$SoldoutList .= "main['".$Presoldoutdate."'] = sub;";
		}

		if($soldoutdate == $Presoldoutdate){
			$i++;
		}else{
			$i = 0;
		}

		$x++;
		$Presoldoutdate = $rowSold['soldout_date'];

		if($i == 0){
			$SoldoutList .= "sub = new Object();";
		}

		$soldoutdate = $rowSold["soldout_date"];
		$optseq = $rowSold["optseq"];
		$opt_sexM = $rowSold["opt_sexM"];
		$opt_sexW = $rowSold["opt_sexW"];
		$optcode = $rowSold["optcode"];
		$optname = $rowSold["optname"];
		
		$SoldoutList .= "sub['$optseq'] = {type: '$optcode', opt_sexM: '$opt_sexM', opt_sexW: '$opt_sexW', optseq: $optseq, optname: '$optname' }; ";
	}
	
	$SoldoutList .= "main['".$Presoldoutdate."'] = sub;";
}

$select_query = 'SELECT * FROM `AT_PROD_OPT` where seq = '.$reqSeq.' AND use_yn = "Y" ORDER BY ordernum';
$result_setlist = mysqli_query($conn, $select_query);

$arrOpt = array();
$arrOptT = array();
while ($rowOpt = mysqli_fetch_assoc($result_setlist)){
	$arrOpt[$rowOpt["optcode"]][$rowOpt["optseq"]] = array("optseq" => $rowOpt["optseq"], "optname" => $rowOpt["optname"], "opttime" => $rowOpt["opttime"], "opt_sexM" => $rowOpt["opt_sexM"], "opt_sexW" => $rowOpt["opt_sexW"], "sell_price" => $rowOpt["sell_price"], "opt_info" => $rowOpt["opt_info"], "stay_day" => $rowOpt["stay_day"]);

	$arrOptT[$rowOpt["optcode"]] = $rowOpt["optcode"];
}

$sLng = $rowMain["shop_lat"];
$sLat = $rowMain["shop_lng"];

//연락처 모바일 여부
if(Mobile::isMobileCheckByAgent()) $inputtype = "number"; else $inputtype = "text";
?>

<div id="wrap">
    <? include '_layout_top.php'; ?>

    <link rel="stylesheet" href="css/surfview.css">

    <div class="top_area_zone">
        
        <link rel="stylesheet" href="/act/css/surfview_cate.css">

        <section id="viewSlide">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <?foreach ($shop_img as $key => $value) {
                        if($key == 0 || $value == "") {
                            continue;
                        }
                    ?>
                    <div class="swiper-slide">
                        <div class="swiperimg swiper-slide" style="background-image:url(<?=$value?>);">
                        </div>
                    </div>
                    <?}?>
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
                <!-- Add Arrows -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </section>
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1>[<?=$rowMain["categoryname"]?>] <?=$rowMain["shopname"]?></h1>
                <div class="shopsubtitle">주소 : <?=$rowMain["shopaddr"]?></div>
            </div>
            
        </section>

        <section class="notice">
            <div class="vip-tabwrap">
                <div id="tabnavi" class="fixed1" style="top: 49px;">
                    <div class="vip-tabnavi">
                        <ul>
                            <li class="on"><a>위치안내</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="view_tab1">
                <div class="noticeline" id="content_tab1">
                    <article>
                        <p class="noticesub">서핑강습 안내</p>
                        <ul>
                            <li class="litxt">강습 예약 시 강습시간 10분 전에 샵에 방문해주세요.</li>
                            <li class="litxt">슈트 안에 입을 수영복, 비키니, 래시가드 등</li>
                            <li class="litxt">개인 세면도구 (수건은 기본 한장 제공됩니다)</li>
                            <li class="litxt">선케어제품 (워터프루프 썬스틱 추천합니다)</li>
                            <li class="litxt">슬리퍼 (해변에서 물에 들어가기 위해 이동 시 필요합니다)</li>
                        </ul>
                    </article>
                </div>
                <div id="shopmap">
                    <iframe scrolling="no" frameborder="0" id="ifrmMap" name="ifrmMap" style="width:100%;height:490px;" src="surf/surfmap.html"></iframe>

                    <div style="padding:10px 0 5px 0;font-size:12px;">
                        <a href="http://pf.kakao.com/_HxmtMxl" target="_blank" rel="noopener"><img src="images/kakaochat.jpg" class="placeholder"></a>
                    </div>
                </div>
                <div class="noticeline2" id="cancelinfo">
                    <p class="noticetxt">취소/환불 안내</p>
                    <article>
                        <p class="noticesub">취소 안내</p>
                        <ul>
                            <li class="litxt">우천시 정상적으로 서핑강습이 진행됩니다.</li>
                            <li class="litxt">기상악화 및 천재지변으로 인하여 예약이 취소될 경우 전액환불해드립니다.</li>
                        </ul>
                    </article>
                    <article>
                        <p class="noticesub">환불 규정안내</p>
                        <ul>
                            <li class="refund"><img src="images/refund.jpg" alt=""></li>
                        </ul>
                    </article>
                </div>
            </div>
        </section>
    </div>
</div>
<? include '_layout_bottom.php'; ?>

<script>
$j(document).ready(function() {
    var swiper = new Swiper('.swiper-container', {
		loop: true,
		autoplay: {
            delay: 2000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
});

var mapView = 1;
var sLng = "<?=$rowMain["shoplat"]?>";
var sLat = "<?=$rowMain["shoplng"]?>";
var MARKER_SPRITE_X_OFFSET = 29,
    MARKER_SPRITE_Y_OFFSET = 50,
    MARKER_SPRITE_POSITION2 = {
        '<?=$rowMain["shopname"]?>': [0, MARKER_SPRITE_Y_OFFSET * 3, sLng, sLat, '<?=$rowMain["shopaddr"]?>', '구매 <b><?=number_format($rowMain["sell_cnt"])?></b>개', 0, <?=$reqSeq?>, '<?=$shop_img[0]?>', '<?=$rowMain["categoryname"]?>', '<?=$rowMain["shopname"]?>']
    };

var main = new Object();
</script>