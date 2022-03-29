<?php
include __DIR__.'/../../surf/surffunc.php';

$reqDate = $_REQUEST["selDate"];
if($reqDate == ""){
    $selDate = str_replace("-", "", date("Y-m-d"));
}else{
	include __DIR__.'/../../db.php';
    $shopseq = $_REQUEST["seq"];
    $selDate = $reqDate;
}
$Year = substr($selDate,0,4);
$Mon = substr($selDate,4,2);

if($_REQUEST["chkResConfirm"] == ""){
    $res_confirm = "0, 1, 8, 4";
}else{
	include __DIR__.'/../../db.php';
    $res_confirm = "";

    $chkResConfirm = $_REQUEST["chkResConfirm"];
    $chkbusNum = $_REQUEST["chkbusNum"];
    $sDate = $_REQUEST["sDate"];
    $eDate = $_REQUEST["eDate"];
    $schText = trim($_REQUEST["schText"]);
    
    for($b = 0; $b < count($chkResConfirm); $b++){
        $res_confirm .= $chkResConfirm[$b].',';
    }
    $res_confirm .= '99';

    $inResType = "";
    for($b = 0; $b < count($chkbusNum); $b++){
        $inResType .= '"'.$chkbusNum[$b].'",';
    }
    $inResType .= '99';
}

if($sDate == ""){
    $select_query = 'SELECT a.user_name, a.user_tel, a.etc, a.user_email, b.* FROM `AT_RES_MAIN` as a INNER JOIN `AT_RES_SUB` as b 
                        ON a.resnum = b.resnum 
                        WHERE b.seq IN (7, 14)
                            AND b.res_confirm IN ('.$res_confirm.')
                            ORDER BY b.resnum, b.ressubseq';
}else{
    $busDate = "";
    if($sDate == "" && $eDate == ""){
    }else{
        if($sDate != "" && $eDate != ""){
            $busDate = ' AND (res_date BETWEEN CAST("'.$sDate.'" AS DATE) AND CAST("'.$eDate.'" AS DATE))';
        }else if($sDate != ""){
            $busDate = ' AND res_date >= CAST("'.$sDate.'" AS DATE)';
        }else if($eDate != ""){
            $busDate = ' AND res_date <= CAST("'.$eDate.'" AS DATE)';
        }
    }

    if($schText != ""){
        $schText = ' AND (a.resnum like "%'.$schText.'%" OR a.user_name like "%'.$schText.'%" OR a.user_tel like "%'.$schText.'%")';
    }

    $select_query = 'SELECT a.user_name, a.user_tel, a.etc, a.user_email, a.memo, b.* FROM `AT_RES_MAIN` as a INNER JOIN `AT_RES_SUB` as b 
                        ON a.resnum = b.resnum 
                        WHERE b.res_confirm IN ('.$res_confirm.')
                            AND res_busnum IN ('.$inResType.')'.$busDate.$schText.' 
                            ORDER BY b.resnum, b.res_date, b.ressubseq';
}

//echo $select_query;
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count == 0){
?>
 <div class="contentimg bd">
 <div class="gg_first">셔틀버스 예약정보</div>
    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:5px;width:100%;">
    <colgroup>
        <col width="31%" />
        <col width="25%" />
        <col width="22%" />
        <col width="22%" />
    </colgroup>
    <tbody>
        <tr>
            <th rowspan="2">예약번호</th>
            <th>이용일</th>
            <th colspan="2">이름</th>
        </tr>
        <tr>
            <th>상태</th>
            <th>승인여부</th>
            <th>요청사항</th>
        </tr>
            <tr>
                <td colspan="5" style="text-align:center;height:50px;">
                <b>예약된 목록이 없습니다.</b>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?
	return;
}

