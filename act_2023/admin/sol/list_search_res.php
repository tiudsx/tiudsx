<?
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/kakaoalim.php';

$schText = $_REQUEST["schText"];
$sDate = $_REQUEST["sDate"];
$eDate = $_REQUEST["eDate"];

$chkResConfirm = $_REQUEST["chkResConfirm"];
for($b = 0; $b < count($chkResConfirm); $b++){
    $confirmText .= "'".$chkResConfirm[$b]."',";
}
$confirmText .= "'99'";

$selDate = "";
if($sDate == "" && $eDate == ""){
}else{
    if($sDate != "" && $eDate != ""){
        $eDate2 = date("Y-m-d", strtotime($eDate." -1 day"));
        $selDate = " AND ((b.resdate BETWEEN CAST('$sDate' AS DATE) AND CAST('$eDate' AS DATE)) 
                        OR 
                        (('$sDate' BETWEEN b.sdate AND DATE_ADD(b.edate, INTERVAL -1 DAY) OR '$eDate2' BETWEEN b.sdate AND DATE_ADD(b.edate, INTERVAL -1 DAY))
                        OR (b.sdate BETWEEN '$sDate' AND '$eDate2' OR DATE_ADD(b.edate, INTERVAL -1 DAY) BETWEEN '$sDate' AND '$eDate2')))";
    }else if($sDate != ""){
        $selDate = " AND (b.resdate >= CAST('$sDate' AS DATE)
                        OR
                        (b.sdate >= CAST('$sDate' AS DATE) OR b.edate >= CAST('$sDate' AS DATE)))";
    }else if($eDate != ""){
        $selDate = " AND (b.resdate <= CAST('$eDate' AS DATE)
                        OR
                        (b.sdate <= CAST('$eDate' AS DATE) OR b.edate <= CAST('$eDate' AS DATE)))
                        AND (b.sdate != '0000-00-00' AND b.edate != '0000-00-00' AND b.resdate != '0000-00-00')";
    }
}

if($schText != ""){
    $schText = ' AND (a.user_name like "%'.$schText.'%" OR a.user_tel like "%'.$schText.'%")';
}

$select_query = "SELECT a.resseq FROM AT_SOL_RES_MAIN as a 
                    INNER JOIN AT_SOL_RES_SUB as b 
                        ON a.resseq = b.resseq 
                    WHERE 1=1 
                        $selDate
                        $schText
                    GROUP BY b.resseq";
                    
$select_query = "SELECT 
        a.resseq, a.resnum, a.admin_user, a.res_confirm, a.res_kakao, a.res_kakao_chk, a.res_room_chk, a.res_company, a.user_name, a.user_tel, a.memo, a.memo2, a.history, a.insdate, 
        b.ressubseq, b.res_type, b.prod_name, b.sdate, b.edate, '' as resdate, b.staysex, b.stayM, b.stayroom, b.staynum, b.restime, b.surfM, b.surfW, b.surfrent, b.surfrentM, b.surfrentW, b.surfrentYN,
        DAY(b.sdate) AS sDay, DAY(b.edate) AS eDay, DAY(b.resdate) AS resDay, MONTH(b.sdate) AS sMonth, MONTH(b.edate) AS eMonth, MONTH(b.resdate) AS resMonth, DATEDIFF(b.edate, b.sdate) as eDateDiff, a.userinfo, a.res_bankchk, c.response, c.KAKAO_DATE, 
            CASE WHEN staysex = '남' AND (party IN ('ALL', 'BBQ')) THEN 1
                ELSE 0 END AS BBQ_M,
            CASE WHEN staysex = '여' AND (party IN ('ALL', 'BBQ')) THEN 1
                ELSE 0 END AS BBQ_W,
            CASE WHEN staysex = '남' AND (party IN ('ALL', 'PUB')) THEN 1
                ELSE 0 END AS PUB_M,
            CASE WHEN staysex = '여' AND (party IN ('ALL', 'PUB')) THEN 1
                ELSE 0 END AS PUB_W
            FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                LEFT JOIN AT_KAKAO_HISTORY as c
                    ON a.userinfo = c.msgid
                WHERE b.res_type = 'stay'
                    AND a.res_confirm IN ($confirmText)
                    AND a.resseq IN ($select_query)
        UNION ALL
    SELECT 
        a.resseq, a.resnum, a.admin_user, a.res_confirm, a.res_kakao, a.res_kakao_chk, a.res_room_chk, a.res_company, a.user_name, a.user_tel, a.memo, a.memo2, a.history, a.insdate, 
        b.ressubseq, b.res_type, 
        CASE WHEN b.res_type = 'stay' THEN 'N' ELSE b.prod_name END as prod_name, 
        '' as sdate, '' as edate, b.resdate, b.staysex, b.stayM, null as stayroom, null as staynum, b.restime, b.surfM, b.surfW, b.surfrent, b.surfrentM, b.surfrentW, b.surfrentYN,
        DAY(b.sdate) AS sDay, DAY(b.edate) AS eDay, DAY(b.resdate) AS resDay, MONTH(b.sdate) AS sMonth, MONTH(b.edate) AS eMonth, MONTH(b.resdate) AS resMonth, DATEDIFF(b.edate, b.sdate) as eDateDiff, a.userinfo, a.res_bankchk, c.response, c.KAKAO_DATE,
        0 AS BBQ_M, 0 AS BBQ_W, 0 AS PUB_M, 0 AS PUB_W 
            FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                LEFT JOIN AT_KAKAO_HISTORY as c
                    ON a.userinfo = c.msgid
                WHERE a.res_confirm IN ($confirmText)
                    AND b.res_type = 'surf'
                    AND a.resseq IN ($select_query)
        ORDER BY resseq, ressubseq";
        
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count == 0){
?>
 <div class="contentimg bd">
    <div class="gg_first">예약 현황 (<span id="listdate"></span>)
    </div>
    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:5px;width:100%;" id="tbSolSearch">
        <tbody>
            <tr>
                <td style="text-align:center;height:50px;">
                    <b>예약된 목록이 없습니다. 달력에서 다른 날짜를 선택하세요.</b>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?
	return;
}

