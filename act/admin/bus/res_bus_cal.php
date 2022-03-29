<?php
if($_REQUEST["selDate"]  == ""){
	include __DIR__.'/../common/funcalendar.php';
    $selDate = str_replace("-", "", date("Y-m-d"));
}else{
	include __DIR__.'/../../common/funcalendar.php';
	include __DIR__.'/../../db.php';
    $selDate = $_REQUEST["selDate"];
}

$holidays = fnholidays();

$selDay = $_REQUEST["selDay"];
$iDay;
$iMonLastDay;
$iWeekCnt;
$Year = substr($selDate,0,4);
$Mon = substr($selDate,4,2);

$datDate = date("Y-m-d", mktime(0, 0, 0, $Mon, 1, $Year));
$NextDate = date("Y-m-d", strtotime($datDate." +1 month"));
$PreDate = date("Y-m-d", strtotime($datDate." -1 month"));
$now = date("Y-m-d A h:i:s");

	$x=explode("-",$datDate); // 들어온 날짜를 년,월,일로 분할해 변수로 저장합니다.
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

	$nowDate = date("Y-m-d");
// 변수 $s 에 새로운 값을 넣고 새문서를 만들면, $s 가 들어와 원하는 값을 표시해 줍니다.

	echo ("
    <div class='tour_calendar_box'>
        <div class='tour_calendar_header'>
	");
	echo "<a href='javascript:fnCalMoveAdminCal(\"$p_m\", 0);' class='tour_calendar_prev'><span class='cal_ico'></span>이전</a>";
	echo ("
            <a href='javascript:fnCalMoveAdminCal(\"$n_m\", 0);' class='tour_calendar_next'><span class='cal_ico'></span>다음</a>
            <div class='tour_calendar_title'>
                <span class='tour_calendar_month'>$s_Y.$s_m</span>
            </div>
        </div>
        <table class='tour_calendar' summary='출발일을 선택하는 달력입니다. 달력 선택후 출발 시간 선택 레이어가 활성화 됩니다.'>
            <caption>출발일 선택 달력</caption>
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

	$select_query = '
		SELECT * FROM (
            SELECT "일반" AS title, COUNT(*) AS Cnt, res_date, DAY(res_date) AS sDay, SUM(res_price) AS price, SUM(res_coupon) AS coupon, SUM(res_totalprice) AS total_price, res_confirm FROM `AT_RES_SUB`
							WHERE res_confirm = 3
                            	AND code = "bus"
								AND (Year(res_date) = '.$Year.' AND Month(res_date) = '.$Mon.')
								AND res_coupon NOT IN ("JOABUS", "KLOOK", "NAVER", "FRIP", "MYTRIP")
                            GROUP BY res_date
            UNION ALL
            SELECT "네이버" AS title, COUNT(*) AS Cnt, res_date, DAY(res_date) AS sDay, SUM(res_price) AS price, SUM(res_coupon) AS coupon, (COUNT(*) * 20000) AS total_price, res_confirm FROM `AT_RES_SUB`
							WHERE res_confirm = 3
                                AND code = "bus"
                                AND res_coupon = "NAVER"
								AND (Year(res_date) = '.$Year.' AND Month(res_date) = '.$Mon.')
							GROUP BY res_date
			UNION ALL
            SELECT "조아" AS title, COUNT(*) AS Cnt, res_date, DAY(res_date) AS sDay, SUM(res_price) AS price, SUM(res_coupon) AS coupon, (COUNT(*) * 17500) AS total_price, res_confirm FROM `AT_RES_SUB`
							WHERE res_confirm = 3
                                AND code = "bus"
                                AND res_coupon = "JOABUS"
								AND (Year(res_date) = '.$Year.' AND Month(res_date) = '.$Mon.')
							GROUP BY res_date
			UNION ALL
			SELECT "KLOOK" AS title, COUNT(*) AS Cnt, res_date, DAY(res_date) AS sDay, SUM(res_price) AS price, SUM(res_coupon) AS coupon, (COUNT(*) * 15000) AS total_price, res_confirm FROM `AT_RES_SUB`
							WHERE res_confirm = 3
								AND code = "bus"
								AND res_coupon = "KLOOK"
								AND (Year(res_date) = '.$Year.' AND Month(res_date) = '.$Mon.')
							GROUP BY res_date
			UNION ALL
			SELECT "FRIP" AS title, COUNT(*) AS Cnt, res_date, DAY(res_date) AS sDay, SUM(res_price) AS price, SUM(res_coupon) AS coupon, (COUNT(*) * 16000) AS total_price, res_confirm FROM `AT_RES_SUB`
							WHERE res_confirm = 3
								AND code = "bus"
								AND res_coupon = "FRIP"
								AND (Year(res_date) = '.$Year.' AND Month(res_date) = '.$Mon.')
							GROUP BY res_date
			UNION ALL
			SELECT "MYTRIP" AS title, COUNT(*) AS Cnt, res_date, DAY(res_date) AS sDay, SUM(res_price) AS price, SUM(res_coupon) AS coupon, (COUNT(*) * 16000) AS total_price, res_confirm FROM `AT_RES_SUB`
							WHERE res_confirm = 3
								AND code = "bus"
								AND res_coupon = "MYTRIP"
								AND (Year(res_date) = '.$Year.' AND Month(res_date) = '.$Mon.')
							GROUP BY res_date
			UNION ALL
			SELECT "서프존" AS title, COUNT(*) AS Cnt, res_date, DAY(res_date) AS sDay, SUM(res_price) AS price, SUM(res_coupon) AS coupon, (COUNT(*) * 16000) AS total_price, res_confirm FROM `AT_RES_SUB`
							WHERE res_confirm = 3
								AND code = "bus"
								AND res_coupon = "SURFX"
								AND (Year(res_date) = '.$Year.' AND Month(res_date) = '.$Mon.')
							GROUP BY res_date
			UNION ALL
			SELECT "망고2" AS title, COUNT(*) AS Cnt, res_date, DAY(res_date) AS sDay, SUM(res_price) AS price, SUM(res_coupon) AS coupon, (COUNT(*) * 15000) AS total_price, res_confirm FROM `AT_RES_SUB` a INNER JOIN `AT_RES_MAIN` b
							ON a.resnum = b.resnum
								AND res_coupon = "MANGO"
							WHERE res_confirm = 3
								AND code = "bus"
								AND (Year(res_date) = '.$Year.' AND Month(res_date) = '.$Mon.')
							GROUP BY res_date
			UNION ALL
			SELECT "망고" AS title, COUNT(*) AS Cnt, res_date, DAY(res_date) AS sDay, SUM(res_price) AS price, SUM(res_coupon) AS coupon, (COUNT(*) * 20000) AS total_price, res_confirm FROM `AT_RES_SUB` a INNER JOIN `AT_RES_MAIN` b
							ON a.resnum = b.resnum
                         INNER JOIN AT_COUPON_CODE c
                         	ON a.res_coupon = c.coupon_code
                            	AND c.couponseq = 14
							WHERE res_confirm = 3
								AND code = "bus"
								AND (Year(res_date) = '.$Year.' AND Month(res_date) = '.$Mon.')
							GROUP BY res_date
		) as a';
	//echo $select_query;
	$result_setlist = mysqli_query($conn, $select_query);

	$arrResCount = array();
	$arrResJoaCount = array();
	$arrResKlookCount = array();
	$arrResFripCount = array();
	$arrResSurfxCount = array();
	$arrResMyCount = array();
	$arrResMangCount = array();
	$arrResMangPrice = array();
	$arrResNaverCount = array();
	while ($row = mysqli_fetch_assoc($result_setlist)){
        if($row['title'] == "조아"){
            $arrResJoaCount[$row['res_confirm']][$row['sDay']] = $row['total_price'];
        }else if($row['title'] == "KLOOK"){
            $arrResKlookCount[$row['res_confirm']][$row['sDay']] = $row['total_price'];
        }else if($row['title'] == "FRIP"){
            $arrResFripCount[$row['res_confirm']][$row['sDay']] = $row['total_price'];
        }else if($row['title'] == "서프존"){
            $arrResSurfxCount[$row['res_confirm']][$row['sDay']] = $row['total_price'];
        }else if($row['title'] == "MYTRIP"){
            $arrResMyCount[$row['res_confirm']][$row['sDay']] = $row['total_price'];
        }else if($row['title'] == "네이버"){
            $arrResNaverCount[$row['res_confirm']][$row['sDay']] = $row['total_price'];
        }else if($row['title'] == "망고"){
            $arrResMangCount[$row['res_confirm']][$row['sDay']] = $row['total_price'];
            // $arrResMangPrice[$row['res_confirm']][$row['sDay']] = $row['price'];
        }else{
            $arrResCount[$row['res_confirm']][$row['sDay']] = $row['total_price'];
        }
	}

/*

            UNION ALL         
			SELECT "합계" AS title, COUNT(*) AS Cnt, LEFT(res_date, 7), "" AS sDay, SUM(ResPrice) AS price, ResConfirm FROM SURF_BUS_SUB
							WHERE ResConfirm = 3
								AND (Year(res_date) = '.$Year.' AND Month(res_date) = '.$Mon.')
                            GROUP BY LEFT(res_date, 7)
                            
	while ($row = mysqli_fetch_assoc($result_setlist)){
		if($row['title'] == "합계"){
			$arrTotalPrice = number_format($row['total']);
		}else{
			$arrResCount[$row['sDay'].$row['PriceType']] = $row['total'];
		}
	}
*/

    for($r=0;$r<=$ra;$r++){
        echo "<tr>";

		for($z=1;$z<=7;$z++){
			$rv=7*$r+$z; $ru=$rv-$l; // 칸에 번호를 매겨줍니다. 1일이 되기전 공백들 부터 마이너스 값으로 채운 뒤 ~ 

			if($ru<=0 || $ru>$s_t){ 
				echo "<td><span class='tour_td_block' style='min-height:55px;'><span class='tour_cal_day'>&nbsp;</span></span></td>";
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
				$daySum = 0;

                //일반
				if($arrResCount[3][$ru] != ""){
					$daySum += $arrResCount[3][$ru];
					$adminText .= '<br><font color="black">'.number_format($arrResCount[3][$ru])."원</font>";
                }
                
                //네이버
                if($arrResNaverCount[3][$ru] != ""){
					$daySum += $arrResNaverCount[3][$ru];
					$adminText .= '<br><font color="black">네이버:'.number_format($arrResNaverCount[3][$ru])."원</font>";
				}
                
                //조아
                if($arrResJoaCount[3][$ru] != ""){
					$daySum += $arrResJoaCount[3][$ru];
					$adminText .= '<br><font color="black">조아:'.number_format($arrResJoaCount[3][$ru])."원</font>";
				}

				//FRIP
                if($arrResFripCount[3][$ru] != ""){
					$daySum += $arrResFripCount[3][$ru];
					$adminText .= '<br><font color="black">FRIP:'.number_format($arrResFripCount[3][$ru])."원</font>";
				}

				//서프존
                if($arrResSurfxCount[3][$ru] != ""){
					$daySum += $arrResSurfxCount[3][$ru];
					$adminText .= '<br><font color="black">서프존:'.number_format($arrResSurfxCount[3][$ru])."원</font>";
				}


				//마이리얼트립
                if($arrResMyCount[3][$ru] != ""){
					$daySum += $arrResMyCount[3][$ru];
					$adminText .= '<br><font color="black">마이리얼:'.number_format($arrResMyCount[3][$ru])."원</font>";
				}

				//클룩
                if($arrResKlookCount[3][$ru] != ""){
					$daySum += $arrResKlookCount[3][$ru];
					$adminText .= '<br><font color="black">KLOOK:'.number_format($arrResKlookCount[3][$ru])."원</font>";
				}

				//망고
                if($arrResMangCount[3][$ru] != ""){
					$daySum += $arrResMangCount[3][$ru];
					// $daySum -= $arrResMangPrice[3][$ru];
					$adminText .= '<br><font color="black">망고:'.number_format($arrResMangCount[3][$ru])."원</font>";
				}


				$selYN = 'no';
				$selYNbg = '';
				if($selDay == $ru){
					$selYN = 'yes';
					$selYNbg = 'background:#efefef;';
				}
                
                $daySumText = "";
                if($daySum > 0){
                    $daySumText = "<br>총 : ".number_format($daySum)."원";
                }
				echo "<td class='cal_type2'><calBox sel='$selYN' style='min-height:105px;$selYNbg' class='tour_td_block' value='$s' weekNum='$weeknum'><span class='tour_cal_day' $holidayChk>$ru</span><span class='tour_cal_pay'>$adminText $daySumText</span></calBox></td>";
			}
		}

        echo "</tr>";
    }
echo ("
            </tbody>
        </table>
    </div>");
?>