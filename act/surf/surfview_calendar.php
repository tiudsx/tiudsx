<?php
include __DIR__.'/../db.php';
$reqDate = $_REQUEST["selDate"];

$selDate = ($reqDate == "") ? str_replace("-", "", date("Y-m-d")) : $reqDate;
$seqCal = $_REQUEST["seq"];

if($seqCal == 0){
	$selDate = "20200901";
}

include __DIR__.'/../common/funcalendar.php';
$holidays = fnholidays();

$iDay;
$iMonLastDay;
$iWeekCnt;
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

if($seqCal == 13 || $seqCal == 15){
	$nowDate = date("Y-m-d");
}else{
	$nowDate = date("Y-m-d", strtotime(date("Y-m-d")." +1 day"));
}
// 변수 $s 에 새로운 값을 넣고 새문서를 만들면, $s 가 들어와 원하는 값을 표시해 줍니다.

echo ("
<div class='tour_calendar_box'>
	<div class='tour_calendar_header'>
");
if($selMonth > date("Ym", strtotime($nowMonth." -0 month"))){
	echo "<a href='javascript:fnCalMove(\"$p_m\", \"$seqCal\");' class='tour_calendar_prev'><span class='cal_ico'></span>이전</a>";
}

if($selMonth < date("Ym", strtotime($nowMonth." +3 month"))){
// if($selMonth < 202112){
	echo "<a href='javascript:fnCalMove(\"$n_m\", \"$seqCal\");' class='tour_calendar_next'><span class='cal_ico'></span>다음</a>";
}

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

if($reqDate != ""){
}

$arrPreDate = explode("-",$PreDate); // 들어온 날짜를 년,월,일로 분할해 변수로 저장합니다.
$PreDate_Y=$arrPreDate[0]; // 지정된 년도 
$PreDate_m=$arrPreDate[1]; // 지정된 월
$PreDate_d=$arrPreDate[2]; // 지정된 요일

$PreDate_t=date("t",mktime(0,0,0,$PreDate_m,$PreDate_d,$PreDate_Y)); // 지정된 달은 몇일까지 있을까요?

$select_query = "SELECT *, year(sdate) as yearS, month(sdate) as monthS, day(sdate) as dayS, year(edate) as yearE, month(edate) as monthE, day(edate) as dayE FROM AT_PROD_DAY where seq =".$_REQUEST["seq"]." AND sdate <= '$PreDate_Y.$PreDate_m.$PreDate_t' AND edate >= '$PreDate_Y.$PreDate_m.$PreDate_t' AND use_yn = 'Y' ORDER BY sdate";
$result_precal = mysqli_query($conn, $select_query);
$count_precal = mysqli_num_rows($result_precal);

if($count_precal == 0 || "$PreDate_Y-$PreDate_m-$PreDate_t" == date("Y-m-d")){
	echo "<tr style='display:none;'>";
	echo "	<td class='cal_type2'><span class='tour_td_block'><span class='tour_cal_day'>$PreDate_t</span><span class='tour_cal_pay' style='color:#d0d0d0;'></span></span></td>";
	echo "</tr>";
}else{
	while ($row_precal = mysqli_fetch_assoc($result_precal)){
		$day_type = $row_precal["day_type"];
		$lesson_price = $row_precal["lesson_price"];
		$rent_price = $row_precal["rent_price"];
		$stay_price = $row_precal["stay_price"];
		$bbq_price = $row_precal["bbq_price"];
		$pkg_price = $row_precal["pkg_price"];
		$day_week = $row_precal["day_week"];

		$forI=$PreDate_t;
		$forE=$PreDate_t;

		//예약 가능한 날짜 배열
		echo "<tr style='display:none;'>";
		for($i=$forI;$i<=$forE;$i++){
			$thisWeekNum = date("w",mktime(0,0,0,$Mon,$i,$Year));
			// if($thisWeekNum == 7) $thisWeekNum = 0;

			if($row_precal["week".$thisWeekNum] == "N"){
				echo "	<td class='cal_type2'><span class='tour_td_block'><span class='tour_cal_day'>$forE</span><span class='tour_cal_pay' style='color:#d0d0d0;'></span></span></td>";
				continue;
			}

			if(strrpos($day_week, (string)$thisWeekNum) === false){
				$calWeek[$i][$thisWeekNum] = array("day_week" => "Y", "day_type" => "$day_type", "lesson_price" => 0, "rent_price" => 0, "stay_price" => 0, "bbq_price" => 0, "pkg_price" => 0);
			}else{
				$calWeek[$i][$thisWeekNum] = array("day_week" => "Y", "day_type" => "$day_type", "lesson_price" => $lesson_price, "rent_price" => $rent_price, "stay_price" => $stay_price, "bbq_price" => $bbq_price, "pkg_price" => $pkg_price);
			}
			$calDay[$i] = $i;

			$weekChk = strpos($calWeek[$i][$thisWeekNum]["day_week"], "Y");

			$s = date("Y-m-d",mktime(0,0,0,$PreDate_m,$i,$PreDate_Y)); // 현재칸의 날짜
			if($s >= $nowDate && ($weekChk !== false)){
				$pricePlus = "day_type='".$calWeek[$i][$thisWeekNum]["day_type"]."' lesson_price='".$calWeek[$i][$thisWeekNum]["lesson_price"]."' rent_price='".$calWeek[$i][$thisWeekNum]["rent_price"]."' stay_price='".$calWeek[$i][$thisWeekNum]["stay_price"]."' bbq_price='".$calWeek[$i][$thisWeekNum]["bbq_price"]."' pkg_price='".$calWeek[$i][$thisWeekNum]["pkg_price"]."'";

				echo "<td class='cal_type2' style='cursor:pointer;'><calBox class='tour_td_block' value='$s' weekNum='$thisWeekNum' $pricePlus><span class='tour_cal_day' $holidayChk>$i</span><span class='tour_cal_pay'>예약가능</span></calBox></td>";
			}else{
				echo "<td class='cal_type2' style='padding-bottom:2px;'><span class='tour_td_block'><span class='tour_cal_day' style='color:#c2c2c2;'>$i</span><span class='tour_cal_pay' style='color:#d0d0d0;'></span></span></td>"; //종료
			}
		}
		echo "</tr>";
	}
}

$select_query = "SELECT *, year(sdate) as yearS, month(sdate) as monthS, day(sdate) as dayS, year(edate) as yearE, month(edate) as monthE, day(edate) as dayE FROM AT_PROD_DAY where seq =".$_REQUEST["seq"]." AND sdate <= '$s_Y.$s_m.$s_t' AND edate >= '$s_Y.$s_m.01' AND use_yn = 'Y' ORDER BY sdate";
// echo $select_query;
$result_cal = mysqli_query($conn, $select_query);
$count_cal = mysqli_num_rows($result_cal);

$calDay = array();
$calWeek = array();
while ($row_cal = mysqli_fetch_assoc($result_cal)){
	$day_type = $row_cal["day_type"];
	$lesson_price = $row_cal["lesson_price"];
	$rent_price = $row_cal["rent_price"];
	$stay_price = $row_cal["stay_price"];
	$bbq_price = $row_cal["bbq_price"];
	$pkg_price = $row_cal["pkg_price"];
	$day_week = $row_cal["day_week"];

	$forI=1;
	$forE=$s_t;

	if($row_cal["yearS"] == $s_Y && $row_cal["monthS"] == $s_m){
		$forI = $row_cal["dayS"];
	}

	if($row_cal["yearE"] == $s_Y && $row_cal["monthE"] == $s_m){
		$forE = $row_cal["dayE"];
	}

	//예약 가능한 날짜 배열
	for($i=$forI;$i<=$forE;$i++){
		$thisWeekNum = date("w",mktime(0,0,0,$Mon,$i,$Year));
		// if($thisWeekNum == 7) $thisWeekNum = 0;

		if($row_cal["week".$thisWeekNum] == "N"){
			continue;
		}

		if(strrpos($day_week, (string)$thisWeekNum) === false){
			$calWeek[$i][$thisWeekNum] = array("day_week" => "Y", "day_type" => "$day_type", "lesson_price" => 0, "rent_price" => 0, "stay_price" => 0, "bbq_price" => 0, "pkg_price" => 0);
		}else{
			$calWeek[$i][$thisWeekNum] = array("day_week" => "Y", "day_type" => "$day_type", "lesson_price" => $lesson_price, "rent_price" => $rent_price, "stay_price" => $stay_price, "bbq_price" => $bbq_price, "pkg_price" => $pkg_price);
		}
		$calDay[$i] = $i;
	}
}

//달력 for
for($r=0;$r<=$ra;$r++){
	echo "<tr>";

	for($z=1;$z<=7;$z++){
		$rv=7*$r+$z; $ru=$rv-$l; // 칸에 번호를 매겨줍니다. 1일이 되기전 공백들 부터 마이너스 값으로 채운 뒤 ~ 

		if($ru<=0 || $ru>$s_t){ 
			echo "<td><span class='tour_td_block'><span class='tour_cal_day'>&nbsp;</span></span></td>";
		}else{
			$s = date("Y-m-d",mktime(0,0,0,$s_m,$ru,$s_Y)); // 현재칸의 날짜
			//$week = date("w", strtotime($s));
			$weeknum = $z - 1;

			$calMD = explode("-",$s)[1].explode("-",$s)[2];
			$holidayChk = "";
			if(array_key_exists($calMD, $holidays)){
				if($holidays[$calMD]["year"] == "" || $Year == $holidays[$calMD]["year"]){
					$holidayChk = " style='color:red;'";
				}
			}

			$onOff = 0;
			if(array_key_exists($ru, $calDay)){
				$onOff = 1;
			}

			if($onOff == 0)
			{
				//echo "<td class='cal_type2'><span class='tour_td_block'><span class='tour_cal_day' $holidayChk'>$ru</span><span class='tour_cal_pay' style='color:#d0d0d0;'></span></span></td>";
				echo "<td class='cal_type2' style='padding-bottom:2px;'><span class='tour_td_block'><span class='tour_cal_day' style='color:#c2c2c2;'>$ru</span><span class='tour_cal_pay' style='color:#d0d0d0;'></span></span></td>"; //종료
			}
			else
			{
				$weekChk = strpos($calWeek[$ru][$weeknum]["day_week"], "Y");

				if($s >= $nowDate && ($weekChk !== false)){
					$pricePlus = "day_type='".$calWeek[$ru][$weeknum]["day_type"]."' lesson_price='".$calWeek[$ru][$weeknum]["lesson_price"]."' rent_price='".$calWeek[$ru][$weeknum]["rent_price"]."' stay_price='".$calWeek[$ru][$weeknum]["stay_price"]."' bbq_price='".$calWeek[$ru][$weeknum]["bbq_price"]."' pkg_price='".$calWeek[$ru][$weeknum]["pkg_price"]."'";
					echo "<td class='cal_type2' style='cursor:pointer;'><calBox class='tour_td_block' value='$s' weekNum='$weeknum' onclick='fnPassenger(this);' $pricePlus><span class='tour_cal_day' $holidayChk>$ru</span><span class='tour_cal_pay'>예약가능</span></calBox></td>";
				}else{
					echo "<td class='cal_type2' style='padding-bottom:2px;'><span class='tour_td_block'><span class='tour_cal_day' style='color:#c2c2c2;'>$ru</span><span class='tour_cal_pay' style='color:#d0d0d0;'></span></span></td>"; //종료
				}
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