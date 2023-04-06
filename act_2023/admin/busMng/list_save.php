<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/kakaoalim.php';
include __DIR__.'/../../common/func.php';

$success = true;
$datetime = date('Y/m/d H:i'); 

$param = $_REQUEST["resparam"];

$errmsg = "";
$intseq = "";
$intseq3 = "";
$to = "lud1@naver.com,ttenill@naver.com";

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

$bus_date = $_REQUEST["hidselDate"];
$resseq = $_REQUEST["resseq"]; //seq

if($param == "busMngdel"){
	$select_query = "DELETE FROM AT_PROD_BUS_DAY WHERE dayseq = $resseq";
	$result_set = mysqli_query($conn, $select_query);

	$errmsg = $select_query;
	if(!$result_set) goto errGo;

	mysqli_query($conn, "COMMIT");
}else if($param == "busMngadd"){
	$res_busgubun = $_REQUEST["res_busgubun"]; //버스번호
	$res_point = $_REQUEST["res_point"]; //노선
	$res_seat = $_REQUEST["res_seat"]; //좌석수
	$res_gpsname = $_REQUEST["res_gpsname"]; //GPS 이름
	$res_useYN = $_REQUEST["res_useYN"]; //사용여부

	$select_query = "DELETE FROM AT_PROD_BUS_DAY WHERE bus_date = '$bus_date'";
	$result_set = mysqli_query($conn, $select_query);

	$errmsg = $select_query;
	if(!$result_set) goto errGo;

	//서핑버스 정보등록
	for($i = 1; $i < count($resseq); $i++){
		$bus_date = $bus_date;
		$dayseq = $resseq[$i];
		$arrGubun = explode("|",$res_busgubun[$i]);
		$bus_gubun = $arrGubun[0];
		$bus_num = $arrGubun[1];
		$bus_name = $arrGubun[2];
		$code = $res_point[$i];
		$seat = $res_seat[$i];
		$gpsname = $res_gpsname[$i];
		$useYN = $res_useYN[$i];

		if($dayseq == ""){
			$select_query = "INSERT INTO `AT_PROD_BUS_DAY`(`bus_date`, `code`, `bus_gubun`, `bus_name`, `bus_num`, `gpsname`, `seat`, `useYN`) VALUES ('$bus_date', '$code', '$bus_gubun', '$bus_name', '$bus_num', '$gpsname', $seat, '$useYN')";
		}else{
			$select_query = "UPDATE `AT_PROD_BUS_DAY` SET 
				`bus_date`='$bus_date'
				,`code`='$code'
				,`bus_gubun`='$bus_gubun'
				,`bus_name`='$bus_name'
				,`bus_num`='$bus_num'
				,`gpsname`='$gpsname'
				,`seat`=$seat
				,`useYN`='$useYN'
			WHERE dayseq = $dayseq";
		}

		$result_set = mysqli_query($conn, $select_query);

		$errmsg = $select_query;
		if(!$result_set) goto errGo;
	}
		
	mysqli_query($conn, "COMMIT");
}

if(!$success){
	errGo:
	mysqli_query($conn, "ROLLBACK");
	echo "err|$errmsg";
}else{
	echo '0';
}

mysqli_close($conn);
?>
