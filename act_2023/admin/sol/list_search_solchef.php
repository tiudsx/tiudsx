<?php
include __DIR__.'/../../common/func.php';
include __DIR__.'/../../common/kakaoalim.php';

include __DIR__.'/../../common/db.php';

$select_query = "SELECT A.*, REPLACE(B.name, '서핑버스 ', '') AS name FROM `AT_COUPON_CODE` AS A 
                    INNER JOIN AT_COUPON AS B 
                        ON A.couponseq = B.couponseq                        
                    WHERE A.couponseq IN (7,10,11,12,14,15,16,17,20,21,22) AND A.use_yn = 'N'
                    ORDER BY A.codeseq DESC";
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count == 0){
?>
<table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:5px;width:100%;">
    <tbody>
        <tr>
            <th>이름</th>
            <th>연락처</th>
            <th>입금금액</th>
            <th>예약여부</th>
            <th>결과코드</th>
        </tr>
        <tr>
            <td colspan="5" style="text-align:center;height:50px;">
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
    <tbody>
        <tr>
            <th>이름</th>
            <th>연락처</th>
            <th>입금금액</th>
            <th>예약여부</th>
            <th>결과코드</th>
        </tr>
<?while ($row = mysqli_fetch_assoc($result_setlist)){
	$coupon_code = $row['coupon_code'];
	$userinfo = $row['userinfo'];

    $arrChk = explode("|", $userinfo);
    //홍길동|0104437123|2022-01-08|2|2022-01-09|2|fail|L|M107:DeniedSenderNumber|K102:InvalidPhoneNumber
    //이승철|01044370009|2023-05-07|1|2023-05-07|1|success|AT|K000||WEB20230417134422004880

    $rtnText = "<b>".(($arrChk[6] == "fail") ? "실패" : "성공")."</b> (".(($arrChk[7] == "AT") ? "알림톡" : "문자").")<br>";
    $rtnMessage = "<b>".$rtnText."</b>";
    $rtnTextCode = "";
   
    if($arrChk[8] != ""){
        $mesCode = substr($arrChk[8], 0, 4);
        //$rtnTextCode .= $mesCode;
        //$rtnText .= "_".$arrChk[8];

        if($mesCode == "M001"){
            $data = json_decode(getKakaoSearch($arrChk[10]), true);
            $rtnMessage .= "<br>&nbsp;&nbsp;&nbsp; - ".substr($data["message"], 0, 4)." : ".fnMessageText(substr($data["message"], 0, 4));
        }else{
            $rtnMessage .= "<br>&nbsp;&nbsp;&nbsp; - ".$mesCode." : ".fnMessageText($mesCode);
        }
    }
    
    if($arrChk[9] != ""){
        $mesOriCode = substr($arrChk[9], 0, 4);
        //$rtnTextCode .= " / ".$mesOriCode;
        //$rtnText .= "_".$arrChk[9];

        $rtnMessage .= "<br>&nbsp;&nbsp;&nbsp; - ".$mesOriCode." : ".fnMessageText($mesOriCode);
    }

    $rtnTextCode = '<span class="kakao_view" seq="'.$row['codeseq'].'">'.$rtnText.$rtnTextCode.'</span><span style="display:none;"><b>'.$row['insdate'].'</b><br><br>'.$rtnMessage.'</span>';

    ?>
        <tr>
            <td><?=$arrChk[0]?></td>
            <td><?=$arrChk[1]?></td>
            <td></td>
            <td>
                <!-- <input type="button" class="gg_btn res_btn_color2" style="width:40px; height:25px;" value="삭제" onclick="fnSolChefDel(<?=$row['codeseq']?>);" /> -->
            </td>
            <td>
                <?=$rtnTextCode?>                
            </td>
        </tr>
<?}?>
    </tbody>
</table>

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