<?
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/kakaoalim.php';

$reqDate = $_REQUEST["selDate"];
$gubun = $_REQUEST["gubun"];
if($reqDate == ""){
    $selDate = date("Y-m-d");
}else{
    $selDate = $reqDate;
}
$arrDate = explode('-', $selDate);

$Year = $arrDate[0];
$Mon = $arrDate[1];
$Day = $arrDate[2];

if($gubun == "cancel"){
    $confirmText = "'취소'";
    $tabColor1 = "";
    $tabColor2 = "gg_btn_color";
}else{
    $confirmText = "'대기', '확정'";
    $tabColor1 = "gg_btn_color";
    $tabColor2 = "";
}

$select_query = "
    SELECT 
        a.resseq, a.resnum, a.admin_user, a.res_confirm, a.res_kakao, a.res_kakao_chk, a.res_room_chk, a.res_company, a.user_name, a.user_tel, a.memo, a.memo2, a.history, a.insdate, 
        b.ressubseq, b.res_type, b.prod_name, b.sdate, b.edate, '' as resdate, b.staysex, b.stayM, b.stayroom, b.staynum, b.restime, b.surfM, b.surfW, b.surfrent, b.surfrentM, b.surfrentW, b.surfrentYN,
        DAY(b.sdate) AS sDay, DAY(b.edate) AS eDay, DAY(b.resdate) AS resDay, MONTH(b.sdate) AS sMonth, MONTH(b.edate) AS eMonth, MONTH(b.resdate) AS resMonth, DATEDIFF(b.edate, b.sdate) as eDateDiff, a.userinfo, a.res_bankchk, c.response, c.KAKAO_DATE 
            FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                LEFT JOIN AT_KAKAO_HISTORY as c
                    ON a.userinfo = c.msgid
                WHERE ((b.sdate <= '$selDate' AND DATE_ADD(b.edate, INTERVAL -1 DAY) >= '$selDate')
                        OR b.resdate = '$selDate')
                    AND res_type = 'stay'
                    AND a.res_confirm IN ($confirmText)
        UNION ALL
    SELECT 
        a.resseq, a.resnum, a.admin_user, a.res_confirm, a.res_kakao, a.res_kakao_chk, a.res_room_chk, a.res_company, a.user_name, a.user_tel, a.memo, a.memo2, a.history, a.insdate, 
        b.ressubseq, b.res_type, 
        CASE WHEN b.res_type = 'stay' THEN 'N' ELSE b.prod_name END as prod_name, 
        '' as sdate, '' as edate, b.resdate, b.staysex, b.stayM, null as stayroom, null as staynum, b.restime, b.surfM, b.surfW, b.surfrent, b.surfrentM, b.surfrentW, b.surfrentYN,
        DAY(b.sdate) AS sDay, DAY(b.edate) AS eDay, DAY(b.resdate) AS resDay, MONTH(b.sdate) AS sMonth, MONTH(b.edate) AS eMonth, MONTH(b.resdate) AS resMonth, DATEDIFF(b.edate, b.sdate) as eDateDiff, a.userinfo, a.res_bankchk, c.response, c.KAKAO_DATE 
            FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                LEFT JOIN AT_KAKAO_HISTORY as c
                    ON a.userinfo = c.msgid
                WHERE b.resdate = '$selDate'                    
                    AND a.res_confirm IN ($confirmText)
                    AND res_type = 'surf'
        ORDER BY resseq, ressubseq";
                   
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count == 0){
?>
 <div class="contentimg bd">
    <div class="gg_first">예약 현황 (<span id="listdate"><?=$selDate?></span>)
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large <?=$tabColor1?>" style="width:80px; height:20px;" value="전체" onclick="fnListTab('all', this);" />
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large " style="width:80px; height:20px;" value="파티인원" onclick="fnListTab('stay', this);" />
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large " style="width:80px; height:20px;" value="강습&렌탈" onclick="fnListTab('surf', this);" />
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large <?=$tabColor2?>" style="width:80px; height:20px;" value="취소건" onclick="fnListTab('cancel', this);" />
    </div>
    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:5px;width:100%;" id="tbSolList">
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
    <div class="gg_first">예약 현황 (<span id="listdate"><?=$selDate?></span>)
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large <?=$tabColor1?>" style="width:80px; height:20px;" value="전체" onclick="fnListTab('all', this);" />
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large " style="width:80px; height:20px;" value="파티인원" onclick="fnListTab('stay', this);" />
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large " style="width:80px; height:20px;" value="강습&렌탈" onclick="fnListTab('surf', this);" />
    <?if($gubun != "cancel"){?>
        <input type="button" class="gg_btn res_btn_color2" style="width:120px; height:22px;" value="카톡 선택발송" onclick="fnKakaoSend(null, true);" />
    <?}?>

        <input type="button" name="listtab" class="gg_btn gg_btn_grid large <?=$tabColor2?>" style="width:80px; height:20px;" value="취소건" onclick="fnListTab('cancel', this);" />
    </div>
    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;" id="tbSolList">
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
                <th style="<?=$css_table?>" rowspan="2"><label><input type="checkbox" onclick="fnAllChk(this);"></label></th>
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
$TotalbbqM = 0;
$TotalbbqW = 0;
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
    $bbqText = "";
    $surfrentText = "";
    $stayMText = "";
    $stayWText = "";
    $bbqMText = "";
    $bbqWText = "";
    if($row['res_type'] == "stay"){ //숙박&바베큐
        if($prod_name == "N"){
            $res_room_chk = "";
        }else{
            if($row['sMonth'] == $Mon || $row['eMonth'] == $Mon){
                if(!((int)$Day == $eDay)){
                    $stayText = str_replace("-", ".", substr($sdate, 5, 10))." ~ ".str_replace("-", ".", substr($edate, 5, 10));

                    if($res_confirm == "확정" || $res_confirm == "대기"){
                        $stayInfo = "stayinfo='$user_name|$user_name|$prod_name|$staysex|$stayroom|$staynum|".$row['eDateDiff']."|$eDay|$resseq|$res_confirm'";
                    }

                    if($staysex == "남"){
                        $stayMText = $stayMem.(($stayMem == "")? "" : "명");
                        $TotalstayM += $stayMem;
                    }else{
                        $stayWText = $stayMem.(($stayMem == "")? "" : "명");
                        $TotalstayW += $stayMem;
                    }
                }
            }
        }

        if($Day == $row['resDay']){
            $bbqText = $bbq;
            
            if($staysex == "남"){
                $bbqMText = $stayMem.(($stayMem == "")? "" : "명");
                $TotalbbqM += $stayMem;
            }else{
                $bbqWText = $stayMem.(($stayMem == "")? "" : "명");
                $TotalbbqW += $stayMem;
            }
        }
    }else{ //강습&렌탈
        $res_room_chk = "";
        if($Day == $row['resDay']){
            if($prod_name != "N"){
                $surfText = str_replace("솔게스트하우스", "솔.동해점", $row['prod_name']);

                $TotalsurfM += $surfM;
                $TotalsurfW += $surfW;
            }

            if($surfrent != "N"){            
                $surfrentText = $surfrent;
            }
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
        //$fontcolor = "color:#c0c0c0;";
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
        
            $rtnMessage .= " : <a href='https://alimtalk-api.bizmsg.kr/codeList.html' target=_blank>오류코드 목록</a>";
            if($message != ""){
                $mesCode = substr($message, 0, 4);
                $rtnTextCode .= $mesCode;

                if($mesCode == "M001"){
                    $kakao_data = getKakaoSearch($msgid);
                    $kakao_json = json_decode($kakao_data, true);
                    $rtnMessage .= "<br>&nbsp;&nbsp;&nbsp; - ".$kakao_json["message"]." : ".fnMessageText($kakao_json["message"]);

                    if($kakao_json["message"] != $mesCode){
                        $select_query = "UPDATE AT_KAKAO_HISTORY 
                                            SET response = '".$kakao_data."'
                                                ,code = '".$kakao_json["code"]."'
                                                ,message = '".$kakao_json["message"]."'
                                                ,originMessage = '".$kakao_json["originMessage"]."'
                                        WHERE msgid = ".$msgid;
                        $result_set = mysqli_query($conn, $select_query);
                    }
                }else{
                    $rtnMessage .= "<br>&nbsp;&nbsp;&nbsp; - ".$mesCode." : ".fnMessageText($mesCode);
                }
            }
            
            if($originMessage != ""){
                $mesOriCode = substr($originMessage, 0, 4);
                $rtnTextCode .= " / ".$mesOriCode;

                $rtnMessage .= "<br>&nbsp;&nbsp;&nbsp; - ".$mesOriCode." : ".fnMessageText($mesOriCode);
            }

            $rtnTextCode = $res_kakao.'회';
            
            $rtnText = "<span class='btn_view' seq='40$c'>$rtnText_0<br>$rtnText_1</span><span style='display:none;'><b>$KAKAO_DATE <a href='/sol_kakao?chk=1&seq=$resseq' target='_blank'>[알림톡 보기]<a></b><br><br>$rtnMessage</span>";
        }
    }
?>
    <tr>
        <td style="<?=$fontcolor?>">
        <label>
        <?if($res_confirm == "확정"){?>
            <input type="checkbox" id="chkresseq" name="chkresseq[]" value="<?=$resseq?>">
        <?}else{?>
            <input type="checkbox" id="chkresseq" name="chkresseq[]" disabled>
        <?}?>
            <br><?=$resseq?></label>
        </td>
        <td style="cursor:pointer;<?=$fontcolor?>" onclick="fnSolModify(<?=$resseq?>, '_2');"><b><?=$user_name?><br><?=$user_tel?></b></td>
        <td style="<?=$fontcolor?>">
            <input type="button" class="gg_btn res_btn_color2" style="width:40px; height:22px;" value="수정" onclick="fnSolModify(<?=$resseq?>);" />
        </td>
        <td style="<?=$fontcolor?>" <?=$stayInfo?>><?=$stayText?></td>
        <td style="<?=$fontcolor?>"><?=$stayMText?></td>
        <td style="<?=$fontcolor.$css_table_right?>"><?=$stayWText?></td>
        <!-- <td style="<?=$fontcolor?>"><?=$bbqText?></td> -->
        <td style="<?=$fontcolor?>"><?=$bbqMText?></td>
        <td style="<?=$fontcolor.$css_table_right?>"><?=$bbqWText?></td>
        <td style="<?=$fontcolor?>"><?=$bbqMText?></td>
        <td style="<?=$fontcolor.$css_table_right?>"><?=$bbqWText?></td>
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
            <?if($res_confirm == "확정"){?>
            <input type="button" class="gg_btn res_btn_color2" style="width:40px; height:22px;" value="발송" onclick="fnKakaoSend(<?=$resseq?>, false);" />
            <?}?>
            <?=$bankchk?>
        </td>
        <td style="<?=$fontcolor?>"><?=$res_company?></td>
    </tr>
<?
//while end
}
$rowlist .= $b."|";
?>
            <tr style="background-color:#ffa87d;">
                <td colspan="3"><strong>합 계</strong></td>
                <td colspan="3">
                    <strong>숙박</strong>&nbsp;&nbsp;&nbsp;남 : <?=($TotalstayM == 0) ? "" : $TotalstayM."명"?> / 여 : <?=($TotalstayW == 0) ? "" : $TotalstayW."명"?>
                    <br><strong>총 : <?=$TotalstayM+$TotalstayW."명"?></strong>
                </td>
                <td colspan="3">
                    <strong>바베큐</strong>&nbsp;&nbsp;&nbsp;남 : <?=($TotalbbqM == 0) ? "" : $TotalbbqM."명"?> / 여 : <?=($TotalbbqW == 0) ? "" : $TotalbbqW."명"?>
                    <br><strong>총 : <?=$TotalbbqM+$TotalbbqW."명"?></strong>
                </td>
                <td colspan="6">
                    <strong>서핑강습</strong>&nbsp;&nbsp;&nbsp;남 : <?=($TotalsurfM == 0) ? "" : $TotalsurfM."명"?> / 여 : <?=($TotalsurfW == 0) ? "" : $TotalsurfW."명"?>
                    <br><strong>총 : <?=$TotalsurfM+$TotalsurfW."명"?></strong>
                </td>
                <td colspan="9"></td>
            </tr>
		</tbody>
    </table>
    <input type="hidden" id="hidrowcnt" value="<?=$rowlist?>" />
    <input type="hidden" id="resparam" name="resparam" value="solkakaoAll" />
    <input type="hidden" id="selDate" name="selDate" value="<?=$selDate?>" />
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

    $j("calbox[value=" + $j("#selDate").val() + "]").css("background", "#efefef");
    $j("calbox[value=" + $j("#selDate").val() + "]").attr("sel", "yes");
}); 
</script>