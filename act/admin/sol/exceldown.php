<?php
include __DIR__.'/../../db.php';

session_start();

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('Asia/Seoul');

require_once __DIR__.'/../Classes/PHPExcel.php';

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load(__DIR__."/SolStayTemplate.xls");

$baseRow = 5;

$selDate = $_REQUEST["selDate"];

$arrDate = explode('-', $selDate);
$Year = $arrDate[0];
$Mon = $arrDate[1];
$Day = $arrDate[2];

$select_query = "SELECT *, DAY(b.sdate) AS sDay, DAY(b.edate) AS eDay, DAY(b.resdate) AS resDay, MONTH(b.sdate) AS sMonth, MONTH(b.edate) AS eMonth, MONTH(b.resdate) AS resMonth, DATEDIFF(b.edate, b.sdate) as eDateDiff FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                    WHERE ((b.sdate <= '$selDate' AND DATE_ADD(b.edate, INTERVAL -1 DAY) >= '$selDate')
                        OR	b.resdate = '$selDate')
                        AND b.res_type = 'stay'
                        AND a.res_confirm = '확정'
                        ORDER BY ressubseq";

$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

//=========== 샵 정보 가져오기 ===========
$objPHPExcel -> setActiveSheetIndex(0); //엑셀 sheet 선택
$objPHPExcel->getActiveSheet()->setCellValue("A1", "[".$selDate."] 솔게스트하우스 예약현황");

//$objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle('A1'),'A2:A10'); 
//시트타이틀
//$objPHPExcel->getActiveSheet()->setTitle('PhpSpreadsheet Test Document');

$r = 0;
$stayMCnt = 0;
$stayWCnt = 0;
$bbqMCnt = 0;
$bbqWCnt = 0;
$pubMCnt = 0;
$pubWCnt = 0;

$stayinfo = "";
while ($row = mysqli_fetch_assoc($result_setlist)){
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
    $prod_name = $row['prod_name'];
    $sdate = $row['sdate'];
    $edate = $row['edate'];
    $resdate = $row['resdate'];
    $staysex = $row['staysex'];
    $stayM = $row['stayM'];
    $stayroom = $row['stayroom'];
    $staynum = $row['staynum'];
	$bbq = $row['bbq'];

	$stayMText = "";
	$stayWText = "";

    if($prod_name != "N"){
        if($staynum != ""){
            $staynum = $staynum."번 (".((($staynum % 2) == 0) ? "2층" : "1층").")";
        }

        if($row['sMonth'] == $Mon || $row['eMonth'] == $Mon){
			$stayinfo = str_replace("-", ".", substr($sdate, 5, 10))." ~ ".str_replace("-", ".", substr($edate, 5, 10));

			if($staysex == "남"){
				$stayMCnt += $stayM;
				$stayMText = $stayM;
			}else{
				$stayWCnt += $stayM;
				$stayWText = $stayM;
			}
		}
    }

	$bbqMText = "";
	$bbqWText = "";
	$pubMText = "";
	$pubWText = "";
    if($bbq != "N" && $Day == $row['resDay']){
		if($staysex == "남"){
			$stayMText = $stayM;
		}else{
			$stayWText = $stayM;
		}

        if($staysex == "남"){
            if($bbq == "바베큐"){
                $bbqMCnt += $stayM;
				$bbqMText = $stayM;
            }else if($bbq == "펍파티"){
                $pubMCnt += $stayM;
				$pubMText = $stayM;
            }else{
                $bbqMCnt += $stayM;
                $pubMCnt += $stayM;
				$bbqMText = $stayM;
				$pubMText = $stayM;
            }
        }else{
            if($bbq == "바베큐"){
                $bbqWCnt += $stayM;
				$pubWText = $stayM;
            }else if($bbq == "펍파티"){
                $pubWCnt += $stayM;
				$stayWText = $stayM;
            }else{
                $bbqWCnt += $stayM;
                $pubWCnt += $stayM;
				$stayWText = $stayM;
				$pubWText = $stayM;
            }
        }
    }
	

	$cellnum = $baseRow + $r;
	if($r > 0){
		$objPHPExcel->getActiveSheet()->insertNewRowBefore($cellnum,1);
	}
    
    if($prod_name != "N" && $prod_name != "솔게스트하우스"){
		$objPHPExcel -> getActiveSheet() -> getStyle("A".$cellnum.":"."N".$cellnum) -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("D8E4BC");
	}else{
		$objPHPExcel -> getActiveSheet() -> getStyle("A".$cellnum.":"."N".$cellnum) -> getFill() -> setFillType(PHPExcel_Style_Fill::FILL_SOLID) -> getStartColor() -> setRGB("FFFFFF");
	}

	$objPHPExcel->getActiveSheet()
		->setCellValue("A".$cellnum, $user_name)
		->setCellValue("B".$cellnum, $user_tel)
		->setCellValue("C".$cellnum, (($prod_name == "N") ? "" : $prod_name))
		->setCellValue("D".$cellnum, $stayinfo)
		->setCellValue("E".$cellnum, (($stayroom == "") ? "" : $stayroom."호"))
		->setCellValue("F".$cellnum, $staynum)
		->setCellValue("G".$cellnum, $stayMText)
		->setCellValue("H".$cellnum, $stayWText)
		->setCellValue("I".$cellnum, $bbqMText)
		->setCellValue("J".$cellnum, $bbqWText)
		->setCellValue("K".$cellnum, $pubMText)
		->setCellValue("L".$cellnum, $pubWText)
		->setCellValue("M".$cellnum, $memo)
		->setCellValue("N".$cellnum, $memo2);
	$r++;
	
}

