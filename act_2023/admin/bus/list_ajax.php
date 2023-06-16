<?php
	include __DIR__.'/../../common/db.php';
	include __DIR__.'/../../common/func.php';

	/*

	//임시 테이블정보
	SELECT userinfo, use_yn, couponseq FROM `AT_COUPON_CODE` where userinfo is not null and couponseq = 16 AND userinfo like '%테스트%'


	// 확정 테이블정보
	SELECT 
		* 
	FROM AT_RES_MAIN A
		INNER JOIN AT_RES_SUB B
			ON A.resnum = B.resnum

	$select_query = "SELECT userinfo, use_yn, couponseq FROM `AT_COUPON_CODE` where userinfo is not null and couponseq = 16 AND userinfo like '%테스트%'";

	$result = mysqli_query($conn, $select_query);
	$count_sub = mysqli_num_rows($result);

	if($count_sub == 0){
		echo "err";
	}else{
		
		$dbdata;
        while ( $row = $result->fetch_assoc()){
            $dbdata = $row;
        }

	}

	*/	

	$rev_data = $_POST['data'];
	//header('Content-Type: application/json');

	/*
	{
		"res_id":"UKS708485"
		,"state":"대기중"
		,"rgb":"rgb(255, 255, 255)"
		,"prod_pkg":"서울 사당 - 양양 (편도/ 토요일, 일요일) "
		,"ea":"인원 x 4"
		,"bus_date":"2023-06-24"
		,"user_fullname":"정 숙영"
		,"user_tel":"***"

		,username		//고객명
		,usertel		//고객 연락처
		,usedate		//사용일
		,bustypetext	//버스상품명
		,bustypevalue	//버스상품타입 1:출발,2:복귀
		,etc1:""		//임시데이터 유무
		,etc2:""		//확정데이터 유무
		,etc3:""		//처리
	}
	

	노선
	이름
	연락처
	이용일(인원)

	임시
	확정
	처리

	*/
	
	foreach ($rev_data as $row) {
        //print $row['키값'];
    }

	echo(json_encode($rev_data));

?>
