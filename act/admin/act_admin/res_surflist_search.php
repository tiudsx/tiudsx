<?
$hidsearch = $_REQUEST["hidsearch"];
if($hidsearch == ""){ //초기화면 조회
    $select_query = 'SELECT a.user_name, a.user_tel, a.etc, a.user_email, a.memo, b.*, c.optcode, c.stay_day FROM `AT_RES_MAIN` as a INNER JOIN `AT_RES_SUB` as b 
                        ON a.resnum = b.resnum 
                    INNER JOIN `AT_PROD_OPT` c
                        ON b.optseq = c.optseq
                        WHERE b.res_confirm IN (0,1,2,4,6,8)
                            AND b.code = "surf"
                            ORDER BY b.seq, b.resnum, b.ressubseq';

    $titleText = "전체";
    $listText = "미입금,입금대기,임시확정,환불요청,임시취소,입금완료";    
}else{
    include __DIR__.'/../../db.php';
    $res_confirm = "";
    
    $chkResConfirm = $_REQUEST["chkResConfirm"];
    $sDate = $_REQUEST["sDate"];
    $eDate = $_REQUEST["eDate"];
    $schText = trim($_REQUEST["schText"]);

    $shoplist1 = $_REQUEST["shoplist1"];
    $shoplist2 = $_REQUEST["shoplist2"];
    $shoplist3 = $_REQUEST["shoplist3"];
    
    for($i = 0; $i < count($chkResConfirm); $i++){
        $res_confirm .= $chkResConfirm[$i].',';

        if($chkResConfirm[$i] == 0){
            $listText .= "미입금,";
        }else if($chkResConfirm[$i] == 1){
            $listText .= "입금대기,";
        }else if($chkResConfirm[$i] == 2){
            $listText .= "임시확정/취소,";
            $res_confirm .= '6,';
        }else if($chkResConfirm[$i] == 3){
            $listText .= "확정,";
        }else if($chkResConfirm[$i] == 4){
            $listText .= "환불요청,";
        }else if($chkResConfirm[$i] == 5){
            $listText .= "환불완료,";
        }else if($chkResConfirm[$i] == 7){
            $listText .= "취소,";
        }else if($chkResConfirm[$i] == 8){
            $listText .= "입금완료,";
        }
        // else if($chkResConfirm[$i] == 6){
        //     $listText .= "임시취소,";
        // }
    }
    $res_confirm .= '99';
    
    if($listText != ""){
        $listText = substr($listText, 0, strlen($listText) - 1);
    }

/*
SELECT a.user_name, a.user_tel, a.etc, a.user_email, b.* FROM `AT_RES_MAIN` as a 
    INNER JOIN `AT_RES_SUB` as b 
        ON a.resnum = b.resnum 
    INNER JOIN `AT_PROD_MAIN` as c 
        ON b.seq = c.seq 
    INNER JOIN `AT_CODE` as d
        ON d.code = c.category 
    WHERE b.res_confirm IN (0,1,2,4,6,8,99) AND b.code = "surf" AND (b.res_date BETWEEN CAST("2020-05-01" AS DATE) AND CAST("2020-05-31" AS DATE)) AND d.uppercode = 'surfeast1' AND c.category = 'jukdo' AND b.seq = '183' ORDER BY b.resnum, b.ressubseq
*/
    $shopcate = "";
    if($shoplist1 != "ALL"){
        $shopcate .= " AND d.uppercode = '".$shoplist1."'";
    }
    if($shoplist2 != "ALL"){
        $shopcate .= " AND c.category = '".$shoplist2."'";
    }
    if($shoplist3 != "ALL"){
        $shopcate .= " AND b.seq = '".$shoplist3."'";
    }

    $shopDate = "";
    if($sDate == "" && $eDate == ""){
        $titleText = "전체";
    }else{
        if($sDate != "" && $eDate != ""){
            $shopDate = ' AND (b.res_date BETWEEN CAST("'.$sDate.'" AS DATE) AND CAST("'.$eDate.'" AS DATE))';
        }else if($sDate != ""){
            $shopDate = ' AND b.res_date >= CAST("'.$sDate.'" AS DATE)';
        }else if($eDate != ""){
            $shopDate = ' AND b.res_date <= CAST("'.$eDate.'" AS DATE)';
        }
        $titleText = "[$sDate ~ $eDate]";
    }

    if($schText != ""){
        $schText = ' AND (a.resnum like "%'.$schText.'%" OR a.user_name like "%'.$schText.'%" OR a.user_tel like "%'.$schText.'%")';
    }
    $select_query = 'SELECT a.user_name, a.user_tel, a.etc, a.user_email, a.memo, b.*, e.optcode, e.stay_day FROM `AT_RES_MAIN` as a INNER JOIN `AT_RES_SUB` as b 
                            ON a.resnum = b.resnum 
                        INNER JOIN `AT_PROD_MAIN` as c
                            ON b.seq = c.seq
                        INNER JOIN `AT_CODE` as d
                            ON d.code = c.category
                        INNER JOIN `AT_PROD_OPT` e
                            ON b.optseq = e.optseq
                        WHERE b.res_confirm IN ('.$res_confirm.')
                            AND b.code = "surf"'.$shopDate.$schText.$shopcate.'
                            ORDER BY c.seq, b.resnum, b.ressubseq';
}
// echo $select_query;
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count == 0){
    $select_query = "SELECT * FROM AT_PROD_MAIN WHERE use_yn = 'Y'";
    $result = mysqli_query($conn, $select_query);
    $rowMain = mysqli_fetch_array($result);

?>
 <div class="contentimg bd">
    <div class="gg_first"><?=$titleText?> 예약목록</div>
    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:5px;width:100%;">
    <colgroup>
			<col width="auto" />
			<col width="9%" />
			<col width="9%" />
			<col width="8%" />
			<col width="13%" />
			<col width="15%" />
			<col width="8%" />
			<col width="3%" />
			<col width="5%" />
			<col width="8%" />
			<col width="5%" />
			<col width="5%" />
		</colgroup>
        <tbody>
            <tr>
                <th rowspan="2">입점샵</th>
                <th rowspan="2">예약번호</th>
                <th rowspan="2">이름/연락처</th>
                <th colspan="5">예약항목</th>
                <th rowspan="2">승인처리</th>
                <th rowspan="2">결제금액</th>
                <th rowspan="2">요청사항</th>
                <th rowspan="2">취소사유</th>
            </tr>
            <tr>
                <th style="text-align:center;">이용일</th>
                <th style="text-align:center;">예약항목</th>
                <th style="text-align:center;">예약내용</th>
                <th style="text-align:center;">예약상태</th>
                <th style="text-align:center;">환불</th>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;height:50px;">
                <b>[<?=$listText?>] 건으로 조회된 데이터가 없습니다.</b>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?
	return;
}

