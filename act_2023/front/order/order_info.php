<?php 
$param_mid = $_REQUEST["mid"];

if($param_mid == ""){
	$param = str_replace("/", "", $_SERVER["REQUEST_URI"]);

	if (!empty(strpos($_SERVER["REQUEST_URI"], '?'))){
		$param = substr($param, 0, strpos($_SERVER["REQUEST_URI"], '?') - 1);
	}

	$param = explode('_', $param)[0];
}else{
	$param = $param_mid;
}

$resNumber = str_replace(' ', '', $_REQUEST["resNumber"]);
$num = $_REQUEST["num"];
$gubun = substr($resNumber, 0, 1);

$title = "액트립";
$infoUrl = "order_info_view.php";
$funUrl = "/../../common/func.php";
$dbTableMain = "`AT_RES_MAIN`";
$dbTableSub = "`AT_RES_SUB`";

include __DIR__.'/../../common/db.php';
include __DIR__.$funUrl;

if($gubun == 1){ //서핑샵
	$select_query = "SELECT a.*, b.*, a.resnum as res_num, TIMESTAMPDIFF(MINUTE, b.insdate, now()) as timeM, c.optcode, c.stay_day 
						FROM $dbTableMain a INNER JOIN $dbTableSub as b 
							ON a.resnum = b.resnum
								AND b.code = 'surf'
						INNER JOIN `AT_PROD_OPT` c
							ON b.optseq = c.optseq
						WHERE a.resnum = $resNumber
							ORDER BY a.resnum, b.ressubseq";

}else{ //셔틀버스
	$select_query = "SELECT a.*, b.*, a.resnum as res_num, TIMESTAMPDIFF(MINUTE, b.insdate, now()) as timeM, TIMESTAMPDIFF(MINUTE, b.confirmdate, now()) as timeM2, d.couponseq
						FROM $dbTableMain a INNER JOIN $dbTableSub as b 
							ON a.resnum = b.resnum 
						LEFT JOIN AT_COUPON_CODE d ON b.res_coupon = d.coupon_code
						WHERE a.resnum = $resNumber
							ORDER BY b.bus_oper DESC, b.res_seat";

	
	$select_row_query = "SELECT a.res_confirm, d.couponseq
							FROM $dbTableSub a LEFT JOIN AT_COUPON_CODE d 
								ON a.res_coupon = d.coupon_code
							WHERE a.resnum = $resNumber AND a.res_confirm = 3 AND a.res_date > DATE_FORMAT(now(), '%Y-%m-%d')";
}

$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count == 0){
	echo "<script>alert('예약된 정보가 없습니다.');location.href='/ordersearch';</script>";
	return;
}

$result_row = mysqli_query($conn, $select_row_query);
$count_row = mysqli_num_rows($result_row);
$rowMain = mysqli_fetch_array($result_row);

$pkg_btn = "";
if($rowMain["couponseq"] == 17 || $rowMain["couponseq"] == 26){ //마린서프
	$pkg_btn = "/act_2023/front/bus_pkg/surf_gisa.html";
}else if($rowMain["couponseq"] == 20 || $rowMain["couponseq"] == 27){ //인구서프, 엉클 프립
	$pkg_btn = "/act_2023/front/bus_pkg/surf_ingu.html";
}else if($rowMain["couponseq"] == 22 || $rowMain["couponseq"] == 29){ //솔게하
	$pkg_btn = "/act_2023/front/bus_pkg/surf_dh.html";
}
?>

<script type="text/javascript" src="/act_2023/front/_js/ordersearch.js?v=<?=time()?>"></script>

