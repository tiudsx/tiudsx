<?php
	include __DIR__.'/../../common/db.php';
	include __DIR__.'/../../common/func.php';

	/*

	//임시 테이블정보
	SELECT userinfo, use_yn, couponseq FROM `AT_COUPON_CODE` where userinfo is not null and couponseq = 16 AND userinfo like '%테스트%'
	*/

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
		,bustypevalue	//버스상품타입 Y:양양행,S:동해행
		,etc1:""		//임시데이터 유무
		,etc2:""		//확정데이터 유무
		,etc3:""		//처리
	}

	*/

	header('Content-Type: application/json');

	$rev_data = $_POST['data'];
	
	$i = 0;
	foreach ($rev_data as $row) {
        
		$data1 = $rev_data[$i]['usertel'];
		$data2 = $rev_data[$i]['bus_date'];
		$data3 = $rev_data[$i]['bustypevalue'];

		$select_query = "SELECT 
				A.resnum,
				A.user_id,
				A.user_name,
				A.user_tel,
				B.res_date,
				B.res_bus
			FROM AT_RES_MAIN A
				INNER JOIN AT_RES_SUB B
					ON A.resnum = B.resnum
		WHERE REPLACE(A.user_tel,'-','') = '$data1'
		AND B.res_date = '$data2'
		AND SUBSTRING(B.res_bus,1,1) = '$data3'";

		$result = mysqli_query($conn, $select_query);
		$count_sub = mysqli_num_rows($result);

		//while ( $row = $result->fetch_assoc()){}

		if ($count_sub == $rev_data[$i]["resbusseat2"]) {
			$rev_data[$i]["etc2"] = "O";
		}
		else {
			$rev_data[$i]["etc2"] = "";
		}

		//$rev_data[$i]["etc1"] = $rev_data[$i]["bustypevalue"];
		//$rev_data[$i]["etc1"] = $count_sub;
		//$rev_data[$i]["etc3"] = $data1.'/'.$data2.'/'.$data3.'/'.$rev_data[$i]["resbusseat2"];
		$i++;

    }

	echo(json_encode($rev_data));

	// $output = json_encode($dbdata, JSON_UNESCAPED_UNICODE);
    // echo urldecode($output);

?>