$i = 0;
$b = 0;
$c = 0;
$PreMainNumber = "";
$RtnTotalPrice = 0;
$TotalPrice = 0;
$TotalDisPrice = 0;
$res_coupon = "";
$ChangeChk = 0;
$reslist = '';
$reslist1 = '';
$reslistConfirm = "";
while ($row = mysqli_fetch_assoc($result_setlist)){
	$now = date("Y-m-d");
	$MainNumber = $row['resnum'];

	if($MainNumber != $PreMainNumber && $c > 0){
        $i++;

        $trcolor = "";
        if(($i % 2) == 0){
            $trcolor = "class='selTr2'";
        }
    ?>
            <tr name="btnTrList" <?=$trcolor?>>
                <td style="text-align: center;" <?=$rowspan?>><?=$shopname?></td>
                <td style="text-align: center;" <?=$rowspan?>><?=$PreMainNumber?></td>
                <td style="text-align: center;" <?=$rowspan?>><?=$user_name?><br><?=$user_tel?></td>
                <?=$reslist?>
                <td style="text-align: center;" <?=$rowspan?>>
                    <!-- <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:40px; height:30px;background:green;" value="변경" onclick="fnConfirmUpdateList(this, 3, <?=$PreMainNumber?>);" />   -->
                    <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:40px; height:30px;background:green;" value="변경" onclick="fnSurfModify(<?=$PreMainNumber?>);" />  
                </td>
                <td <?=$rowspan?>><b style="font-weight:700;color:red;"><?=number_format($TotalDisPrice).'원'?></b>
                    <?if(($TotalPrice-$TotalDisPrice) > 0){?>
                    <br>(할인:<?=number_format($TotalPrice-$TotalDisPrice).'원'?>)
                    <?}?>
                </td>
                <td style="text-align: center;" <?=$rowspan?>>
                    <?if($etc != ""){?>
                        <span class="btn_view" seq="2<?=$i?>">있음</span><span style='display:none;'><b>요청사항</b><br><?=$etc?></span>
                    <?}?>
                </td>
                <td style="text-align: center;" <?=$rowspan?>>
                    <?if($memo != ""){?>
                        <span class="btn_view" seq="1<?=$i?>">있음</span><span style='display:none;'><b>취소사유</b><br><?=$memo?></span>
                    <?}?>
                </td>
            </tr>
            <?=$reslist1?>
            <tr id="tr<?=$PreMainNumber?>" style="display:none;">
                <td colspan="4"></td>
                <td>취소사유를 작성해주세요~</td>
                <td colspan="3"><textarea id="memo" name="memo" rows="3" style="width: 90%; resize:none;"><?=$memo?></textarea></td>
                <td colspan="4"></td>
            </tr>

    <?
	}

	if($MainNumber == $PreMainNumber){
		$b++;
	}else{
		$b = 0;
        $RtnTotalPrice = 0;
        $TotalPrice = 0;
        $TotalDisPrice = 0;
        $res_coupon = "";
        $ChangeChk = 0;
		$reslist = '';
		$reslist1 = '';
        $reslistConfirm = "";
        $z++;
    }
    
    $shopname = $row['shopname'];
    $shopseq = $row['seq'];
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
    <div class="gg_first"><?=$titleText?> 예약목록</div>
    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:5px;width:100%;">
        <colgroup>
			<col width="auto" />
			<col width="9%" />
			<col width="9%" />
			<col width="8%" />
			<col width="13%" />
			<col width="15%" />
			<col width="8%" />
			<col width="3%" />
			<col width="5%" />
			<col width="9%" />
			<col width="5%" />
			<col width="5%" />
		</colgroup>
        <tbody>
            <tr>
                <th style="background-color:#336600; color:#efefef;" rowspan="2">입점샵</th>
                <th style="background-color:#336600; color:#efefef;" rowspan="2">예약번호</th>
                <th style="background-color:#336600; color:#efefef;" rowspan="2">이름/연락처</th>
                <th style="background-color:#336600; color:#efefef;" colspan="5">예약항목</th>
                <th style="background-color:#336600; color:#efefef;" rowspan="2">승인처리</th>
                <th style="background-color:#336600; color:#efefef;" rowspan="2">결제금액</th>
                <th style="background-color:#336600; color:#efefef;" rowspan="2">요청사항</th>
                <th style="background-color:#336600; color:#efefef;" rowspan="2">취소사유</th>
            </tr>
            <tr>
                <th style="background-color:#336600; color:#efefef;">이용일</th>
                <th style="background-color:#336600; color:#efefef;">예약항목</th>
                <th style="background-color:#336600; color:#efefef;">예약내용</th>
                <th style="background-color:#336600; color:#efefef;">예약상태</th>
                <th style="background-color:#336600; color:#efefef;">환불</th>
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
        $ChangeChk++;
    }else if($ResConfirm == 1){
        $ResConfirmText = "예약대기";
    }else if($ResConfirm == 2){
        $ResConfirmText = "임시확정";
        $TotalPrice += $row['res_price'];
        $TotalDisPrice += $row['res_totalprice'];
        $ChangeChk++;
    }else if($ResConfirm == 6){
        $ResConfirmText = "임시취소";
        $ChangeChk++;
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
        $ChangeChk++;
        $TotalPrice += $row['res_price'];
        $TotalDisPrice += $row['res_totalprice'];
        $RtnTotalPrice += $row['rtn_totalprice'];
    }else if($ResConfirm == 5){
        $ResConfirmText = "환불완료";
        $ResCss = "rescss";
        $ResColor = "canceltext";
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
        //$ResCss = "predate";
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
            //$ResOptInfo = "안내 : $optinfo";
        }
    }else if($row['sub_title'] == "rent"){

    }else if($row['sub_title'] == "pkg"){
        // $ResOptInfo = $optinfo.$TimeDate;
        $ResOptInfo = $optinfo;
    }else if($row['sub_title'] == "bbq"){
        // $ResOptInfo = str_replace('<br>', '', $optinfo);
        // $ResOptInfo = $optinfo;
    }
    $ressubseq = $row['ressubseq'];
    $optname = $row['optname'];

    $RtnPrice = '';
    $RtnBank = '';
    $RtnBankRow = '';
	if($ResConfirm == 4 || $ResConfirm == 5){
		// $RtnPrice = ''.number_format($row['rtn_totalprice']).'원';
		// $RtnBank = '<tr class="'.$ResCss.'" name="btnTrPoint">
		// 				<td style="text-align:center;" colspan="4">'.str_replace('|', '&nbsp ', $row['rtn_bankinfo']).' | 환불액 : '.$RtnPrice.'</td>
        //             </tr>';
        // $RtnBankRow = 'rowspan="2"';
        $RtnPrice = ''.number_format($row['rtn_totalprice']).'원';
		$RtnBank = '<span class="btn_view '.$ResCss.'" seq="3'.$ressubseq.'">계좌</span><span style="display:none;"><b>환불계좌</b><br>'.str_replace('|', '&nbsp ', $row['rtn_bankinfo']).'<br>환불액 : '.$RtnPrice.'</span></td>';
    }

    if($b == 0){
        $reslist = "
                        <td style='text-align:center;'>
                            <input type='hidden' id='MainNumber' name='MainNumber' value='$MainNumber'>
                            <input type='hidden' id='shopseq' name='shopseq' value='$shopseq'>
                            <label>
                            <input type='hidden' id='chkCancel' name='chkCancel[]' resnum='$MainNumber' value='$ressubseq' style='vertical-align:-3px;display:;' /> 
                            $res_date
                            </label>
                        </td>
                        <td>
                            $optname
                        </td>
                        <td>
                            <span class='resoption' style='color:black;'>$TimeDate ($ResNum)</span>
                            <span class='resoption' style='color:black;'>$ResOptInfo</span>
                        </td>
                        <td style='text-align:center;'>";                
            $ResConfirm0 = '';
            $ResConfirm1 = '';
            $ResConfirm2 = '';
            $ResConfirm3 = '';
            $ResConfirm4 = '';
            $ResConfirm5 = '';
            $ResConfirm6 = '';
            $ResConfirm7 = '';
            $ResConfirm8 = '';

        if($ResConfirm == 0) $ResConfirm0 = 'selected';
        if($ResConfirm == 1) $ResConfirm1 = 'selected';
        if($ResConfirm == 2) $ResConfirm2 = 'selected';
        if($ResConfirm == 3) $ResConfirm3 = 'selected';
        if($ResConfirm == 4) $ResConfirm4 = 'selected';
        if($ResConfirm == 5) $ResConfirm5 = 'selected';
        if($ResConfirm == 6) $ResConfirm6 = 'selected';
        if($ResConfirm == 7) $ResConfirm7 = 'selected';
        if($ResConfirm == 8) $ResConfirm8 = 'selected';
        // $reslist .= "
        //                     <select id='selConfirm' name='selConfirm[]' resnum='$MainNumber' class='select' style='padding:1px 2px 4px 2px;' onchange='fnChangeModify(this, $ResConfirm);'>
        //                         <option value='0' ".$ResConfirm0.">미입금</option>
        //                         <option value='1' ".$ResConfirm1.">예약대기</option>
        //                         <option value='2' ".$ResConfirm2.">임시확정</option>
        //                         <option value='3' ".$ResConfirm3.">확정</option>
        //                         <option value='4' ".$ResConfirm4.">환불요청</option>
        //                         <option value='5' ".$ResConfirm5.">환불완료</option>
        //                         <option value='6' ".$ResConfirm6.">임시취소</option>
        //                         <option value='7' ".$ResConfirm7.">취소</option>
        //                         <option value='8' ".$ResConfirm8.">입금완료</option>
        //                     </select>";

        $reslist .= $ResConfirmText."
                        </td>
                        <td style='text-align:center;'>$RtnBank</td>";
    }else{
        $trcolor = "";
        if(($i % 2) == 1 && $i > 0){
            $trcolor = "class='selTr2'";
        }

        $reslist1 .= "
                    <tr name='btnTrList' $trcolor>
                        <td style='text-align:center;'>
                            <input type='hidden' id='MainNumber' name='MainNumber' value='$MainNumber'>
                            <input type='hidden' id='shopseq' name='shopseq' value='$shopseq'>
                            <label>
                            <input type='hidden' id='chkCancel' name='chkCancel[]' resnum='$MainNumber' value='$ressubseq' style='vertical-align:-3px;display:;' /> 
                            $res_date
                            </label>
                        </td>
                        <td>
                            $optname
                        </td>
                        <td>
                            <span class='resoption' style='color:black;'>$TimeDate ($ResNum)</span>
                            <span class='resoption' style='color:black;'>$ResOptInfo</span>
                        </td>
                        <td style='text-align:center;'>";                
            $ResConfirm0 = '';
            $ResConfirm1 = '';
            $ResConfirm2 = '';
            $ResConfirm3 = '';
            $ResConfirm4 = '';
            $ResConfirm5 = '';
            $ResConfirm6 = '';
            $ResConfirm7 = '';
            $ResConfirm8 = '';

        if($ResConfirm == 0) $ResConfirm0 = 'selected';
        if($ResConfirm == 1) $ResConfirm1 = 'selected';
        if($ResConfirm == 2) $ResConfirm2 = 'selected';
        if($ResConfirm == 3) $ResConfirm3 = 'selected';
        if($ResConfirm == 4) $ResConfirm4 = 'selected';
        if($ResConfirm == 5) $ResConfirm5 = 'selected';
        if($ResConfirm == 6) $ResConfirm6 = 'selected';
        if($ResConfirm == 7) $ResConfirm7 = 'selected';
        if($ResConfirm == 8) $ResConfirm8 = 'selected';
        // $reslist1 .= "
        //                     <select id='selConfirm' name='selConfirm[]' resnum='$MainNumber' class='select' style='padding:1px 2px 4px 2px;' onchange='fnChangeModify(this, $ResConfirm);'>
        //                         <option value='0' ".$ResConfirm0.">미입금</option>
        //                         <option value='1' ".$ResConfirm1.">예약대기</option>
        //                         <option value='2' ".$ResConfirm2.">임시확정</option>
        //                         <option value='3' ".$ResConfirm3.">확정</option>
        //                         <option value='4' ".$ResConfirm4.">환불요청</option>
        //                         <option value='5' ".$ResConfirm5.">환불완료</option>
        //                         <option value='6' ".$ResConfirm6.">임시취소</option>
        //                         <option value='7' ".$ResConfirm7.">취소</option>
        //                         <option value='8' ".$ResConfirm8.">입금완료</option>
        //                     </select>";
        $reslist1 .= $ResConfirmText."
                        </td>
                        <td style='text-align:center;'>$RtnBank</td>
                    </tr>";
    }

    $rowspan = "";
    if($b > 0){
        $rowspan = "rowspan='".($b + 1)."'";
    }
