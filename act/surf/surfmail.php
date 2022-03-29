<?php

/*셔틀버스 메일 발송 */
function sendMailContentBus($arrMail){
	$addText1 = " 참석";
	$addText2 = "";
	if($arrMail["campStayName"] == "busCancel1"){
		$addText1 = " 취소";
		$addText2 = "취소 ";
	}

	$contents = "<table width='720' align='center' border='0' cellspacing='0' cellpadding='0'>
    <tbody>
        <tr>
            <td height='58' valign='bottom' style='padding-bottom: 2px;'>
                <a href='https://actrip.co.kr/' target='_blank'><img alt='' src='https://surfenjoy.cdn3.cafe24.com/logo/actrip.png' border='0'></a>
            </td>
        </tr>
		<tr>
			<td style='height: 3px; line-height: 0; font-size: 0px; background-color: rgb(83, 83, 83);'>&nbsp;</td>
		</tr>
        <tr>
            <td><br>
                <span style='color: rgb(51, 51, 51); font-family:굴림체; font-size: 30px; font-weight: bold;'>액트립 예약안내 메일입니다.</span><br>
				<p style='color: rgb(51, 51, 51); font-family: ; font-size: 16px; font-weight: bold; margin-bottom: 11px;'>안녕하세요. <strong>".$arrMail['userName']."</strong> 고객님.</p>
				".$arrMail['gubun']."를 예약해 주셔서 진심으로 감사드립니다.				
            </td>
        </tr>
        <tr>
            <td height='35'></td>
        </tr>
        <tr>
            <td style=\"background: url('http://www.smartix.co.kr/joinmail_img/bg.gif') no-repeat right bottom; padding-bottom: 10px;\">
                <img src='http://www.smartix.co.kr/joinmail_img/05.png'>
                <b>예약정보</b></td>
        </tr>
        <tr>
            <td style='padding-top: 7px;'>
                <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                    <tbody>
                        <tr>
                            <td width='150' height='27' style='padding-left: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>- 예&nbsp;약&nbsp;자 </td>
                            <td width='1' style='border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>
                                <img src='http://www.smartix.co.kr/joinmail_img/tap.gif'></td>
                            <td style='padding-left: 15px; font-weight: bold; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>".$arrMail['userName']." (".$arrMail['userPhone'].")</td>
                        </tr>
                        <tr>
                            <td width='150' height='27' style='padding-left: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>- 예약번호</td>
                            <td width='1' style='border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>
                                <img src='http://www.smartix.co.kr/joinmail_img/tap.gif'></td>
                            <td style='color: rgb(222, 119, 118); padding-left: 15px; font-weight: bold; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>".$arrMail['ResNumber']." <a href='https://actrip.co.kr/orderview?num=1&resNumber=".$arrMail['ResNumber']."' target='_blank'>[예약조회]</a>
                            </td>
                        </tr>
                        <tr>
                            <td width='150' height='27' style='padding-left: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>- ".$addText2."좌석안내</td>
                            <td width='1' style='border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>
                                <img src='http://www.smartix.co.kr/joinmail_img/tap.gif'></td>
                            <td style='padding-left: 15px;padding-top: 8px;padding-bottom: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>".$arrMail['busSeatInfo']."</td>
                        </tr>";
		if($arrMail["campStayName"] != "busCancel1"){
			$contents .= "<tr>
                            <td width='150' height='27' style='padding-left: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>- 탑승시간/위치 안내</td>
                            <td width='1' style='border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>
                                <img src='http://www.smartix.co.kr/joinmail_img/tap.gif'></td>
                            <td style='padding-left: 15px;padding-top: 8px;padding-bottom: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>".$arrMail['busStopInfo']."</td>
                        </tr>";
		}

		if($arrMail['SurfBBQMem'] > 0){
			$contents .= "<tr>
                            <td width='150' height='27' style='padding-left: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>- 바베큐 ".$addText1."</td>
                            <td width='1' style='border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>
                                <img src='http://www.smartix.co.kr/joinmail_img/tap.gif'></td>
                            <td style='padding-left: 15px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>[".$arrMail['SurfBBQ']."] ".$arrMail['SurfBBQMem']."명</td>
                        </tr>";
		}
           $contents .= "<tr>
                            <td width='150' height='27' style='padding-left: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>- 요청사항</td>
                            <td width='1' style='border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>
                                <img src='http://www.smartix.co.kr/joinmail_img/tap.gif'></td>
                            <td style='padding-left: 15px; padding-top:5px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'><textarea name='etc' id='etc' style='margin: 0px; width: 97%; height: 80px; resize: none;' rows='8' cols='42' disabled='disabled'>".$arrMail['etc']."</textarea></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>";

if($arrMail["campStayName"] == "busStay1"){
$contents .= "<tr>
            <td height='35'></td>
        </tr>
        <tr>
            <td style=\"background: url('http://www.smartix.co.kr/joinmail_img/bg.gif') no-repeat right bottom; padding-bottom: 10px;\">
                <img src='http://www.smartix.co.kr/joinmail_img/05.png'>
                <b>결제정보</b></td>
        </tr>
        <tr>
            <td style='padding-top: 7px;'>
                <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                    <tbody>
                        <tr>
                            <td width='150' height='27' style='padding-left: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>- 입금계좌</td>
                            <td width='1' style='border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>
                                <img src='http://www.smartix.co.kr/joinmail_img/tap.gif'></td>
                            <td style='padding-left: 15px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'><font color='#8000ff'><b>".$arrMail['banknum']."</b></font>
                            </td>
                        </tr>
                        <tr>
                            <td width='150' height='27' style='padding-left: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>- 입금금액</td>
                            <td width='1' style='border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>
                                <img src='http://www.smartix.co.kr/joinmail_img/tap.gif'></td>
                            <td style='color: rgb(222, 119, 118); padding-left: 15px; font-weight: bold; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>".$arrMail['totalPrice']."</td>
                        </tr>";
           	$contents .= "<tr>
                            <td width='150' height='27' style='padding-left: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>- 플러스친구</td>
                            <td width='1' style='border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>
                                <img src='http://www.smartix.co.kr/joinmail_img/tap.gif'></td>
                            <td style='padding-left: 15px; font-weight: bold; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;' height='45'><a href='http://pf.kakao.com/_HxmtMxl' target='_blank'>http://pf.kakao.com/_HxmtMxl</a></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>";
}else if($arrMail["campStayName"] == "busCancel1"){
$contents .= "<tr>
            <td height='35'></td>
        </tr>
        <tr>
            <td style=\"background: url('http://www.smartix.co.kr/joinmail_img/bg.gif') no-repeat right bottom; padding-bottom: 10px;\">
                <img src='http://www.smartix.co.kr/joinmail_img/05.png'>
                <b>환불정보</b></td>
        </tr>
        <tr>
            <td style='padding-top: 7px;'>
                <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                    <tbody>
                        <tr>
                            <td width='150' height='27' style='padding-left: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>- 환불금액</td>
                            <td width='1' style='border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>
                                <img src='http://www.smartix.co.kr/joinmail_img/tap.gif'></td>
                            <td style='padding-left: 15px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>".$arrMail['totalPrice']."
                            </td>
                        </tr>
                        <tr>
                            <td width='150' height='27' style='padding-left: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>- 환불계좌</td>
                            <td width='1' style='border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>
                                <img src='http://www.smartix.co.kr/joinmail_img/tap.gif'></td>
                            <td style='padding-left: 15px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>".$arrMail['banknum']."
                            </td>
                        </tr>";
           	$contents .= "<tr>
                            <td width='150' height='27' style='padding-left: 8px; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>- 플러스친구</td>
                            <td width='1' style='border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;'>
                                <img src='http://www.smartix.co.kr/joinmail_img/tap.gif'></td>
                            <td style='padding-left: 15px; font-weight: bold; border-bottom-color: rgb(226, 226, 226); border-bottom-width: 1px; border-bottom-style: solid;' height='45'><a href='http://pf.kakao.com/_HxmtMxl' target='_blank'>http://pf.kakao.com/_HxmtMxl</a></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>";

}

$contents .= "<tr>
            <td height='30' align='center' style='padding: 10px 0px 30px;'><a href='https://actrip.co.kr/orderview?num=1&resNumber=".$arrMail['ResNumber']."' target='_blank'>
                <img src='http://www.smartix.co.kr/joinmail_img/03.png' border='0'></a>
            </td>
        </tr>
        <tr>
            <td style='height: 3px; line-height: 0; font-size: 0px; background-color: rgb(83, 83, 83);'>&nbsp;</td>
        </tr>
        <tr>
            <td align='center' style='padding-top: 25px; font-family: ;'>본 메일은 <span style='color: rgb(235, 75, 95);'>발신전용</span> 메일이므로 회신되지 않습니다.</td>
        </tr>
    </tbody>
</table>";

	return $contents;
}
?>