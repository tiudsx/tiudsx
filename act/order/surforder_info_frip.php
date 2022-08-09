<?
$i = 1;
$couponPrice = 0;
$totalPrice = 0;
$shopbankview = 0;
$PointChangeChk = 0;
while ($row = mysqli_fetch_assoc($result_setlist)){
	$now = date("Y-m-d");

	$surfSeatInfo = "";
	$arrSeatInfo = array();

	$bankUserName = $row['user_name'];
	$res_confirm = $row['res_confirm'];
	$res_coupon = $row['res_coupon'];
	$couponseq = $row['couponseq'];

	$chkView = 0;
	$chkViewPrice = 1;
	$datDate = $row['res_date'];
	
	$chkView = 0;
	$chkViewPrice = 0;
	$cancelChk = "coupon";
/*
예약상태
    0 : 미입금
    1 : 예약대기
    2 : 임시확정
    3 : 확정
    4 : 환불요청
    5 : 환불완료
    6 : 임시취소
    7 : 취소
    8 : 입금완료
*/
    $ResColor = "";
    $ResCss = "";

	if($res_confirm == 0){
		$ResConfirm = "미입금";
		$totalPrice += $row['res_price'];
		$shopbankview++;
		$PointChangeChk++;
	}else if($res_confirm == 1 || $res_confirm == 2){
		$ResConfirm = "확인중";
		$ResColor = "rescolor2";
		$totalPrice += $row['res_price'];
		$PointChangeChk++;
	}else if($res_confirm == 6 || $res_confirm == 8){
		$ResConfirm = "입금완료";
		$ResColor = "rescolor2";
		$totalPrice += $row['res_price'];
		$PointChangeChk++;
	}else if($res_confirm == 3){
		$ResConfirm = "확정";
		$ResColor = "rescolor3";
		$totalPrice += $row['res_price'];
		$PointChangeChk++;
	}else if($res_confirm == 4){
		$ResConfirm = "환불요청";
		$ResColor = "rescolor1";
        $totalPrice += $row['res_price'];
        
        $RtnTotalPrice += $row['rtn_totalprice']; //환불금액 표시
	}else if($res_confirm == 5){
		$ResConfirm = "환불완료";
		$ResCss = "rescss";
		$totalPrice += $row['res_price'];
	}else if($res_confirm == 7){
		$ResConfirm = "취소";
		$ResCss = "rescss";
	}

	if ($datDate < date("Y-m-d", strtotime($now." 0 day")))
	{
		$ResCss = "resper";
	}
	
	$RtnBank = str_replace("|", " / ", fnBusPoint($row['res_spointname'], $row['res_bus'], $datDate));

	if($i == 1){
?>
    <table class="et_vars exForm bd_tb">
		<colgroup>
			<col width="20%" />
			<col width="auto;" />
		</colgroup>
        <tbody>
			<tr>
                <th>예약자</th>
                <td><?=$row['user_name']?><span style="display:;">  (<?=$row['user_tel']?>)</span></td>
            </tr>
            <tr>
                <th colspan="2">[<?=$row['shopname']?>] 예약정보</th>
            </tr>
			<tr>
				<td colspan="2">
    <table class="et_vars exForm bd_tb" style="width:100%">
        <tbody>
			<colgroup>
				<col style="width:80px;">
				<col style="width:auto;">
				<col style="width:70px;">
			</colgroup>
			<tr>
                <th style="text-align:center;">이용일</th>
				<th style="text-align:center;">예약항목</th>
				<th style="text-align:center;">상태</th>
			</tr>
<?
	}
?>
			<tr class="<?=$ResCss?>">
                <td style="text-align:center;" rowspan="2">
					<label>
				<?if($chkView == 1){?>
					<input type="checkbox" id="chkCancel" name="chkCancel[]" value="<?=$row['ressubseq']?>" style="vertical-align:-3px;" onclick="fnCancelSum(this, '<?=$row['code']?>', '<?=$row['res_num']?>');" />
				<?}else{?>
					<input type="checkbox" disabled="disabled" />
				<?}?>
					<br><?=$row['res_date']?>
					</label>
				</td>
				<td><b><?=fnBusNum($row['res_bus'])?> : <?=$row['res_seat']?>번</b><br><span style="padding-left:10px;"><?=$row['res_spointname']?> -> <?=$row['res_epointname']?></span></td>
				<td style="text-align:center;" class="<?=$ResColor?>"><?=$ResConfirm?></td>
			</tr>
			<tr class="<?=$ResCss?>">
				<td colspan="2"><?=$RtnBank?></td>
			</tr>
<?
	if($i == $count){?>
		</tbody>
	</table>

					<div class="write_table" style="text-align:center;">
					<input type="button" class="gg_btn gg_btn_grid large" style="width:150px; height:28px;color: #fff !important; background: #3195db;display:;" value="좌석/정류장 변경 신청" onclick="location.href='/pointchangeFrip?num=<?=$num?>&resNumber=<?=$row['res_num']?>';" />
					</div>
				</td>
			</tr>
			<?if(!($row['etc'] == "")){?>
            <tr>
                <th scope="row">요청사항</th>
                <td><textarea id="etc" name="etc" rows="5" style="width: 90%; resize:none;" disabled="disabled"><?=$row['etc']?></textarea></td>
            </tr>
			<?}?>
		</tbody>
	</table>
<?
	}
	$i++;
}
?>