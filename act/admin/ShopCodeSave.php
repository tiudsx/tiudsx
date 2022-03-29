<?php
include __DIR__.'/../db.php';


$success = true;
$insuserid = "admin";
$datetime = date('Y/m/d H:i'); 

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");
/* 입점샵 은행 계좌 정보 */
$full_bankname = "농협"; //은행
$full_banknum = "351-1080-7853-93"; //계좌번호
$full_bankuser = "정광영"; //예금주
$bankname = "농협"; //은행
$banknum = "351-1080-7853-93"; //계좌번호
$bankuser = "정광영"; //예금주

$select_query = "INSERT INTO `AT_PROD_BANKNUM`(`shopname`, `full_bankname`, `full_banknum`, `full_bankuser`, `bankname`, `banknum`, `bankuser`, `etc`) VALUES ('test', '$full_bankname', '$full_banknum', '$full_bankuser', '$bankname', '$banknum', '$bankuser', '');";
$result_set = mysqli_query($conn, $select_query);

//$rs_identity = $conn->fetchAll("select LAST_INSERT_ID() as identity");
$result = mysqli_query($conn, "select LAST_INSERT_ID() as identity");
$rowMain = mysqli_fetch_array($result);

echo '결과 : '.$rowMain["identity"];

if(!$success){
	mysqli_query($conn, "ROLLBACK");

	echo '<script>alert("예약진행 중 오류가 발생하였습니다.");</script>';
}else{
	mysqli_query($conn, "COMMIT");

	echo '<script>alert("완료되었습니다.");</script>';
}
?>