$css_table = "background-color:#336600; color:#efefef;";
$css_table_right = " border-right:2px solid #c0c0c0";
?>

<div class="contentimg bd">
<form name="frmConfirm" id="frmConfirm" autocomplete="off">
    <div class="gg_first">예약 현황 (<span id="listdate"></span>)
    </div>
    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;" id="tbSolSearch">
        <colgroup>
            <col width="44px" />
            <col width="110px" />
            <col width="60px" />
            <col width="100px" />
            <col width="35px" />
            <col width="35px" />
            <col width="35px" />
            <col width="35px" />
            <col width="35px" />
            <col width="35px" />
            <col width="85px" />
            <col width="45px" />
            <col width="35px" />
            <col width="35px" />
            <col width="75px" />
            <col width="35px" />
            <col width="35px" />
            <col width="42px" />
            <col width="42px" />
            <col width="42px" />
            <col width="42px" />
            <col width="62px" />
            <col width="82px" />
            <col width="auto" />
        </colgroup>
        <tbody>
            <tr>
                <th style="<?=$css_table?>" rowspan="2"></th>
                <th style="<?=$css_table?>" rowspan="2" colspan="2">예약자</th>
                <th style="<?=$css_table.$css_table_right?>" colspan="3">숙박정보</th>
                <th style="<?=$css_table.$css_table_right?>" colspan="2">바베큐</th>
                <th style="<?=$css_table.$css_table_right?>" colspan="2">2차</th>
                <th style="<?=$css_table?>" rowspan="2">서핑샵</th>
                <th style="<?=$css_table.$css_table_right?>" colspan="3">서핑강습</th>
                <th style="<?=$css_table?>" colspan="3">렌탈</th>
                <th style="<?=$css_table?>" rowspan="2">메모</th>
                <th style="<?=$css_table?>" rowspan="2">입실</th>
                <th style="<?=$css_table?>" rowspan="2">상태</th>
                <th style="<?=$css_table?>" colspan="3">알림톡</th>
                <th style="<?=$css_table?>" rowspan="2">예약업체</th>
            </tr>
            <tr>
                <th style="<?=$css_table?>">숙박일</th>
                <th style="<?=$css_table?>">남</th>
                <th style="<?=$css_table.$css_table_right?>">여</th>
                <th style="<?=$css_table?>">남</th>
                <th style="<?=$css_table.$css_table_right?>">여</th>
                <th style="<?=$css_table?>">남</th>
                <th style="<?=$css_table.$css_table_right?>">여</th>
                <th style="<?=$css_table?>">시간</th>
                <th style="<?=$css_table?>">남</th>
                <th style="<?=$css_table.$css_table_right?>">여</th>
                <th style="<?=$css_table?>">종류</th>
                <th style="<?=$css_table?>">남</th>
                <th style="<?=$css_table?>">여</th>
                <th style="<?=$css_table?>">읽음</th>
                <th style="<?=$css_table?>">결과</th>
                <th style="<?=$css_table?>">발송수</th>
            </tr>

<?
$i = 0;