$cellnum = $baseRow + $r;
$objPHPExcel->getActiveSheet()
	->setCellValue("G".$cellnum, $stayMCnt)
	->setCellValue("H".$cellnum, $stayWCnt)
	->setCellValue("I".$cellnum, $bbqMCnt)
	->setCellValue("J".$cellnum, $bbqWCnt)
	->setCellValue("K".$cellnum, $pubMCnt)
	->setCellValue("L".$cellnum, $pubWCnt);


//서핑강습
$select_query = "SELECT * FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                    WHERE b.resdate = '$selDate'
                        AND b.res_type = 'surf'
                        AND a.res_confirm = '확정'
                        ORDER BY  b.ressubseq, b.prod_name, b.restime";
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

$surfcnt = array();
$rentcnt = 0;
$arrsurf = array();
$arrrent = array();
$surfsolcnt = array();
$surfspcnt = array();
$surflalacnt = array();
$surfrangcnt = array();
while ($row = mysqli_fetch_assoc($result_setlist)){
	$now = date("Y-m-d");

    $resseq = $row['resseq'];
    $admin_user = $row['admin_user'];
	$res_company = $row['res_company'];
	$user_name = $row['user_name'];
    $user_tel = $row['user_tel'];
    $memo = $row['memo'];
    $memo2 = $row['memo2'];
    $ressubseq = $row['ressubseq'];
    $res_type = $row['res_type'];
    $prod_name = $row['prod_name'];
    $resdate = $row['resdate'];
    $restime = $row['restime'];
    $surfM = $row['surfM'];
    $surfW = $row['surfW'];
    $surfrent = $row['surfrent'];
    $surfrentM = $row['surfrentM'];
    $surfrentW = $row['surfrentW'];
    $surfrentYN = $row['surfrentYN'];

    $memoYN = "";
    if($memo != "" || $memo2 != ""){
        $memoYN = "O";
    }

    //강습&렌탈
    if($prod_name != "N"){
        //서핑샵+강습시간 row count
		$arrykey = str_replace("시", "", $restime);
		$data = "$user_name/$user_tel/".(($surfM == 0) ? "" : $surfM)."/".(($surfW == 0) ? "" : $surfW)."/$memoYN";

		if($prod_name == "솔게스트하우스"){
			if(array_key_exists($arrykey, $surfsolcnt)){
				$surfsolcnt[$arrykey]++;
			}else{
				$surfsolcnt[$arrykey] = 0;
			}
			$arrsurf[$prod_name][$arrykey][$surfsolcnt[$arrykey]] = $data;
		}else if($prod_name == "서프팩토리"){
			if(array_key_exists($arrykey, $surfspcnt)){
				$surfspcnt[$arrykey]++;
			}else{
				$surfspcnt[$arrykey] = 0;
			}
			$arrsurf[$prod_name][$arrykey][$surfspcnt[$arrykey]] = $data;
		}else if($prod_name == "라라서프"){
			if(array_key_exists($arrykey, $surflalacnt)){
				$surflalacnt[$arrykey]++;
			}else{
				$surflalacnt[$arrykey] = 0;
			}
			$arrsurf[$prod_name][$arrykey][$surflalacnt[$arrykey]] = $data;
		}else if($prod_name == "서퍼랑"){
			if(array_key_exists($arrykey, $surfrangcnt)){
				$surfrangcnt[$arrykey]++;
			}else{
				$surfrangcnt[$arrykey] = 0;
			}
			$arrsurf[$prod_name][$arrykey][$surfrangcnt[$arrykey]] = $data;
		}
		// if(array_key_exists($arrykey, $surfcnt[$prod_name])){
		// 	$surfcnt[$prod_name][$arrykey]++;
		// }else{
		// 	$surfcnt[$prod_name][$arrykey] = 0;
        // }
        
    	//echo $surfcnt[$prod_name][$arrykey].":"."$prod_name/$restime/$user_name/$user_tel/".(($surfM == 0) ? "" : $surfM)."/".(($surfW == 0) ? "" : $surfW)."/$memoYN"."<Br>";
		//$arrsurf[$prod_name][$arrykey][$surfcnt[$prod_name][$arrykey]] = "$user_name/$user_tel/".(($surfM == 0) ? "" : $surfM)."/".(($surfW == 0) ? "" : $surfW)."/$memoYN";
    }

    if($surfrent != "N"){
		$arrrent[$rentcnt] = "$user_name/$user_tel/$surfrent/".(($surfrentM == 0) ? "" : $surfrentM)."/".(($surfrentW == 0) ? "" : $surfrentW)."/$memoYN";
		$rentcnt++;
    }
//while end
}

