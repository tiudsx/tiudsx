<?php
$reqDate = $_REQUEST["selDate"];
if($reqDate != ""){
	include __DIR__.'/../../common/db.php';
	$shopseq = $_REQUEST["seq"];
}

include __DIR__.'/../../common/func.php';
$holidays = fnholidays();

$selDate = ($reqDate == "") ? str_replace("-", "", date("Y-m-d")) : $reqDate;
$selDay = $_REQUEST["selDay"];

$Year = substr($selDate,0,4);
$Mon = substr($selDate,4,2);

$datDate = date("Y-m-d", mktime(0, 0, 0, $Mon, 1, $Year));
$NextDate = date("Y-m-d", strtotime($datDate." +1 month"));
$PreDate = date("Y-m-d", strtotime($datDate." -1 month"));
$now = date("Y-m-d A h:i:s");

$x = explode("-",$datDate); // 들어온 날짜를 년,월,일로 분할해 변수로 저장합니다.
$s_Y=$x[0]; // 지정된 년도 
$s_m=$x[1]; // 지정된 월
$s_d=$x[2]; // 지정된 요일

$nowMonth = date("Ym");
$selMonth = date("Ym",mktime(0,0,0,$s_m,$s_d,$s_Y));

$s_t=date("t",mktime(0,0,0,$s_m,$s_d,$s_Y)); // 지정된 달은 몇일까지 있을까요?
$s_n=date("N",mktime(0,0,0,$s_m,1,$s_Y)); // 지정된 달의 첫날은 무슨요일일까요?
$l=$s_n%7; // 지정된 달 1일 앞의 공백 숫자.
$ra=($s_t+$l)/7; $ra=ceil($ra); $ra=$ra-1; // 지정된 달은 총 몇주로 라인을 그어야 하나? 

$n_d= date("Y-m-d",mktime(0,0,0,$s_m,$s_d+1,$s_Y)); // 다음날
$p_d= date("Y-m-d",mktime(0,0,0,$s_m,$s_d-1,$s_Y)); // 이전날

$n_m= date("Ym",mktime(0,0,0,$s_m+1,$s_d,$s_Y)); // 다음달 (빠뜨린 부분 추가분이에요)
$p_m= date("Ym",mktime(0,0,0,$s_m-1,$s_d,$s_Y)); // 이전달
$n_Y= date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y+1)); // 내년
$p_Y= date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y-1)); // 작년

// 변수 $s 에 새로운 값을 넣고 새문서를 만들면, $s 가 들어와 원하는 값을 표시해 줍니다.

echo ("
<div class='tour_calendar_box'>
	<div class='tour_calendar_header'>
");
if($selMonth > 202003){
	echo "<a href='javascript:fnCalMoveAdminList(\"$p_m\", 0, -3);' class='tour_calendar_prev'><span class='cal_ico'></span>이전</a>";
}

//if($selMonth < date("Ym", strtotime($nowMonth." +3 month"))){
// if($selMonth < 202012){
	echo "<a href='javascript:fnCalMoveAdminList(\"$n_m\", 0, -3);' class='tour_calendar_next'><span class='cal_ico'></span>다음</a>";
// }

echo ("
		<div class='tour_calendar_title'>
			<span class='tour_calendar_month'>$s_Y.$s_m</span>
		</div>
	</div>
	<table class='tour_calendar'>
		<thead>
			<tr>
				<th scope='col'><span>SUN</span></th>
				<th scope='col'><span>MON</span></th>
				<th scope='col'><span>TUE</span></th>
				<th scope='col'><span>WED</span></th>
				<th scope='col'><span>THU</span></th>
				<th scope='col'><span>FRI</span></th>
				<th scope='col'><span>SAT</span></th>
			</tr>
		</thead>
		<tbody>
	");
	
$select_query_cal = 'SELECT COUNT(*) AS Cnt, res_date, DAY(res_date) AS sDay, res_confirm FROM `AT_RES_SUB`
			WHERE code = "bus"			
				AND seq = 14
				AND (Year(res_date) = '.$Year.' AND Month(res_date) = '.$Mon.')
			GROUP BY res_date, res_confirm';
$result_setlist_cal = mysqli_query($conn, $select_query_cal);

$arrResCount = array();
while ($rowCal = mysqli_fetch_assoc($result_setlist_cal)){
	$arrResCount[$rowCal['res_confirm']][$rowCal['sDay']] = $rowCal['Cnt'];
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

//달력 for
for($r=0;$r<=$ra;$r++){
	echo "<tr>";

	for($z=1;$z<=7;$z++){
		$rv=7*$r+$z; $ru=$rv-$l; // 칸에 번호를 매겨줍니다. 1일이 되기전 공백들 부터 마이너스 값으로 채운 뒤 ~ 

		if($ru<=0 || $ru>$s_t){ 
			echo "<td><span class='tour_td_block' style='min-height:50px;'><span class='tour_cal_day'>&nbsp;</span></span></td>";
		}else{
			$s = date("Y-m-d",mktime(0,0,0,$s_m,$ru,$s_Y)); // 현재칸의 날짜
			$h = date("H");
			$weeknum = $z - 1;

			$calMD = explode("-",$s)[1].explode("-",$s)[2];
			$holidayChk = "";
			if(array_key_exists($calMD, $holidays)){
				if($holidays[$calMD]["year"] == "" || $Year == $holidays[$calMD]["year"]){
					$holidayChk = " style='color:red;'";
				}
			}
			
			$adminText = "";
			$gubunChk = "";
			
			if($arrResCount[3][$ru] != ""){
				$adminText .= "<br><font color='red'><b>".$arrResCount[3][$ru]."명 예약</b></font>";
				$gubunChk .= "3,";
			}

			$gubunChk .= "99";

			$selYN = 'no';
			$selYNbg = '';
			if($selDay == $ru){
				$selYN = 'yes';
				$selYNbg = 'background:#efefef;';
			}
			
			if($gubunChk == "99"){
				echo "<td><span class='tour_td_block' style='min-height:50px;'><span class='tour_cal_day' $holidayChk>$ru</span></span></td>";
			}else{
				echo "<td class='cal_type2'><calBox sel='$selYN' style='min-height:50px;$selYNbg' class='tour_td_block' value='$s' weekNum='$weeknum' gubunchk='$gubunChk' onclick='fnPassengerAdmin(this, -3);'><span class='tour_cal_day' $holidayChk>$ru</span><span class='tour_cal_pay'>$adminText</span></calBox></td>";
			}
		}
	}

	echo "</tr>";
}

echo ("
		</tbody>
	</table>
</div>");
?>