<div id="wrap">
    <? include __DIR__.'/../../_layout/_layout_top.php'; ?>

    <link rel="stylesheet" type="text/css" href="/act_2023/front/_css/surfview.css">
    <link rel="stylesheet" type="text/css" href="/act_2023/front/_css/bus.css">

    <div class="top_area_zone">
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1><?=$title?> 예약조회</h1>
            </div>
			<div id="seatTab" class="busOption01" style="padding: 0px 10px;">
				<ul class="busLineTab" style="display: block;">
				<?if($count_row > 0){?>
					<li class="on" style="cursor:pointer; font-size:1.1em; width:130px; text-align:left;" onclick="fnLayerView('/busgps');">실시간 위치조회</li>
				<?}?>
					<li class="on" style="cursor:pointer; font-size:1.1em; width:105px; text-align:left;" onclick="fnLayerView('/pointlist');">정류장 안내</li>
				<?if($pkg_btn != ""){?>
					<li class="on" style="cursor:pointer; font-size:1.1em; width:105px; text-align:left;" onclick="fnLayerView('<?=$pkg_btn?>');">패키지 안내</li>
				<?}?>
				</ul>
			</div>
        </section>
        <section class="notice">
            <div class="bd" style="padding:0 4px;min-height:300px;">
			<form name="frmCancel" id="frmCancel" target="ifrmResize" autocomplete="off">
				
				<input type="hidden" id="gubun" name="gubun" value="">
				<input type="hidden" id="hidtotalPrice" name="hidtotalPrice" value="0">
				<input type="hidden" id="resparam" name="resparam" value="Cancel">
				<input type="hidden" id="userId" name="userId" value="">
				<input type="hidden" id="MainNumber" name="MainNumber" value="<?=$resNumber?>">
				<?
				include_once($infoUrl);
				
				if($gubun != 3 && $param == "orderview"){
					if($cancelChk == "coupon"){
						echo '<div class="write_table" style="padding-top:2px;padding-bottom:15px;">';
						echo '※ 취소/환불은 카카오채널 [액트립]으로 문의해주세요~<br>';
						echo '<span style="font-weight:500;font-size:12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- 이용일 4일 이전 취소 시 : 전액 환불<br>';
						echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- 이용일 3일 이전 취소 시 : 환불 불가</span>';
						echo '</div>';
					}else{
						echo '<div class="write_table" style="padding-top:2px;padding-bottom:15px;">';
						echo '※ 이용 1일전에는 취소/환불이 안됩니다.<br>';
						echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;단, 예약확정 후 2시간 이내에는 취소/환불이 가능합니다.';
						echo '</div>';
					}
				}
				?>

				<span id="returnBank" style="display:none;">
					<div class="gg_first">취소/환불 수수료 예정금액</div>
					<table class="et_vars exForm bd_tb" style="width:100%">
						<tbody>
							<tr>
								<th style="text-align:center;">총 이용금액</th>
								<th style="text-align:center;">환불수수료</th>
								<th style="text-align:center;">환불금액</th>
							</tr>
							<tr>
								<td style="text-align:center;"><span id="tdCancel1">0</span> 원</td>
								<td style="text-align:center;"><span id="tdCancel2">0</span> 원</td>
								<td style="text-align:center;"><span id="tdCancel3">0</span> 원</td>
							</tr>
						</tbody>
					</table>
					<div class="write_table" style="padding-top:5px;padding-bottom:15px;">
					※ 취소신청 시간에 따라 환불수수료 예정금액과 차이가 있을 수 있습니다.
					</div>

					<div class="gg_first">환불 계좌 <span style="font-weight:100;font-size:12px;font-family:Tahoma,Geneva,sans-serif;">(예약자와 동일한 명의의 계좌번호로 환불가능합니다.)</span></div>
					<table class="et_vars exForm bd_tb" style="width:100%">
						<tbody>
							<tr>
								<th style="text-align:center;">예금자명</th>
								<th style="text-align:center;">은행이름</th>
								<th style="text-align:center;">계좌번호</th>
							</tr>
							<tr>
								<td style="text-align:center;">
									<input type="hidden" id="bankUserName" name="bankUserName" value="<?=$bankUserName?>" class="itx" style="width:50px;"><?=$bankUserName?>
								</td>
								<td style="text-align:center;">
									<input type="text" id="bankName" name="bankName" value="" class="itx" style="width:80px;">
								</td>
								<td style="text-align:center;"><input type="text" id="bankNum" name="bankNum" value="" class="itx" style="width:130px;"></td>
							</tr>
						</tbody>
					</table>
				</span>

				<?if($gubun != 3){?>
					<div class="write_table" style="padding-top:15px;padding-bottom:15px;text-align:center;">
						<?
						if($num == 1){
							echo '		<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:140px; height:40px;" value="메인으로" onclick="location.href=\'/\';" />';
						}else if($num == 2){
							echo '		<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:140px; height:40px;" value="메인으로" onclick="location.href=\'/\';" />';
						}else if($param == "order_kakao"){
							
						}else{
							echo '		<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:140px; height:40px;" value="돌아가기" onclick="location.href=\'/ordersearch\';" />';
						}?>
						<?if($btnDisplay && $cancelChk != "coupon" && $param == "orderview"){?>
							&nbsp;<input type="button" class="gg_btn gg_btn_grid large" style="width:140px; height:40px;color: #fff !important; background: #008000;display:'.$cancelChk.';" value="취소/환불 신청" onclick="fnRefund(<?=$num?>);" />
						<?}?>				
					</div>
				<?}?>
			</form>
			</div>

            <iframe id="ifrmResize" name="ifrmResize" style="width:800px;height:400px;display:none;"></iframe>

        </section>
    </div>