$b = 0;
$c = 0;
$PreMainNumber = "";
$RtnTotalPrice = 0;
$TotalPrice = 0;
$TotalDisPrice = 0;
$res_coupon = "";
$ChangeChk = 0;
$reslist = "";
$reslistConfirm = "";
$busNum = "";
while ($row = mysqli_fetch_assoc($result_setlist)){
	$now = date("Y-m-d");
	$MainNumber = $row['resnum'];

	if($MainNumber != $PreMainNumber && $c > 0){
?>

            <tr name="btnTrList" style="text-align:center;cursor:pointer;" onclick="fnListViewKakao(this);">
                <td style="text-align: center;border-bottom: 3px solid #efefef;" rowspan="2"><?=$PreMainNumber?></td>
                <td style="text-align: center;"><?=$res_date?></td>
                <td style="text-align: center;" colspan="2"><?=$user_name?><br>(<?=$user_tel?>)</td>
            </tr>
            <tr>
                <td style="text-align: center;border-bottom: 3px solid #efefef;"><?=substr($reslistConfirm, 0, strlen($reslistConfirm) - 1)?></td>
                <td style="text-align: center;border-bottom: 3px solid #efefef;"><?if($ChangeChk > 0){ echo "승인필요"; }else{ echo "O"; }?></td>
                <td style="text-align: center;border-bottom: 3px solid #efefef;"><?if($etc != ""){ echo "있음"; }?><?if($res_coupon == "JOABUS"){ echo "[조아]"; }else if($res_coupon == "NAVER"){ echo "[NAVER]"; }else if($res_coupon == "KLOOK"){ echo "[KLOOK]"; }else if($res_coupon != ""){ echo "[할인]"; }?></td>
            </tr>
            
            <tr id="<?=$PreMainNumber?>" style="display:none;">
                <td colspan="4">

                    <table class="et_vars exForm bd_tb" style="width:100%">
                        <colgroup>
                            <col style="width:80px;">
                            <col style="width:*;">
                            <col style="width:90px;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <th style="text-align:center;" rowspan="2">이용일</th>
                                <th style="text-align:center;">행선지</th>
                                <th style="text-align:center;">현재상태</th>
                            </tr>
                            <tr>
                                <th style="text-align:center;">정류장</th>
                                <th style="text-align:center;">상태</th>
                            </tr>
                            <?=$reslist?>
                        </tbody>
                    </table>
                    <table class="et_vars exForm bd_tb" style="width:100%">
                        <colgroup>
                            <col style="width:80px;">
                            <col style="width:auto;">
                        </colgroup>
                        <tbody>
                        <?if($RtnTotalPrice > 0){?>
                            <tr>
                                <th>환불금액</th>
                                <td><b><?=number_format($RtnTotalPrice).'원'?></b></td>
                            </tr>
                        <?}?>
                        <?if($TotalPrice > 0){?>
                            <tr>
                                <th>결제금액</th>
                                <td><b style="font-weight:700;color:red;"><?=number_format($TotalDisPrice).'원'?></b> (<?=number_format($TotalPrice).'원'?> - 할인쿠폰:<?=number_format($TotalPrice-$TotalDisPrice).'원'?>)</td>
                            </tr>
                        <?}?>
                        <?if($etc != ""){?>
                            <tr>
                                <th>요청사항</th>
                                <td><textarea id="etc" name="etc" rows="5" style="width: 90%; resize:none;" disabled="disabled"><?=$etc?></textarea></td>
                            </tr>
                        <?}?>
                            <tr>
                                <th>사유 및<br>메모</th>
                                <td>
                                    <textarea id="memo" name="memo" rows="3" style="width: 90%; resize:none;"><?=$memo?></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="write_table" style="padding-bottom:15px;text-align:center;">
                        <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:90px; height:30px;" value="상태변경하기" onclick="fnConfirmUpdateBus(this);" />
                    </div>
                </td>
            </tr>
<?
	}

	if($MainNumber == $PreMainNumber){
		$b++;
	}else{
		$RtnTotalPrice = 0;
        $TotalPrice = 0;
        $TotalDisPrice = 0;
        $res_coupon = "";
		$b = 0;
        $ChangeChk = 0;
        $reslist = "";
        $reslistConfirm = "";
        $busNum = "";
    }
    
	$shopname = $row['shopname'];
	$user_name = $row['user_name'];
	$user_tel = $row['user_tel'];
	$PreMainNumber = $row['resnum'];
	$etc = $row['etc'];
    $memo = $row['memo'];
    $res_date = $row['res_date'];

    if($c == 0){
?>
        <div class="contentimg bd">
        <form name="frmConfirm" id="frmConfirm" autocomplete="off">
        <div class="gg_first">셔틀버스 예약정보</div>
            <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:5px;width:100%;">
                <colgroup>
                    <col width="31%" />
                    <col width="25%" />
                    <col width="22%" />
                    <col width="22%" />
                </colgroup>
                <tbody>
                    <tr>
                        <th rowspan="2">예약번호</th>
                        <th>이용일</th>
                        <th colspan="2">이름</th>
                    </tr>
                    <tr>
                        <th>상태</th>
                        <th>승인여부</th>
                        <th>요청사항</th>
                    </tr>
<?
    }

    $c++;

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
	$datDate = substr($row['res_date'], 0, 10);

    $ResConfirm = $row['res_confirm'];
    $res_coupon = $row['res_coupon'];    
    if($ResConfirm == 0){
        $ResConfirmText = "미입금";
		$TotalPrice += $row['res_price'];
        $TotalDisPrice += $row['res_totalprice'];
        $ChangeChk++;
    }else if($ResConfirm == 1){
        $ResConfirmText = "예약대기";
    }else if($ResConfirm == 2){
        $ResConfirmText = "임시확정";
        $TotalPrice += $row['res_price'];
        $TotalDisPrice += $row['res_totalprice'];
    }else if($ResConfirm == 6){
        $ResConfirmText = "임시취소";
    }else if($ResConfirm == 8){
        $ResConfirmText = "입금완료";
        $TotalPrice += $row['res_price'];
        $TotalDisPrice += $row['res_totalprice'];
        $ChangeChk++;
    }else if($ResConfirm == 3){
        $ResConfirmText = "확정";
        $ResColor = "rescolor3";
        $TotalPrice += $row['res_price'];
        $TotalDisPrice += $row['res_totalprice'];
    }else if($ResConfirm == 4){
        $ResConfirmText = "환불요청";
        $ResColor = "rescolor1";
        $TotalPrice += $row['res_price'];
        $TotalDisPrice += $row['res_totalprice'];
        $RtnTotalPrice += $row['rtn_totalprice'];
    }else if($ResConfirm == 5){
        $ResConfirmText = "환불완료";
        $ResCss = "rescss";
        $TotalPrice += $row['res_price'];
        $TotalDisPrice += $row['res_totalprice'];
        $RtnTotalPrice += $row['rtn_totalprice'];
    }else if($ResConfirm == 7){
        $ResConfirmText = "취소";
        $ResCss = "rescss";
    }

    $str_pos = strpos($reslistConfirm, $ResConfirmText);
    if($str_pos === false)
    {
        $reslistConfirm .= $ResConfirmText."/";
    }
    

	if ($datDate < date("Y-m-d", strtotime($now." 0 day")))
	{
		$ResCss = "resper";
	}

    
    $ressubseq = $row['ressubseq'];
    
	$RtnPrice = '';
    $RtnBank = '';
    $RtnBankRow = '';
	if($ResConfirm == 4 || $ResConfirm == 5){
		$RtnPrice = ''.number_format($row['rtn_totalprice']).'원';
		$RtnBank = '<tr class="'.$ResCss.'" name="btnTrPoint">
						<td style="text-align:center;" colspan="4">'.str_replace('|', '&nbsp ', $row['rtn_bankinfo']).' | 환불액 : '.$RtnPrice.'</td>
                    </tr>';
        $RtnBankRow = 'rowspan="2"';
    }

    $busNumText = fnBusNum($row['res_busnum']);
    $busNum .= $busNumText.',';
    
    
$reslist .= "
<tr>
     <td style='text-align:center;' rowspan='2'>
         <input type='hidden' id='MainNumber' name='MainNumber' value='$MainNumber'>
         <label>
         <input type='checkbox' id='chkCancel' name='chkCancel[]' value='$ressubseq' style='vertical-align:-3px;' /><br>
         $res_date
         </label>
     </td>
     <td style='text-align:center;' onclick='fnModifyInfo(\"bus\", $ressubseq, 1);'>".$busNumText." ".$row['res_seat']."번</td>
     <td style='text-align:center;'>".$ResConfirmText."</td>
</tr>
<tr>
     <td style='text-align:center;'>".$row["res_spointname"]." -> ".$row["res_epointname"]."</td>
     <td style='text-align:center;' $RtnBankRow>";

     $ResConfirm0 = '';
     $ResConfirm1 = '';
     $ResConfirm3 = '';
     $ResConfirm4 = '';
     $ResConfirm5 = '';
     $ResConfirm7 = '';
     $ResConfirm8 = '';

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

if($ResConfirm == 0) $ResConfirm0 = 'selected';
if($ResConfirm == 1) $ResConfirm1 = 'selected';
if($ResConfirm == 3) $ResConfirm3 = 'selected';
if($ResConfirm == 4) $ResConfirm4 = 'selected';
if($ResConfirm == 5) $ResConfirm5 = 'selected';
if($ResConfirm == 7) $ResConfirm7 = 'selected';
if($ResConfirm == 8) $ResConfirm8 = 'selected';
$reslist .= "
        <select id='selConfirm' name='selConfirm[]' class='select' style='padding:1px 2px 4px 2px;' onchange='fnChangeModify(this, $ResConfirm);'>
            <option value='0' ".$ResConfirm0.">미입금</option>
            <option value='1' ".$ResConfirm1.">예약대기</option>
            <option value='3' ".$ResConfirm3.">확정</option>
            <option value='4' ".$ResConfirm4.">환불요청</option>
            <option value='5' ".$ResConfirm5.">환불완료</option>
            <option value='7' ".$ResConfirm7.">취소</option>
            <option value='8' ".$ResConfirm8.">입금완료</option>
        </select>";
$reslist .= "
     </td>
 </tr>";
 $reslist .= $RtnBank;
//while end
}