$b = 1;
$c = 0;
$PreSeq = "";
$rowlist = '';
$TotalsurfM = 0;
$TotalsurfW = 0;
$TotalstayM = 0;
$TotalstayW = 0;
$partyBBQ_M = 0;
$partyBBQ_W = 0;
$partyPUB_M = 0;
$partyPUB_W = 0;
while ($row = mysqli_fetch_assoc($result_setlist)){
    $prod_name = str_replace("솔게스트하우스", "솔게하", $row['prod_name']);
    $sdate = $row['sdate'];
    $edate = $row['edate'];
    
    if($prod_name == "N" && $sdate == ""){
        //continue;
    }

    $MainSeq = $row['resseq'];
    if($MainSeq == $PreSeq){
		$b++;
	}else{
        if($c > 0){
            $rowlist .= $b."|";
        }
		$b = 1;
    }
    $c++;

    $PreSeq = $row['resseq'];

	$now = date("Y-m-d");

    $resseq = $row['resseq'];
    $admin_user = $row['admin_user'];
    $res_confirm = $row['res_confirm'];
	$res_kakao = $row['res_kakao'];
	$res_kakao_chk = $row['res_kakao_chk'];
	$res_room_chk = $row['res_room_chk'];
	$res_company = $row['res_company'];
	$user_name = $row['user_name'];
    $user_tel = $row['user_tel'];
    $memo = $row['memo'];
    $memo2 = $row['memo2'];
    $ressubseq = $row['ressubseq'];
    $res_type = $row['res_type'];
    $resdate = $row['resdate'];
    $staysex = $row['staysex'];
    $stayMem = $row['stayM'];
    $stayroom = $row['stayroom'];
    $staynum = $row['staynum'];
    $restime = $row['restime'];
    $surfM = $row['surfM'];
    $surfW = $row['surfW'];
    $surfrent = $row['surfrent'];
    $surfrentM = $row['surfrentM'];
    $surfrentW = $row['surfrentW'];
    $bbq = $row['bbq'];
    $eDay = $row['eDay'];
    $res_bankchk = $row['res_bankchk'];
    
    $response = $row['response'];
    $KAKAO_DATE = $row['KAKAO_DATE'];

    $memoYN = "";
    if($memo != "" || $memo2 != ""){
        $memoYN = "있음";
    }

    $stayText = "";
    $surfText = "";
    $stayInfo = "";
    $surfrentText = "";
    $stayMText = "";
    $stayWText = "";
    $BBQ_M = "";
    $BBQ_W = "";
    $PUB_M = "";
    $PUB_W = "";
    if($row['res_type'] == "stay"){ //숙박&바베큐
        if($prod_name == "N"){
            $res_room_chk = "";
        }else{
            $stayText = str_replace("-", ".", substr($sdate, 5, 10))." ~ ".str_replace("-", ".", substr($edate, 5, 10));

            if($res_confirm == "확정" || $res_confirm == "대기"){
                $stayInfo = "stayinfo2='$user_name|$user_name|$prod_name|$staysex|$stayroom|$staynum|".$row['eDateDiff']."|$eDay|$resseq|$res_confirm'";
            }

            if($staysex == "남"){
                $stayMText = $stayMem.(($stayMem == "")? "" : "명");
                $TotalstayM += $stayMem;
            }else{
                $stayWText = $stayMem.(($stayMem == "")? "" : "명");
                $TotalstayW += $stayMem;
            }
        }

        $BBQ_M = ($row['BBQ_M'] == 0) ? "" : $row['BBQ_M'].'명';
        $BBQ_W = ($row['BBQ_W'] == 0) ? "" : $row['BBQ_W'].'명';
        $PUB_M = ($row['PUB_M'] == 0) ? "" : $row['PUB_M'].'명';
        $PUB_W = ($row['PUB_W'] == 0) ? "" : $row['PUB_W'].'명';

        $partyBBQ_M += $BBQ_M;
        $partyBBQ_W += $BBQ_W;
        $partyPUB_M += $PUB_M;
        $partyPUB_W += $PUB_W;
    }else{ //강습&렌탈
        $res_room_chk = "";
        if($prod_name != "N"){
            $surfText = str_replace("솔게스트하우스", "솔.동해점", $row['prod_name']);

            $TotalsurfM += $surfM;
            $TotalsurfW += $surfW;
        }

        if($surfrent != "N"){            
            $surfrentText = $surfrent;
        }
    }

    $fontcolor = "";
    $bankchk = "";
    if($res_confirm == "대기"){
        if($res_bankchk == "N"){
            $bankchk = "미발송";
        }else if($res_bankchk == "0"){
            $bankchk = "일반계좌";
        }else{
            $bankchk = number_format($res_bankchk)."원";            
        }
        $bankchk = "<br><span style='color:black;'><b>".$bankchk."</b></span>";
        $fontcolor = "color:#c0c0c0;";
    }else if($res_confirm == "취소"){
    }else{
        if($row['res_type'] == "stay" && $prod_name != "N" && $prod_name != "솔게하"){
            $fontcolor = "color:#8080ff;";
        }
    }

    $rtnText = "";
    $rtnTextCode = "0회";
    if($res_kakao == "0"){
        $rtnText = '<span class="btn_view" seq="40'.$c.'">X</span><span style="display:none;"><b><a href="/sol_kakao?chk=1&seq='.$resseq.'" target="_blank">[알림톡 보기]<a></b></span>';
    }else{
        if($response == ""){
        }else{
            $data = json_decode($response, true);

            $code = $data["code"];
            $msgid = $data["data"]["msgid"];
            $type = $data["data"]["type"];
            $message = $data["message"];
            $originMessage = $data["originMessage"];

            
            $rtnText_0 = "<b>".(($code == "fail") ? "실패" : "성공")."</b>";
            $rtnText_1 = "(".(($type == "AT") ? "알림톡" : "문자").")";  
            $rtnMessage = "<b>$rtnText_0 $rtnText_1</b>";
            
            $rtnTextCode = $res_kakao.'회';
            
            $rtnText = "<span class='btn_view' seq='40$c'>$rtnText_0<br>$rtnText_1</span><span style='display:none;'><b>$KAKAO_DATE <a href='/sol_kakao?chk=1&seq=$resseq' target='_blank'>[알림톡 보기]<a></b><br><br>$rtnMessage</span>";
        }
    }
?>
    <tr>
        <td style="<?=$fontcolor?>"><label><?=$resseq?></label></td>
        <td style="cursor:pointer;<?=$fontcolor?>" onclick="fnSolModify(<?=$resseq?>, '_2');"><b><?=$user_name?><br><?=$user_tel?></b></td>
        <td style="<?=$fontcolor?>">
            <input type="button" class="gg_btn res_btn_color2" style="width:40px; height:22px;" value="수정" onclick="fnSolModify(<?=$resseq?>);" />
        </td>
        <td style="<?=$fontcolor?>" <?=$stayInfo?>><?=$stayText?></td>
        <td style="<?=$fontcolor?>"><?=$stayMText?></td>
        <td style="<?=$fontcolor.$css_table_right?>"><?=$stayWText?></td>
        <td style="<?=$fontcolor?>"><?=$BBQ_M?></td>
        <td style="<?=$fontcolor.$css_table_right?>"><?=$BBQ_W?></td>
        <td style="<?=$fontcolor?>"><?=$PUB_M?></td>
        <td style="<?=$fontcolor.$css_table_right?>"><?=$PUB_W?></td>
        <td style="<?=$fontcolor?>"><?=$surfText?></td>
        <td style="<?=$fontcolor?>"><?=($restime == 0) ? "" : $restime?></td>
        <td style="<?=$fontcolor?>"><?=($surfM == 0) ? "" : $surfM."명"?></td>
        <td style="<?=$fontcolor.$css_table_right?>"><?=($surfW == 0) ? "" : $surfW."명"?></td>
        <td style="<?=$fontcolor?>"><?=$surfrentText?></td>
        <td style="<?=$fontcolor?>"><?=($surfrentM == 0) ? "" : $surfrentM."명"?></td>
        <td style="<?=$fontcolor?>"><?=($surfrentW == 0) ? "" : $surfrentW."명"?></td>
        <td style="<?=$fontcolor?>"><span class="btn_view" seq="10<?=$c?>"><?=$memoYN?></span><span style='display:none;'><b>요청사항</b><br><?=$memo?><br><br><b>직원메모</b><br><?=$memo2?></td>
        <td style="<?=$fontcolor?>"><?if($res_room_chk == "Y"){echo "입실";}?></td>
        <td style="<?=$fontcolor?>"><?=$res_confirm?></td>
        <td style="<?=$fontcolor?>"><?if($res_kakao_chk == "Y"){echo "읽음";}else{echo "X";}?></td>
        <td style="<?=$fontcolor?>"><?=$rtnText?></td>
        <td style="<?=$fontcolor?>"><?=$rtnTextCode?>
            <?=$bankchk?>
        </td>
        <td style="<?=$fontcolor?>"><?=$res_company?></td>
    </tr>
<?
//while end
}
$rowlist .= $b."|";
?>
		</tbody>
    </table>
    <input type="hidden" id="hidrowcnt2" value="<?=$rowlist?>" />
</form>