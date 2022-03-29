<?php
include __DIR__.'/../db.php';

/*
GPS 오프 : OFF
GPS 미수신 : UNKNOWN
GPS 수신 : ON
*/
$success = true;
$datetime = date('Y-m-d'); 

$lat = trim($_REQUEST["lat"]);
$lng = trim($_REQUEST["lng"]);
$user_name = trim(urldecode($_REQUEST["username"]));
$weeknum = trim($_REQUEST["weeknum"]);
$timenum = trim($_REQUEST["timenum"]);
$stats = trim($_REQUEST["stats"]);
$timestart = trim($_REQUEST["timestart"]);
$timeend = trim($_REQUEST["timeend"]);

 if($stats != "OFF"){
	mysqli_query($conn, "SET AUTOCOMMIT=0");
	mysqli_query($conn, "BEGIN");

	// 셔틀버스 위치 정보 입력
	$select_query = "INSERT INTO AT_PROD_BUS_GPS(`lat`, `lng`, `user_name`, `weeknum`, `timenum`, `insdate`, `stats`, `timestart`, `timeend`) VALUES ('$lat', '$lng', '$user_name', $weeknum, $timenum, now(), '$stats', $timestart, $timeend)";
	$result_set = mysqli_query($conn, $select_query);
	$seq = mysqli_insert_id($conn);
	if(!$result_set) goto errGo;

	// 셔틀버스 데이터 삭제 : 2일전
	$select_query = "DELETE FROM AT_PROD_BUS_GPS WHERE TIMESTAMPDIFF(DAY, insdate, now()) > 2";
	$result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;

	// 셔틀버스 마지막정보 제외 삭제
	$select_query = "DELETE FROM AT_PROD_BUS_GPS_LAST WHERE user_name = '$user_name'";
	$result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;

	// 셔틀버스 최종 정보 입력
	$select_query = "INSERT INTO AT_PROD_BUS_GPS_LAST(`lat`, `lng`, `user_name`, `weeknum`, `timenum`, `insdate`, `stats`, `timestart`, `timeend`, `gpsdate`) VALUES ('$lat', '$lng', '$user_name', $weeknum, $timenum, now(), '$stats', $timestart, $timeend, '$datetime')";
	$result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;


	if(!$result_set){
		errGo:
		mysqli_query($conn, "ROLLBACK");
		echo 'err';
	}else{
		mysqli_query($conn, "COMMIT");
		echo '0';
	}
 }
?>