if($count > 0){		
	$objPHPExcel -> setActiveSheetIndex(1); //엑셀 sheet 선택
	$objPHPExcel->getActiveSheet()->setCellValue("A1", "[".$selDate."] 서핑강습 예약현황");

	//렌탈 예약
	$r = 0;
	if(count($arrrent)){
		
		foreach ($arrrent as $key => $value) {
			$cellnum = $baseRow + $r;
			if($r > 0){
				 $objPHPExcel->getActiveSheet()->insertNewRowBefore($cellnum,1);
			}

			$arrVlu = explode("/", $value);
			$objPHPExcel->getActiveSheet()
				->setCellValue("A".$cellnum, $arrVlu[0])
				->setCellValue("B".$cellnum, $arrVlu[1])
				->setCellValue("C".$cellnum, $arrVlu[2])->mergeCells("C".$cellnum.':D'.$cellnum)
				->setCellValue("E".$cellnum, $arrVlu[3])
				->setCellValue("F".$cellnum, $arrVlu[4])
				->setCellValue("G".$cellnum, $arrVlu[5]);
			$r++;
		}
	}

	//강습 예약
	$surfsol = "";
	$surfsp = "";
	$surflala = "";
	$surfrang = "";
	
	$surfsolcnt = 0;
	$surfspcnt = 0;
	$surflalacnt = 0;
	$surfrangcnt = 0;
	if(count($arrsurf)){
		foreach ($arrsurf as $key => $value) {
			foreach ($value as $key2 => $value2) {
				// echo "<Br>".count($value2).":$key2/$value2";
				if($key == "솔게스트하우스"){
					if(count($value2) > $surfsolcnt){
						$surfsolcnt = count($value2);
					}
				}else if($key == "서프팩토리"){
					if(count($value2) > $surfspcnt){
						$surfspcnt = count($value2);
					}
				}else if($key == "라라서프"){
					if(count($value2) > $surflalacnt){
						$surflalacnt = count($value2);
					}
				}else if($key == "서퍼랑"){
					if(count($value2) > $surfrangcnt){
						$surfrangcnt = count($value2);
					}
				}
			}

			if($key == "솔게스트하우스"){
				$surfsol = "Y";
			}else if($key == "서프팩토리"){
				$surfsp = "Y";
			}else if($key == "라라서프"){
				$surflala = "Y";
			}else if($key == "서퍼랑"){
				$surfrang = "Y";
			}
		}

		// $cellnum = $baseRow + $r;
		// if($surflala == ""){
		// 	$objPHPExcel->getActiveSheet()->removeRow($cellnum + 21, 7);
		// }
	
		// if($surfrang == ""){
		// 	$objPHPExcel->getActiveSheet()->removeRow($cellnum + 14, 7);
		// }
	
		// if($surfsp == ""){
		// 	$objPHPExcel->getActiveSheet()->removeRow($cellnum + 7, 7);
		// }
	
		// if($surfsol == ""){
		// 	$objPHPExcel->getActiveSheet()->removeRow($cellnum + 7, 7);
		// }

		if($r < 2){
			$cellnum = $baseRow + 1;
		}else{
			$cellnum = $baseRow + $r;
		}

		$totalsol = 1;
		$totalsp = 1;
		$totalrang = 1;
		$totallala = 1;
		if($surflalacnt > 1){
			$objPHPExcel->getActiveSheet()->insertNewRowBefore($cellnum + 26,$surflalacnt - 1);
			$totallala = $surflalacnt;
		}else if($surflalacnt == 0){
			//$objPHPExcel->getActiveSheet()->removeRow($cellnum + 21, 7);
		}
		
		if($surfrangcnt > 1){
			$objPHPExcel->getActiveSheet()->insertNewRowBefore($cellnum + 19,$surfrangcnt - 1);
			$totalrang = $surfrangcnt;
		}else if($surfrangcnt == 0){
			//$objPHPExcel->getActiveSheet()->removeRow($cellnum + 14, 7);
		}

		if($surfspcnt > 1){
			$objPHPExcel->getActiveSheet()->insertNewRowBefore($cellnum + 12,$surfspcnt - 1);
			$totalsp = $surfspcnt;
		}else if($surfspcnt == 0){
			//$objPHPExcel->getActiveSheet()->removeRow($cellnum + 7, 7);
		}

		if($surfsolcnt > 1){
			$objPHPExcel->getActiveSheet()->insertNewRowBefore($cellnum + 5,$surfsolcnt - 1);
			$totalsol = $surfsolcnt;
		}else if($surfsolcnt == 0){
			//$objPHPExcel->getActiveSheet()->removeRow($cellnum + 1, 6);
		}
		$surflalacnt = $cellnum + 25 + (($surfrangcnt < 2) ? 0 : $surfrangcnt - 1) + (($surfspcnt < 2) ? 0 : $surfspcnt - 1) + (($surfsolcnt < 2) ? 0 : $surfsolcnt - 1);
		$surfrangcnt = $cellnum + 18 + (($surfspcnt < 2) ? 0 : $surfspcnt - 1) + (($surfsolcnt < 2) ? 0 : $surfsolcnt - 1);
		$surfspcnt = $cellnum + 11 + (($surfsolcnt < 2) ? 0 : $surfsolcnt - 1);
		$surfsolcnt = $cellnum + 4;
		
		foreach ($arrsurf as $key => $value) {
			if($key == "솔게스트하우스"){
				$baseRow = $surfsolcnt;
				$baseRowTotal = $baseRow + $totalsol;
			}else if($key == "서프팩토리"){
				$baseRow = $surfspcnt;
				$baseRowTotal = $baseRow + $totalsp;
			}else if($key == "라라서프"){
				$baseRow = $surflalacnt;
				$baseRowTotal = $baseRow + $totalrang;
			}else if($key == "서퍼랑"){
				$baseRow = $surfrangcnt;
				$baseRowTotal = $baseRow + $totallala;
			}

			foreach ($value as $key2 => $value2) {
				$i = 0;
				$totalM9 = 0;
				$totalM11 = 0;
				$totalM13 = 0;
				$totalM15 = 0;
				$totalW9 = 0;
				$totalW11 = 0;
				$totalW13 = 0;
				$totalW15 = 0;
				foreach ($value2 as $key3 => $value3) {
					$cellnum = $baseRow + $i;
					$i++;

					$arrVlu = explode("/", $value3);
					if($key2 == 9){
						$objPHPExcel->getActiveSheet()
							->setCellValue("A".$cellnum, $arrVlu[0])
							->setCellValue("B".$cellnum, $arrVlu[1])
							->setCellValue("C".$cellnum, $arrVlu[2])
							->setCellValue("D".$cellnum, $arrVlu[3])
							->setCellValue("E".$cellnum, $arrVlu[4]);

							if($arrVlu[2] != ""){
								$totalM9 += $arrVlu[2];
							}
							if($arrVlu[3] != ""){
								$totalW9 += $arrVlu[3];
							}
					}else if($key2 == 11){
						$objPHPExcel->getActiveSheet()
							->setCellValue("F".$cellnum, $arrVlu[0])
							->setCellValue("G".$cellnum, $arrVlu[1])
							->setCellValue("H".$cellnum, $arrVlu[2])
							->setCellValue("I".$cellnum, $arrVlu[3])
							->setCellValue("J".$cellnum, $arrVlu[4]);

							if($arrVlu[2] != ""){
								$totalM11 += $arrVlu[2];
							}
							if($arrVlu[3] != ""){
								$totalW11 += $arrVlu[3];
							}
					}else if($key2 == 13){
						$objPHPExcel->getActiveSheet()
							->setCellValue("K".$cellnum, $arrVlu[0])
							->setCellValue("L".$cellnum, $arrVlu[1])
							->setCellValue("M".$cellnum, $arrVlu[2])
							->setCellValue("N".$cellnum, $arrVlu[3])
							->setCellValue("O".$cellnum, $arrVlu[4]);

							if($arrVlu[2] != ""){
								$totalM13 += $arrVlu[2];
							}
							if($arrVlu[3] != ""){
								$totalW13 += $arrVlu[3];
							}
					}else if($key2 == 15){
						$objPHPExcel->getActiveSheet()
							->setCellValue("P".$cellnum, $arrVlu[0])
							->setCellValue("Q".$cellnum, $arrVlu[1])
							->setCellValue("R".$cellnum, $arrVlu[2])
							->setCellValue("S".$cellnum, $arrVlu[3])
							->setCellValue("T".$cellnum, $arrVlu[4]);

							if($arrVlu[2] != ""){
								$totalM15 += $arrVlu[2];
							}
							if($arrVlu[3] != ""){
								$totalW15 += $arrVlu[3];
							}
					}
				}
				
				if($key2 == 9){
					$objPHPExcel->getActiveSheet()
						->setCellValue("C".($baseRowTotal), $totalM9)
						->setCellValue("D".($baseRowTotal), $totalW9)
						->setCellValue("E".($baseRowTotal), ($totalM9+$totalW9));
				}else if($key2 == 11){
					$objPHPExcel->getActiveSheet()
						->setCellValue("H".($baseRowTotal), $totalM11)
						->setCellValue("I".($baseRowTotal), $totalW11)
						->setCellValue("J".($baseRowTotal), ($totalM11+$totalW11));
				}else if($key2 == 13){
					$objPHPExcel->getActiveSheet()
						->setCellValue("M".($baseRowTotal), $totalM13)
						->setCellValue("N".($baseRowTotal), $totalW13)
						->setCellValue("O".($baseRowTotal), ($totalM13+$totalW13));
				}else if($key2 == 15){
					$objPHPExcel->getActiveSheet()
						->setCellValue("R".($baseRowTotal), $totalM15)
						->setCellValue("S".($baseRowTotal), $totalW15)
						->setCellValue("T".($baseRowTotal), ($totalM13+$totalW13));
				}
			}
		}
	}

	$objPHPExcel -> setActiveSheetIndex(0); //엑셀 sheet 선택
}

$filename = 'sol_'.$selDate;

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;
?>