</div>
<div class="con_footer">
    <div class="fixedwidth resbottom">
		<input type="button" class="btnsurfdel" id="slide1" style="width:120px; height:40px;display:none;" value="창닫기" onclick="fnLayerView('');" >
        <div id="sildeing" style="display:block;height:100%;padding-top:5px;">
            <iframe frameborder="0" id="ifrmView" name="ifrmView" style="width:100%;height:92%;" scrolling="auto"></iframe>
        </div>
    </div>
</div>
<div id="banner" class="on">
	<img src="https://developers.kakao.com/assets/img/about/logos/kakaotalksharing/kakaotalk_sharing_btn_medium.png" onclick="shareMessage('<?=$resNumber?>', '<?=$bankUserName?>');" alt="카카오톡 공유 보내기 버튼" style="width:50px;">	
</div>
<style>
#banner {
  position: fixed;
  right: 20px;
  bottom: 50px;
  width: 50px;
  height: 50px;
}

#banner.on {
  position: absolute;
  bottom: 157px;
}
</style>
<script src="https://t1.kakaocdn.net/kakao_js_sdk/2.2.0/kakao.min.js" integrity="sha384-x+WG2i7pOR+oWb6O5GV5f1KN2Ko6N7PTGPS7UlasYWNxZMKQA63Cj/B2lbUmUfuC" crossorigin="anonymous"></script>
<script>
	Kakao.init('15043b4ab2fd95556fa77e2c604d421e'); // 사용하려는 앱의 JavaScript 키 입력
    var type = "";
    var btnheight = "";

	$j(function() {
		var $w = $j(window),
			footerHei = $j('.footer_Util_wrap00').outerHeight(),
			$banner = $j('#banner');

		$w.on('scroll', function() {
			var sT = $w.scrollTop();
			var val = $j(document).height() - $w.height() - footerHei;
			
			if (sT >= val)
				$banner.addClass('on')
			else
				$banner.removeClass('on')
		});
	});

	function fnLayerView(url) {
		if (btnheight == "") btnheight = $j(".con_footer").height();
		if (type == "down") {
			$j("#ifrmView").css("display", "none");
			$j("#ifrmView").attr("src", "about:blank");
			$j(".con_footer").css("height", btnheight + "px");
			$j("#slide1").css("display", "none");
			$j(".con_footer").css("background-color", "");
			$j(".resbottom").css("background-color", "");

			type = "";
		} else {
			$j("#ifrmView").attr("src", url);
			$j("#ifrmView").css("display", "");
			$j(".con_footer").css("height", "100%");
			$j("#slide1").css("display", "");
			$j(".resbottom").css("height", "100%");
			$j(".con_footer").css("background-color", "white");
			$j(".resbottom").css("background-color", "white");

			type = "down";
		}
	}	
</script>
<? include __DIR__.'/../../_layout/_layout_bottom.php'; ?>