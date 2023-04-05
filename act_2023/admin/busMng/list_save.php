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

if($param == "soldel"){
	$resseq = $_REQUEST["resseq"];

	//예약 메인데이터 삭제
	$select_query = "DELETE FROM AT_SOL_RES_MAIN WHERE resseq = $resseq";
	$result_set = mysqli_query($conn, $select_query);

	$errmsg = $select_query;
	if(!$result_set) goto errGo;

	//예약 상세데이터 삭제
	$select_query = "DELETE FROM AT_SOL_RES_SUB WHERE resseq = $resseq";
	$result_set = mysqli_query($conn, $select_query);

	$errmsg = $select_query;
	if(!$result_set) goto errGo;

	mysqli_query($conn, "COMMIT");
}else if($param == "busMngadd"){
	$resseq = $_REQUEST["resseq"];
	$hidselDate = $_REQUEST["hidselDate"];

	$res_busgubun = $_REQUEST["res_busgubun"]; //버스번호
	$res_point = $_REQUEST["res_point"]; //노선
	$res_seat = $_REQUEST["res_seat"]; //좌석수
	$res_gpsname = $_REQUEST["res_gpsname"]; //GPS 이름
	$res_useYN = $_REQUEST["res_useYN"]; //사용여부

	if($resseq == ""){
		//메인 정보 등록
		$select_query = "INSERT INTO `AT_SOL_RES_MAIN`(`resnum`, `admin_user`, `res_confirm`, `res_kakao`, `res_kakao_chk`, `res_room_chk`, `res_company`, `user_name`, `user_tel`, `memo`, `memo2`, `history`, `insdate`) VALUES ('$ResNumber', '$res_adminname', '$res_confirm', 0, 'N', 'N', '$res_company', '$user_name', '$user_tel', '$memo', '$memo2', '', now())";
		$result_set = mysqli_query($conn, $select_query);
		$seq = mysqli_insert_id($conn);

		$errmsg = $select_query;
		if(!$result_set) goto errGo;
	}else{
		//메인 정보 수정
		$select_query = "UPDATE `AT_SOL_RES_MAIN` SET 
			`admin_user`='$res_adminname'
			,`res_confirm`='$res_confirm'
			,`res_company`='$res_company'
			,`user_name`='$user_name'
			,`user_tel`='$user_tel'
			,`memo`='$memo'
			,`memo2`='$memo2'
			,`history`= CONCAT(history,'$res_adminname:".date("Y-m-d A h:i:s")."@')
		WHERE resseq = $resseq";
		$result_set = mysqli_query($conn, $select_query);

		$errmsg = $select_query;
		if(!$result_set) goto errGo;

		$select_query = "DELETE FROM AT_SOL_RES_SUB WHERE resseq = $resseq";
		$result_set = mysqli_query($conn, $select_query);

		$errmsg = $select_query;
		if(!$result_set) goto errGo;

		$seq = $resseq;
	}

	//서핑버스 정보등록
	for($i = 1; $i < count($res_busgubun); $i++){
		$prod_name = $res_stayshop[$i];
		$sdate = null;
		$edate = null;
		$resdate = null;
		$staysex = $res_staysex[$i];
		$stayroom = null;
		$staynum = null;
		$bbq = $res_bbq[$i];
		$stayM = $res_stayM[$i];

		$select_query = "INSERT INTO `AT_SOL_RES_SUB`(`resseq`, `res_type`, `prod_name`, `sdate`, `edate`, `resdate`, `staysex`, `stayroom`, `staynum`, `bbq`, `stayM`) VALUES ($seq, 'stay', '$prod_name', '$sdate', '$edate', '$resdate', '$staysex', '$stayroom', '$staynum', '$bbq', $stayM)";
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
}else if(!$success){
	errGoRoom:
	mysqli_query($conn, "ROLLBACK");
	echo "errRoom|$stayroom|$staynum";
}else{
	echo '0';
}

mysqli_close($conn);
?>
