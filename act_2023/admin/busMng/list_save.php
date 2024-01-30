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
$to = "lud1@naver.com";

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
}else if($param == "busMngCopy"){
	$copyDate = $_REQUEST["copyDate"];

	$select_query = "INSERT INTO AT_PROD_BUS_DAY 
						SELECT 0, '$copyDate', code, shopseq, bus_oper, bus_line, bus_gubun, bus_num, '', seat, price, useYN, channel  FROM `AT_PROD_BUS_DAY`  WHERE bus_date = '$bus_date'";
	$result_set = mysqli_query($conn, $select_query);

	$errmsg = $select_query;
	if(!$result_set) goto errGo;

	mysqli_query($conn, "COMMIT");
}else if($param == "busMngadd"){
	$res_busline = $_REQUEST["res_busline"]; //행선지
	$res_busgubun = $_REQUEST["res_busgubun"]; //노선
	$res_busnum = $_REQUEST["res_busnum"]; //호차
	$res_price = $_REQUEST["res_price"]; //가격
	$res_seat = $_REQUEST["res_seat"]; //좌석수
	$res_gpsname = $_REQUEST["res_gpsname"]; //GPS 이름
	$res_useYN = $_REQUEST["res_useYN"]; //사용여부
	$res_channel = $_REQUEST["res_channel"]; //예약형태

	//서핑버스 정보등록
	for($i = 1; $i < count($resseq); $i++){
		$dayseq = $resseq[$i];
		$shopseq = explode("|", $res_busline[$i])[1];  //shopseq
		$bus_line = explode("|", $res_busline[$i])[0];  //행선지
		$bus_gubun = $res_busgubun[$i]; //노선
		if($bus_gubun == "SA" || $bus_gubun == "JO"){
			$bus_oper = "start";
		}else{
			$bus_oper = "return";
		}

		$bus_num = $res_busnum[$i]; //호차
		$code = "";
		$price = $res_price[$i];
		$seat = $res_seat[$i];
		$gpsname = $res_gpsname[$i];
		$useYN = $res_useYN[$i];
		$channel = $res_channel[$i];

		if($dayseq == ""){
			$select_query = "INSERT INTO `AT_PROD_BUS_DAY`(`bus_date`, `code`, `shopseq`, `bus_oper`, `bus_line`, `bus_gubun`, `bus_num`, `gpsname`, `seat`, `price`, `useYN`, `channel`) VALUES ('$bus_date', '$code', $shopseq, '$bus_oper', '$bus_line', '$bus_gubun', '$bus_num', '$gpsname', $seat, $price, '$useYN', '$channel')";
		}else{
			$select_query = "UPDATE `AT_PROD_BUS_DAY` SET 
				 `code`='$code'
				,`shopseq`=$shopseq
				,`bus_oper`='$bus_oper'
				,`bus_line`='$bus_line'
				,`bus_gubun`='$bus_gubun'
				,`bus_num`='$bus_num'
				,`gpsname`='$gpsname'
				,`seat`=$seat
				,`price`=$price
				,`useYN`='$useYN'
				,`channel`='$channel'
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