?>

            <tr name="btnTrList" style="text-align:center;cursor:pointer;" onclick="fnListViewKakao(this);">
                <td style="text-align: center;border-bottom: 3px solid #efefef;" rowspan="2"><?=$PreMainNumber?></td>
                <td style="text-align: center;"><?=$res_date?></td>
                <td style="text-align: center;" colspan="2"><?=$user_name?><br>(<?=$user_tel?>)</td>
            </tr>
            <tr>
                <td style="text-align: center;border-bottom: 3px solid #efefef;"><?=substr($reslistConfirm, 0, strlen($reslistConfirm) - 1)?></td>
                <td style="text-align: center;border-bottom: 3px solid #efefef;"><?if($ChangeChk > 0){ echo "승인필요"; }else{ echo "O"; }?></td>
                <td style="text-align: center;border-bottom: 3px solid #efefef;"><?if($etc != ""){ echo "있음"; }?><?if($res_coupon == "JOABUS"){ echo "[조아]"; }else if($res_coupon == "NAVER"){ echo "[NAVER]"; }else if($res_coupon == "KLOOK"){ echo "[KLOOK]"; }else if($res_coupon != ""){ echo "[할인]"; }?></td>
            </tr>
            <tr id="<?=$PreMainNumber?>" style="display:none;">
                <td colspan="4">

                    <table class="et_vars exForm bd_tb" style="width:100%">
                        <colgroup>
                            <col style="width:80px;">
                            <col style="width:*;">
                            <col style="width:90px;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <th style="text-align:center;" rowspan="2">이용일</th>
                                <th style="text-align:center;">행선지</th>
                                <th style="text-align:center;">현재상태</th>
                            </tr>
                            <tr>
                                <th style="text-align:center;">정류장</th>
                                <th style="text-align:center;">상태</th>
                            </tr>
                            <?=$reslist?>
                        </tbody>
                    </table>
                    <table class="et_vars exForm bd_tb" style="width:100%">
                        <colgroup>
                            <col style="width:80px;">
                            <col style="width:auto;">
                        </colgroup>
                        <tbody>
                        <?if($RtnTotalPrice > 0){?>
                            <tr>
                                <th>환불금액</th>
                                <td><b><?=number_format($RtnTotalPrice).'원'?></b></td>
                            </tr>
                        <?}?>
                        <?if($TotalPrice > 0){?>
                            <tr>
                                <th>결제금액</th>
                                <td><b style="font-weight:700;color:red;"><?=number_format($TotalDisPrice).'원'?></b> (<?=number_format($TotalPrice).'원'?> - 할인쿠폰:<?=number_format($TotalPrice-$TotalDisPrice).'원'?>)</td>
                            </tr>
                        <?}?>
                        <?if($etc != ""){?>
                            <tr>
                                <th>요청사항</th>
                                <td><textarea id="etc" name="etc" rows="5" style="width: 90%; resize:none;" disabled="disabled"><?=$etc?></textarea></td>
                            </tr>
                        <?}?>
                            <tr>
                                <th>사유 및<br>메모</th>
                                <td>
                                    <textarea id="memo" name="memo" rows="3" style="width: 90%; resize:none;"><?=$memo?></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="write_table" style="padding-bottom:15px;text-align:center;">
                        <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:90px; height:30px;" value="상태변경하기" onclick="fnConfirmUpdateBus(this);" />
                    </div>
                </td>
            </tr>
		</tbody>
	</table>
	<span id="hidInitParam" style="display:none;">
		<input type="hidden" id="resparam" name="resparam" size="10" value="changeConfirm" class="itx">
		<input type="hidden" id="userid" name="userid" size="10" value="admin" class="itx">
		<input type="hidden" id="changeConfirm" name="changeConfirm" size="10" value="1" class="itx">
	</span>
</form>

<form name="frmConfirmSel" id="frmConfirmSel" style="display:none;"></form>
</div>