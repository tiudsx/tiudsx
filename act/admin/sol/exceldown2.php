<?php
include __DIR__.'/../../db.php';

$selDate = $_REQUEST["selDate"];

$arrDate = explode('-', $selDate);
$Year = $arrDate[0];
$Mon = $arrDate[1];
$Day = $arrDate[2];

//서핑강습
$select_query = "SELECT * FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                    WHERE b.resdate = '$selDate'
                        AND b.res_type = 'surf'
                        AND a.res_confirm = '확정'
                        ORDER BY  b.ressubseq, b.prod_name, b.restime";
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

$r = 0;
$surfcnt = array();
$rentcnt = 0;
$arrsurf = array();
$arrrent = array();
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
		if(array_key_exists($arrykey, $surfcnt[$prod_name])){
			$surfcnt[$prod_name][$arrykey]++;
		}else{
			$surfcnt[$prod_name][$arrykey] = 0;
        }
        
    	echo $surfcnt[$prod_name][$arrykey].":"."$prod_name/$restime/$user_name/$user_tel/".(($surfM == 0) ? "" : $surfM)."/".(($surfW == 0) ? "" : $surfW)."/$memoYN"."<Br>";
		$arrsurf[$prod_name][$arrykey][$surfcnt[$prod_name][$arrykey]] = "$user_name/$user_tel/".(($surfM == 0) ? "" : $surfM)."/".(($surfW == 0) ? "" : $surfW)."/$memoYN";
    }

    if($surfrent != "N"){
		$arrrent[$rentcnt] = "$user_name/$user_tel/$surfrent/".(($surfM == 0) ? "" : $surfM)."/".(($surfW == 0) ? "" : $surfW)."/$memoYN";
		$rentcnt++;
    }
//while end
}

//렌탈 예약
echo "렌트<br>";
if(count($arrrent)){
    echo "<Br>".count($arrrent);
    foreach ($arrrent as $key => $value) {
        echo "<Br>$key/$value";
    }
}
echo "<br>강습<br>";
//강습 예약
if(count($arrsurf)){
    foreach ($arrsurf as $key => $value) {
        ksort($value); //key값으로 재정렬
        echo "<Br>$key/$value";
        foreach ($value as $key2 => $value2) {
            echo "<Br>".count($value2).":$key2/$value2";
            foreach ($value2 as $key3 => $value3) {
                echo "<Br>$key3/$value3";
            }
        }
    }
}

$surfsolcnt = 0;
$surfspcnt = 0;
$surflalacnt = 0;
$surfrangcnt = 0;
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

echo "<br>솔:".$surfsolcnt;
echo "<br>서프팩토리:".$surfspcnt;
echo "<br>라라서프:".$surflalacnt;
echo "<br>서퍼랑:".$surfrangcnt;
echo "<br><br><br>";
foreach ($arrsurf as $key => $value) {
    if($key == "솔게스트하우스"){
        $baseRow = $surfsolcnt;
    }else if($key == "서프팩토리"){
        $baseRow = $surfspcnt;
    }else if($key == "라라서프"){
        $baseRow = $surflalacnt;
    }else if($key == "서퍼랑"){
        $baseRow = $surfrangcnt;
    }

    echo "<br>".$baseRow;
    foreach ($value as $key2 => $value2) {
        $i = 0;
        foreach ($value2 as $key3 => $value3) {
            $cellnum = $baseRow + $i;
            $i++;

            $arrVlu = explode("/", $value3);
            echo "<br>$key : $key2 : ".$value3;
            if($key2 == 9){
                
            }else if($key2 == 11){
                
            }else if($key2 == 13){
                
            }else if($key2 == 15){
                
            }
        }
    }
}
?>