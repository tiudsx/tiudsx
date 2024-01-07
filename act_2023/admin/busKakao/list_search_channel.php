<?php
include __DIR__.'/../../common/func.php';
include __DIR__.'/../../common/kakaoalim.php';

$hidsearch = $_REQUEST["hidsearch"];

include __DIR__.'/../../common/db.php';

$select_query = "SELECT A.*, B.etc, C.name, D.msgid, D.message, D.response, D.code, D.originMessage, D.seq 
                    FROM AT_RES_TEMP AS A INNER JOIN AT_COUPON_CODE AS B
                            ON A.codeseq = B.codeseq
                        INNER JOIN AT_COUPON AS C
                            ON B.couponseq = C.couponseq
                        INNER JOIN AT_KAKAO_HISTORY AS D
                        	ON A.resnum = D.resnum
                    WHERE A.res_chk = 'N'
                    ORDER BY tempseq ASC";
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count == 0){
?>
<table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:5px;width:100%;">
    <colgroup>
        <col width="16%"/>
        <col width="10%"/>
        <col width="14%"/>
        <col width="15%"/>
        <col width="15%"/>
        <col width="auto"/>
        <col width="12%"/>
    </colgroup>
    <tbody>
        <tr>
            <th>노선/채널</th>
            <th>이름</th>
            <th>연락처</th>
            <th>이용일 (서울 출발)</th>
            <th>이용일 (서울 복귀)</th>
            <th>예약여부</th>
            <th>결과코드</th>
        </tr>
        <tr>
            <td colspan="8" style="text-align:center;height:50px;">
                <b>조회된 데이터가 없습니다.</b>
            </td>
        </tr>
    </tbody>
</table>

<?
	return;
}
?>
<table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:5px;width:100%;">
    <colgroup>
        <col width="80"/>
        <col width="auto"/>
        <col width="100"/>
        <col width="120"/>
        <col width="90"/>
        <col width="140"/>
        <col width="90"/>
        <col width="140"/>
        <col width="70"/>
        <col width="80"/>
        <col width="70"/>
        <col width="100"/>
    </colgroup>
    <tbody>
        <tr>
            <th>노선</th>
            <th>채널</th>
            <th>이름</th>
            <th>연락처</th>
            <th colspan="2">이용일 (서울 출발)</th>
            <th colspan="2">이용일 (서울 복귀)</th>
            <th>발송횟수</th>
            <th colspan="2">재발송</th>
            <th>결과코드</th>
        </tr>
<?while ($row = mysqli_fetch_assoc($result_setlist)){    
    $json = json_decode($row['response'], true);
    $data = $json["data"];
    
    $rtnText = "<b>".(($json["code"] == "fail") ? "실패" : "성공")."</b> (".(($data["type"] == "AT") ? "알림톡" : "문자").")<br>";
    $rtnMessage = "<b>".$rtnText."</b>";

    if($json["message"] == "M001"){
        $kakao_data = getKakaoSearch($data["msgid"]);
        $kakao_json = json_decode($kakao_data, true);
        $rtnMessage .= "<br>&nbsp;&nbsp;&nbsp; - ".$kakao_json["message"]." : ".fnMessageText($kakao_json["message"]);

        if($kakao_json["message"] != $json["message"]){
            $select_query = "UPDATE AT_KAKAO_HISTORY 
                                SET response = '".$kakao_data."'
                                    ,code = '".$kakao_json["code"]."'
                                    ,message = '".$kakao_json["message"]."'
                                    ,originMessage = '".$kakao_json["originMessage"]."'
                            WHERE seq = ".$row['seq'];
            $result_set = mysqli_query($conn, $select_query);
        }
    }else{
        $rtnMessage .= "<br>&nbsp;&nbsp;&nbsp; - ".substr($json["message"], 0, 4)." : ".fnMessageText(substr($json["message"], 0, 4));
    }
    
    if($json["originMessage"] != null){
        $mesOriCode = substr($json["originMessage"], 0, 4);

        $rtnMessage .= "<br>&nbsp;&nbsp;&nbsp; - ".$mesOriCode." : ".fnMessageText($mesOriCode);
    }

    $rtnTextCode = '<span class="kakao_view" seq="'.$row['codeseq'].'">'.$rtnText.'</span><span style="display:none;"><b>'.$row['insdate'].'</b> : <a href="https://alimtalk-api.bizmsg.kr/codeList.html" target=_blank>오류코드 목록</a><br>'.$rtnMessage.'</span>';

    ?>
        <tr>
            <td><a href="<?=$row['etc']?>" target="_blank"><b>[<?=(($row['bus_line'] == "YY") ? "양양" : "동해")?>]</b></a></td>
            <td><a href="<?=$row['etc']?>" target="_blank"><?=$row['name']?></a></td>
            <td><?=$row['user_name']?></td>
            <td><?=$row['user_phone']?></td>
            <td><b><?=(($row['start_bus_gubun'] == "SA") ? "사당선" : "종로선")?></b></td>
            <td>
                <?
                if($row['start_cnt'] > 0){
                    echo $row['start_day']." <b>(".$row['start_cnt']."명)</b>";
                }else{
                    echo "X";
                }
                ?>    
            </td>
            <td><b><?=(($row['return_bus_gubun'] == "AM") ? "오후" : "저녁")?></b></td>
            <td>
                <?
                if($row['return_cnt'] > 0){
                    echo $row['return_day']." <b>(".$row['return_cnt']."명)</b>";
                }else{
                    echo "X";
                }
                ?>
            </td><td>
                <?=$row['kakao_cnt']?> 회              
            </td>
            <td>
                <?if($json["code"] != "fail"){?>
                <input type="button" class="gg_btn res_btn_color1" style="width:50px; height:25px;" value="재발송" onclick="fnBusChannelKakao('<?=$row['resnum']?>');" /> &nbsp; 
                <?}?>
            </td>
            <td>
                <input type="button" class="gg_btn res_btn_color2" style="width:40px; height:25px;" value="삭제" onclick="fnBusChannelDel(<?=$row['codeseq']?>);" /></td>
            <td>
                <?=$rtnTextCode?>                
            </td>
        </tr>
<?}?>
    </tbody>
</table>

<?
/*
    $url = 'https://alimtalk-api.bizmsg.kr/v1/user/balance?userid=surfenjoy&userkey=RLORgrwUB4UI1Vt2ntl3';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    echo "결과:".$response;
    */
?>

<script type="text/javascript">
$j(document).ready(function(){
	$j(".kakao_view[seq]").mouseover(function(e){ //조회 버튼 마우스 오버시
		var seq = $j(this).attr("seq");
		var obj = $j(".kakao_view[seq="+seq+"]");
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
				$j(".kakao_view[seq="+seq+"]").append('<div class="box_layer"></div>');
				$j(".kakao_view[seq="+seq+"]").find(".box_layer").html($j(".kakao_view[seq="+seq+"]").next().html());
				$j(".kakao_view[seq="+seq+"]").find(".box_layer").css({
					"top" : tY
					,"left" : tX
					,"position" : "absolute"
				}).show();
		}		
	});
	
	$j(".kakao_view[seq]").mouseout(function(e){
			$j(this).find(".box_layer").css("display","none");
	});				 
}); 
</script>