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

	$chkView = 0;
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

	if($res_coupon == "NAVERA" || $res_coupon == "NABUSA" || $res_coupon == "NABUSB" || $res_coupon == "NABUSC"){
		$chkView = 0;
		$cancelChk = "NAVERA";
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

	//============= 환불금액 구역 =============
	if($row['code'] == "bus"){
		$RtnBank = str_replace("|", " / ", fnBusPoint($row['res_spointname'], $row['res_bus']));
		$ResNum = "구매수:".$row['res_ea'];
	}else{
		$RtnBank = '';
		
		//============= 예약항목 구역 =============
		$TimeDate = "";
		if(($row['optcode'] == "lesson" || $row['optcode'] == "pkg") && $row['res_time'] != ""){
			$TimeDate = '강습시간 : '.$row['res_time'];
		}
		
		$ResNum = "";
		if($row['res_m'] > 0){
			$ResNum = "남:".$row['res_m']."명";
		}
		if($row['res_m'] > 0 && $row['res_w'] > 0){
            $ResNum .= ",";
        }
		if($row['res_w'] > 0){
			$ResNum .= "여:".$row['res_w']."명";
		}

		$ResOptInfo = "";
		$optinfo = $row['optsubname'];
        if($row['optcode'] == "lesson"){
			$arrdate = explode("-", $row['res_date']); // 들어온 날짜를 년,월,일로 분할해 변수로 저장합니다.
			$s_Y=$arrdate[0]; // 지정된 년도 
			$s_m=$arrdate[1]; // 지정된 월
			$s_d=$arrdate[2]; // 지정된 요일
		
            $stayPlus = $row['stay_day']; //숙박 여부
            //이전일 요일구하기
            $preDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." -1 day"));
            $nextDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." +1 day"));
            if($stayPlus == 0){
                $ResOptInfo = "숙박일 : ".$row['res_date']."(1박)";
            }else if($stayPlus == 1){
                $ResOptInfo = "숙박일 : $preDate(1박)";
            }else if($stayPlus == 2){
                $ResOptInfo = "숙박일 : $preDate(2박)";
            }else{
                // $ResOptInfo = "안내 : $optinfo";
            }
        }else if($row['optcode'] == "rent"){

        }else if($row['optcode'] == "pkg"){
			$ResOptInfo = $optinfo;
        }else if($row['optcode'] == "bbq"){
			//$ResOptInfo = str_replace('<br>', '', $optinfo);
			$ResOptInfo = $optinfo;
        }
	}

	// 환불금액 표시
	if($res_confirm == 4 || $res_confirm == 5){
		$RtnBank = '<b>환불금액 : '.number_format($row['rtn_totalprice']).'원</b> ('.str_replace('|', '&nbsp ', $row['rtn_bankinfo']).')';
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
		if($RtnTotalPrice > 0){
		?>
    <table class="et_vars exForm bd_tb" style="width:100%">
        <tbody>
			<colgroup>
				<col style="width:100px;">
				<col style="width:auto;">
			</colgroup>
			<tr>
                <th style="text-align:center;">총 환불금액</th>
                <td style="text-align:left;padding-left:10px;" colspan="3"><b><?=number_format($RtnTotalPrice).'원'?></b></td>
            </tr>
		</tbody>
	</table>
		<?
		}
		if($PointChangeChk > 0 && $row['code'] == "bus"){
		?>
	
	<div class="write_table" style="text-align:center;">
	<input type="button" class="gg_btn gg_btn_grid large" style="width:150px; height:28px;color: #fff !important; background: #3195db;display:;" value="좌석/정류장 변경 신청" onclick="location.href='/pointchange?num=<?=$num?>&resNumber=<?=$row['res_num']?>';" />
	</div>
	
		<?
		}

		if($row['res_price_coupon'] > 0){
			$res_price_coupon = $row['res_price_coupon'];
	
			if($res_price_coupon <= 100){ //퍼센트 할인
				$res_totalprice = $totalPrice * (1 - ($res_price_coupon / 100));
			}else{ //금액할인
				$res_totalprice = $totalPrice - $res_price_coupon;
			}
		}else{
			$res_totalprice = $totalPrice;
		}
		?>
				</td>
			</tr>
			<tr>
                <th scope="row">결제금액</th>
                <td><b style="font-weight:700;color:red;"><?=number_format($res_totalprice)?>원</b> (<?=number_format($totalPrice)?>원 - 할인쿠폰:<?=number_format($totalPrice - $res_totalprice)?>원)</td>
            </tr>
			<?if($shopbankview > 0){?>
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