//while end
}

$i++;
$trcolor = "";
if(($i % 2) == 0 && $i > 0){
    $trcolor = "class='selTr2'";
}
?>


            <tr name="btnTrList" <?=$trcolor?>>
                <td style="text-align: center;" <?=$rowspan?>><?=$shopname?></td>
                <td style="text-align: center;" <?=$rowspan?>><?=$PreMainNumber?></td>
                <td style="text-align: center;" <?=$rowspan?>><?=$user_name?><br><?=$user_tel?></td>
                <?=$reslist?>
                <td style="text-align: center;" <?=$rowspan?>>
                    <!-- <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:40px; height:30px;background:green;" value="변경" onclick="fnConfirmUpdateList(this, 3, <?=$PreMainNumber?>);" />   -->
                    <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:40px; height:30px;background:green;" value="변경" onclick="fnSurfModify(<?=$PreMainNumber?>);" />  
                </td>
                <td <?=$rowspan?>><b style="font-weight:700;color:red;"><?=number_format($TotalDisPrice).'원'?></b>
                    <?if(($TotalPrice-$TotalDisPrice) > 0){?>
                    <br>(할인:<?=number_format($TotalPrice-$TotalDisPrice).'원'?>)
                    <?}?>
                </td>
                <td style="text-align: center;" <?=$rowspan?>>
                    <?if($etc != ""){?>
                        <span class="btn_view" seq="2<?=$i?>">있음</span><span style='display:none;'><b>요청사항</b><br><?=$etc?></span>
                    <?}?>
                </td>
                <td style="text-align: center;" <?=$rowspan?>>
                    <?if($memo != ""){?>
                        <span class="btn_view" seq="1<?=$i?>">있음</span><span style='display:none;'><b>취소사유</b><br><?=$memo?></span>
                    <?}?>
                </td>
            </tr>
            <?=$reslist1?>
            <tr id="tr<?=$PreMainNumber?>" style="display:none;">
                <td colspan="4"></td>
                <td>취소사유를 작성해주세요~</td>
                <td colspan="3"><textarea id="memo" name="memo" rows="3" style="width: 90%; resize:none;"><?=$memo?></textarea></td>
                <td colspan="4"></td>
            </tr>
		</tbody>
	</table>
	<span id="hidInitParam" style="display:none;">
		<input type="hidden" id="resparam" name="resparam" size="10" value="changeConfirm" class="itx">
		<input type="hidden" id="userid" name="userid" size="10" value="kakaoall" class="itx">
	</span>
