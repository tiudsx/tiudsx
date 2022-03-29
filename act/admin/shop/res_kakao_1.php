<?
$select_query = 'SELECT a.user_name, a.user_tel, a.etc, a.user_email, a.memo, b.*, c.optcode, c.stay_day FROM `AT_RES_MAIN` as a INNER JOIN `AT_RES_SUB` as b 
					ON a.resnum = b.resnum 
                INNER JOIN `AT_PROD_OPT` c
                    ON b.optseq = c.optseq
					WHERE b.seq = '.$shopseq.'
						AND b.resnum = '.$MainNumber.'
						ORDER BY b.resnum, b.ressubseq';

$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count == 0){
	echo '<div style="text-align:center;font-size:14px;padding:50px;">
				<b>예약된 정보가 없습니다.</b>
			</div>';
	return;
}

$i = 0;
$ChangeChk = 0;
$TotalPrice = 0;
while ($row = mysqli_fetch_assoc($result_setlist)){
	$now = date("Y-m-d");
	$shopname = $row['shopname'];
	$user_name = $row['user_name'];
	$MainNumber = $row['resnum'];
	$etc = $row['etc'];
	$memo = $row['memo'];
	$res_coupon = $row['res_coupon'];

	if($i == 0){
?>

    <div class="top_area_zone">
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1><?=$shopname?></h1>
                <a class="reviewlink">
                    <span class="reviewcnt"><b>[<?=$user_name?>]님 예약건</b> (<?=$row['insdate']?>)</span>
                </a>
                <div class="shopsubtitle">예약번호 : <?=$MainNumber?></div>
            </div>
        </section>

        <section class="notice">
            <div class="vip-tabwrap">
                <div id="tabnavi" class="fixed1" style="top: 49px;">
                    <div class="vip-tabnavi">
                        <ul>
                            <li class="on"><a>예약건 안내</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="view_tab1">
                <div class="noticeline" id="content_tab1">
                    <article>
                        <p class="noticesub">예약건 처리안내</p>
                        <ul>
                            <li class="litxt">상태 항목에서 <label style="color:#059bc0;">[확정], [취소]</label> 선택 처리 해주세요.</li>
                            <li class="litxt">매진 및 기타 상황으로 예약진행이 불가능한 경우 취소 처리 해주시면 됩니다.</li>
                            <!-- <li class="litxt">
                                <span>    
                                액트립 서핑버스 이용금액은 부가세 별도금액입니다.<br>
                                <span>현금영수증 신청은 이용일 이후 <label style="color:#059bc0;">[카카오 채널 @액트립]</label> 에서 신청가능합니다.</span>
                                </span>
                            </li> -->
                        </ul>
                        
                        <?if($res_coupon == "ATBLOG"){?>
                        <p class="noticesub">네이버 블로그 체험단 예약건입니다.</p>
                        <?}?>
                    </article>
                </div>
                <div class="contentimg bd">

<form name="frmConfirm" id="frmConfirm" autocomplete="off">
    <table class="et_vars exForm bd_tb" style="margin-bottom:5px;width:100%;">
		<colgroup>
			<col width="20%" />
			<col width="auto;" />
		</colgroup>
        <tbody>
			<tr>
                <th>예약번호</th>
                <td><?=$MainNumber?></td>
            </tr>
			<tr>
                <th>예약자</th>
                <td><?=$user_name?><span>  (<?=$row['user_tel']?>)</span></td>
            </tr>
			<tr>
				<td colspan="2">

    <table class="et_vars exForm bd_tb" style="width:100%">
		<colgroup>
            <col style="width:80px;">
            <col style="width:auto;">
            <col style="width:70px;">
        </colgroup>
        <tbody>
			<tr>
                <th style="text-align:center;">이용일</th>
				<th style="text-align:center;">예약항목</th>
				<th style="text-align:center;">상태</th>
			</tr>
<?
    }
    $i++;
    
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
	if($ResConfirm == 0){
		$ResConfirmText = "미입금";
	}else if($ResConfirm == 1){
		$ResConfirmText = "예약대기";
	}else if($ResConfirm == 2){
		$ResConfirmText = "임시확정";
		$totalPrice += $row['res_price'];
	}else if($ResConfirm == 6){
		$ResConfirmText = "임시취소";
	}else if($ResConfirm == 8){
        $ResConfirmText = "입금완료";
        $ChangeChk++;
	}else if($ResConfirm == 3){
		$ResConfirmText = "확정";
		$ResColor = "rescolor3";
		$totalPrice += $row['res_price'];
	}else if($ResConfirm == 4){
		$ResConfirmText = "환불요청";
		$ResColor = "rescolor1";
	}else if($ResConfirm == 5){
		$ResConfirmText = "환불완료";
		$ResCss = "rescss";
	}else if($ResConfirm == 7){
		$ResConfirmText = "취소";
		$ResCss = "rescss";
	}

	if ($datDate < date("Y-m-d", strtotime($now." 0 day")))
	{
		$ResCss = "predate";
	}

	$TimeDate = "";
    if(($row['sub_title'] == "lesson" || $row['sub_title'] == "pkg") && $row['res_time'] != ""){
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
    if($row['sub_title'] == "lesson"){
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
    }else if($row['sub_title'] == "rent"){

    }else if($row['sub_title'] == "pkg"){
        // $ResOptInfo = $optinfo.$TimeDate;
        $ResOptInfo = $optinfo;
    }else if($row['sub_title'] == "bbq"){
        //$ResOptInfo = str_replace('<br>', '', $optinfo);
        //$ResOptInfo = $optinfo;
    }

?>
            <tr>
                <td style="text-align:center;">   
                    <input type="hidden" id="MainNumber" name="MainNumber" value="<?=$MainNumber?>">                 
					<label>
					<input type="checkbox" id="chkCancel" name="chkCancel[]" value="<?=$row['ressubseq']?>" style="vertical-align:-3px;display:none;" />
					<?=$row['res_date']?>
					</label>
				</td>
                <td>
					<?=$row['optname']?><br>
					<span class="resoption"><?=$TimeDate?> (<?=$ResNum?>)</span>
					<span class="resoption"><?=$ResOptInfo?></span>
				</td>
                <td style="text-align:center;">
                    <?if($ResConfirm == 8){?>
                        <select id="selConfirm" name="selConfirm[]" resnum='<?=$MainNumber?>' class="select" style="padding:1px 2px 4px 2px;" onchange="fnChangeModify(this, <?=$ResConfirm?>);">
                            <option value="<?=$ResConfirm?>">승인처리</option>
                            <option value="3">확정</option>
                            <option value="6">취소</option>
                        </select>
                    <?}else{
                        echo $ResConfirmText;
                    }?>
                </td>
			</tr>
<?
}
?>
		</tbody>
	</table>
				</td>
            </tr>
		<?if($TotalPrice > 0){?>
            <tr>
                <th>결제금액</th>
                <td><?=number_format($TotalPrice).'원'?></td>
            </tr>
		<?}?>
		<?if($etc != ""){?>
            <tr>
                <th>요청사항</th>
                <td><textarea id="etc" name="etc" rows="5" style="width: 90%; resize:none;" disabled="disabled"><?=$etc?></textarea></td>
            </tr>
		<?}?>
        <?if($ChangeChk > 0 || $memo != ""){?>
            <tr id="tr<?=$MainNumber?>" style="display:;">
                <th>취소사유</th>
                <td>                
                    <textarea id="memo" name="memo" rows="3" style="width: 90%; resize:none;"><?=$memo?></textarea>
                </td>
            </tr>
        <?}?>
		</tbody>
	</table>
<?if($ChangeChk > 0){?>
    <div class="write_table" style="padding-top:15px;padding-bottom:15px;text-align:center;">
        <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:140px; height:40px;" value="상태변경하기" onclick="fnConfirmUpdate(this, 1);" />
	</div>
<?}?>
	<span id="hidInitParam" style="display:none;">
		<input type="hidden" id="resparam" name="resparam" size="10" value="changeConfirm" class="itx">
		<input type="hidden" id="userid" name="userid" size="10" value="kakao1" class="itx">
        <input type="hidden" id="shopseq" name="shopseq" value="<?=$shopseq?>">
	</span>
</form>
<form name="frmConfirmSel" id="frmConfirmSel" style="display:none;" target="ifrmResize"></form>

                </div>
                <div>
                    <div style="padding:10px 0 5px 0;font-size:12px;">
                        <a href="http://pf.kakao.com/_HxmtMxl" target="_blank" rel="noopener"><img src="/act/images/kakaochat.jpg" class="placeholder"></a>
                    </div>
                </div>
            </div>
        </section>
    </div>