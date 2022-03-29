<?php 
include __DIR__.'/../db.php';
include __DIR__.'/../surf/surffunc.php';


$resNumber = str_replace(' ', '', $_REQUEST["resNumber"]);
$num = $_REQUEST["num"];

$gubun = substr($resNumber, 0, 1);
$cancelChk = "none";

if($gubun == 1){
	$select_query = "SELECT a.*, b.*, a.resnum as res_num, TIMESTAMPDIFF(MINUTE, b.insdate, now()) as timeM, c.optcode, c.stay_day 
						FROM `AT_RES_MAIN` a LEFT JOIN `AT_RES_SUB` as b 
							ON a.resnum = b.resnum
								AND b.code = 'surf'
						INNER JOIN `AT_PROD_OPT` c
							ON b.optseq = c.optseq
						WHERE a.resnum = $resNumber
							ORDER BY a.resnum, b.ressubseq";

}else{
	$select_query = "SELECT *, a.resnum as res_num, TIMESTAMPDIFF(MINUTE, b.insdate, now()) as timeM, TIMESTAMPDIFF(MINUTE, b.confirmdate, now()) as timeM2 
						FROM `AT_RES_MAIN` a LEFT JOIN `AT_RES_SUB` as b 
							ON a.resnum = b.resnum 
						WHERE a.resnum = $resNumber
							ORDER BY a.resnum, b.ressubseq";
}
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count == 0){
	echo "<script>alert('예약된 정보가 없습니다.');location.href='/ordersearch';</script>";
	return;
}else{
	//echo "<script>fnOrderDisplay(1);</script>";
}
?>

<script src="../js/surfordersearch.js"></script>

<div id="wrap">
<? include  __DIR__.'/../_layout_top.php'; ?>

    <link rel="stylesheet" href="../css/surfview.css">

    <div class="top_area_zone">
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1>액트립 예약조회</h1>
            </div>
        </section>
        <section class="notice">
            <div class="bd" style="padding:0 4px;min-height:300px;">
			<form name="frmCancel" id="frmCancel" target="ifrmResize" autocomplete="off">
				<?
				include_once("surforder_info.php");
				
				if($cancelChk == "none"){
					echo '<div class="write_table" style="padding-top:2px;padding-bottom:15px;display:none;">
					※ 이용 1일전에는 취소가 불가능합니다.
					</div>';
				}else if($cancelChk == "NAVERA"){
					echo '<div class="write_table" style="padding-top:2px;padding-bottom:15px;display:;">
					※ 취소/환불은 네이버를 통해서 해주세요~
					</div>';
				}
				?>
				<div class="write_table" style="padding-top:2px;padding-bottom:15px;">
				※ 이용 1일전에는 취소/환불이 안됩니다.<br>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;단, 예약확정 후 2시간 이내에는 취소/환불이 가능합니다.
				</div>

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
								<td style="text-align:center;"><input type="hidden" id="bankUserName" name="bankUserName" value="<?=$bankUserName?>" class="itx" style="width:50px;"><?=$bankUserName?></td>
								<td style="text-align:center;">
									<input type="hidden" id="gubun" name="gubun" value="">
									<input type="hidden" id="hidtotalPrice" name="hidtotalPrice" value="0">
									<input type="hidden" id="resparam" name="resparam" value="Cancel">
									<input type="hidden" id="userId" name="userId" value="">
									<input type="hidden" id="MainNumber" name="MainNumber" value="">
									<input type="text" id="bankName" name="bankName" value="" class="itx" style="width:80px;">
								</td>
								<td style="text-align:center;"><input type="text" id="bankNum" name="bankNum" value="" class="itx" style="width:130px;"></td>
							</tr>
						</tbody>
					</table>
				</span>

					<div class="write_table" style="padding-top:15px;padding-bottom:15px;text-align:center;">
						<?
						if($num == 1){
							echo '		<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:140px; height:40px;" value="메인으로" onclick="location.href=\'/\';" />';
						}else if($num == 2){
							//echo '		<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:140px; height:40px;" value="돌아가기" onclick="fnOrderDisplay(0);" />';
							echo '		<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:140px; height:40px;" value="돌아가기" onclick="history.back();" />';
						}else{
							//echo '		<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:140px; height:40px;" value="돌아가기" onclick="fnOrderDisplay(0);" />';
							echo '		<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:140px; height:40px;" value="돌아가기" onclick="location.href=\'/ordersearch\';" />';
						}?>
						<?if($cancelChk == "none" || $cancelChk == "NAVERA"){?>
						<?}else{?>
							&nbsp;<input type="button" class="gg_btn gg_btn_grid large" style="width:140px; height:40px;color: #fff !important; background: #008000;display:'.$cancelChk.';" value="취소/환불 신청" onclick="fnRefund(<?=$num?>);" />
						<?}?>
					</div>
			</form>
			</div>

            <iframe id="ifrmResize" name="ifrmResize" style="width:800px;height:400px;display:none;"></iframe>

        </section>
    </div>
</div>

<? include __DIR__.'/../_layout_bottom.php'; ?>