</form>
<form name="frmConfirmSel" id="frmConfirmSel" style="display:none;"></form>

</div>


<script type="text/javascript">
$j(document).ready(function(){
	$j(".btn_view[seq]").mouseover(function(e){ //조회 버튼 마우스 오버시
		var seq = $j(this).attr("seq");
		var obj = $j(".btn_view[seq="+seq+"]");
		var tX = (obj.position().left)-354; //조회 버튼의 X 위치 - 레이어팝업의 크기만 큼 빼서 위치 조절
		var tY = (obj.position().top - 20);  //조회 버튼의 Y 위치
		

		if($j(this).find(".box_layer").length > 0){
			if($j(this).find(".box_layer").css("display") == "none"){
				$j(this).find(".box_layer").css({
					"top" : tY
					,"left" : tX
					,"position" : "absolute"
				}).show();
			}
		}else{
				$j(".btn_view[seq="+seq+"]").append('<div class="box_layer"></div>');
				$j(".btn_view[seq="+seq+"]").find(".box_layer").html($j(".btn_view[seq="+seq+"]").next().html());
				$j(".btn_view[seq="+seq+"]").find(".box_layer").css({
					"top" : tY
					,"left" : tX
					,"position" : "absolute"
				}).show();
		}		
	});
	
	$j(".btn_view[seq]").mouseout(function(e){
			$j(this).find(".box_layer").css("display","none");
	});				 
}); 
</script>