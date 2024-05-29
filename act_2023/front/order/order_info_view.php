<?
$i = 1;
$couponPrice = 0;
$Price = 0;
$totalPrice = 0;
$shopbankview = 0;
$PointChangeChk = 0;
$shopseq = 0;
$btnDisplay = false;
$btnDisplay2 = false;
while ($row = mysqli_fetch_assoc($result_setlist)){
	$now = date("Y-m-d");

	$surfSeatInfo = "";
	$arrSeatInfo = array();

	$bankUserName = $row['user_name'];
	$res_confirm = $row['res_confirm'];
	$res_coupon = $row['res_coupon'];
	$couponseq = $row['couponseq'];
	$shopseq = $row['seq'];

	$chkView = 0;
	$chkViewPrice = 1;
	$datDate = $row['res_date'];
	if($datDate >= $now){
		if($res_confirm == 0 || $res_confirm == 6){
			$chkView = 1;
		} else if($res_confirm == 3){
			$cancelDate = date("Y-m-d", strtotime($datDate." -1 day"));
			if($row['timeM2'] <= 120 || $cancelDate > $now){
				$chkView = 1;
			}
		}
	}
	
	if($chkView == 1){
		$cancelChk = "";
	}

	//타채널 및 100% 쿠폰 사용건
	if(fnCouponCode($couponseq)["gubun"]){
		$chkView = 0;
		$chkViewPrice = 0;
		$cancelChk = "coupon";
	}
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
		$Price += $row['res_price'];
		$totalPrice += $row['res_totalprice'];
		$shopbankview++;
		$PointChangeChk++;

		$btnDisplay = true;
	}else if($res_confirm == 1 || $res_confirm == 2){
		$ResConfirm = "확인중";
		$ResColor = "rescolor2";
		$Price += $row['res_price'];
		$totalPrice += $row['res_totalprice'];
		$PointChangeChk++;
	}else if($res_confirm == 6 || $res_confirm == 8){
		$ResConfirm = "입금완료";
		$ResColor = "rescolor2";
		$Price += $row['res_price'];
		$totalPrice += $row['res_totalprice'];
		$PointChangeChk++;
	}else if($res_confirm == 3){
		$ResConfirm = "확정";
		$ResColor = "rescolor3";
		$Price += $row['res_price'];
		$totalPrice += $row['res_totalprice'];
		$PointChangeChk++;
		
		$btnDisplay = true;
		$btnDisplay2 = true;
	}else if($res_confirm == 4){
		$ResConfirm = "환불요청";
		$ResColor = "rescolor1";
		$Price += $row['res_price'];
        $totalPrice += $row['res_totalprice'];
        
        $RtnTotalPrice += $row['rtn_totalprice']; //환불금액 표시
	}else if($res_confirm == 5){
		$ResConfirm = "환불완료";
		$ResCss = "rescss";
		$Price += $row['res_price'];
		$totalPrice += $row['res_totalprice'];
		
        $RtnTotalPrice += $row['rtn_totalprice']; //환불금액 표시
	}else if($res_confirm == 7){
		$ResConfirm = "취소";
		$ResCss = "rescss";
	}

	if ($datDate < date("Y-m-d", strtotime($now." 0 day")))
	{
		$ResCss = "resper";
	}

	//============= 환불금액 구역 =============
	//셔틀버스 탑승 정보
	$arrPoint = fnBusPointArr2023($row['bus_gubun']."_".$row['res_spointname'], $shopseq, 0);
	$arrTime = fnBusPointArr2023($row['bus_gubun']."_".$row['res_spointname'], $shopseq, 1);

	$RtnBank = "탑승시간 : ".$arrTime." (".$arrPoint.")";
	$ResNum = "구매수:".$row['res_ea'];

	// 환불금액 표시
	if($res_confirm == 4 || $res_confirm == 5){
		if($row['rtn_totalprice'] == 0){ //관리자 환불처리
			$RtnBank = "";
		}else{
			$RtnBank = '<b style="color:#e34a00">환불금액 : '.number_format($row['rtn_totalprice']).'원</b> ('.str_replace('|', '&nbsp ', $row['rtn_bankinfo']).')';
		}
	}

	if($i == 1){
?>
    <table class="et_vars exForm bd_tb">
		<colgroup>
			<col width="20%" />
			<col width="auto;" />
		</colgroup>
        <tbody>
			<tr>
                <th>예약번호</th>
                <td><strong><?=$row['res_num']?></strong><span style="display:none;"> (<?=$row['insdate']?>)</span></td>
            </tr>
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

	if($row['code'] == "bus"){?>
			<tr class="<?=$ResCss?>">
                <td style="text-align:center;" rowspan="2">
					<label>
				<?if($chkView == 1){?>
					<?if($res_confirm == 0){?>
					<input type="checkbox" id="chkCancel" name="chkCancel[]" value="<?=$row['ressubseq']?>" checked="checked" style="vertical-align:-3px;display:none;" />
					<?}else{?>
					<input type="checkbox" id="chkCancel" name="chkCancel[]" value="<?=$row['ressubseq']?>" style="vertical-align:-3px;" onclick="fnCancelSum(this, '<?=$row['code']?>', '<?=$row['res_num']?>');" /><br>
					<?}?>
				<?}else{?>
					<input type="checkbox" disabled="disabled" style="display:none;"/>
				<?}?>
					<?=$row['res_date']?>
					</label>
				</td>
				<td><b><?=fnBusNum2023($row['bus_gubun'].$row['bus_num'])["full"]?> : <?=$row['res_seat']?>번</b><br><span style="padding-left:10px;"><?=$row['res_spointname']?> -> <?=$row['res_epointname']?></span></td>
				<td style="text-align:center;" class="<?=$ResColor?>"><?=$ResConfirm?></td>
			</tr>
			<tr class="<?=$ResCss?>">
				<td colspan="2"><?=$RtnBank?></td>
			</tr>
	<?}else if($row['code'] == "surf"){?>
			<tr class="<?=$ResCss?>">
                <td style="text-align:center;">
					<label>
				<?if($chkView == 1){?>
					<input type="checkbox" id="chkCancel" name="chkCancel[]" value="<?=$row['ressubseq']?>" style="vertical-align:-3px;" onclick="fnCancelSum(this, '<?=$row['code']?>', '<?=$row['res_num']?>');" />
				<?}else{?>
					<input type="checkbox" disabled="disabled" />
				<?}?>
					<br><?=$row['res_date']?>
					</label>
				</td>
                <td>
					<?=$row['optname']?><br>
					<span class="resoption"><?=$TimeDate?> (<?=$ResNum?>)</span>
					<span class="resoption"><?=$ResOptInfo?></span>
				</td>
                <td style="text-align:center;" class="<?=$ResColor?>"><?=$ResConfirm?></td>
			</tr>
			<?=$RtnBank?>
	<?}?>
<?
	if($i == $count){?>
		</tbody>
	</table>

		
		<?
		if($PointChangeChk > 0 && $row['code'] == "bus" && $count_row > 0){
		?>
		<div class="write_table" style="text-align:center;">
		<input type="button" class="gg_btn gg_btn_grid large" style="width:100px; height:28px;color: #fff !important; background: #9326ff;display:none;" value="내좌석 보기"  onclick="fnLayerView('/seatview?num=<?=$num?>&resNumber=<?=$row['res_num']?>');" />

		<?if($btnDisplay2 && $param == "orderview"){?>
		<input type="button" class="gg_btn gg_btn_grid large" style="width:110px; height:28px;color: #fff !important; background: #3195db;display:;" value="좌석/정류장 변경" onclick="fnLayerView('/pointchange?num=<?=$num?>&resNumber=<?=$row['res_num']?>');" />
		<?}?>
		</div>
		<?
		}
		?>
				</td>
			</tr>
			<?if($chkViewPrice == 1 && $totalPrice > 0){?>
			<tr>
                <th scope="row">결제금액</th>
                <td>
					<b style="font-weight:700;color:red;"><?=number_format($totalPrice)?>원</b>
					<?if(($Price - $totalPrice) > 0){?>
				 	(할인쿠폰 : <?=number_format($Price - $totalPrice)?>원)
					<?}?>
				</td>
            </tr>
			<?}
			
			if($RtnTotalPrice > 0){
			?>
			<tr>
				<th scope="row">총 환불금액</th>
				<td><b><?=number_format($RtnTotalPrice).'원'?></b></td>
			</tr>

			<?}
			
			if($shopbankview > 0){?>
			<tr>
                <th scope="row">입금계좌</th>
                <td>
				<?if($row['pay_info'] == "무통장입금"){
					echo "우리은행 / 1002-845-467316 / 이승철";
				}else{
					echo $row['pay_info'];
				}?>
				</td>
            </tr>
			<?}?>
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