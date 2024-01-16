<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$selDate = $_REQUEST["selDate"];
$shopseq = $_REQUEST["seq"];
$bus_gubun = $_REQUEST["bus_gubun"];
$bus_num = $_REQUEST["bus_num"];
?>

<?
$select_query_sub = "SELECT a.user_name, a.user_tel, a.etc, a.resseq, b.res_seat, b.res_spointname, b.res_epointname FROM 
						`AT_RES_MAIN` AS a INNER JOIN `AT_RES_SUB` AS b 
							ON a.resnum = b.resnum 
                                AND b.code = 'bus'
						WHERE b.seq = $shopseq
							AND b.res_date = '$selDate' 
							AND b.bus_gubun = '$bus_gubun' 
							AND b.bus_num = '$bus_num' 
							AND b.res_confirm = 3
						ORDER BY a.user_tel";
$result_setlist = mysqli_query($conn, $select_query_sub);
$count_sub = mysqli_num_rows($result_setlist);

if($count_sub == 0){
	echo '<div style="text-align:center;font-size:14px;padding:50px;" id="initText2">
				<b>예약된 좌석이 없습니다.</b>
			</div>';
	return;
}

$arrUserInfo = array();
$arrDB_Start = array();
$arrDB_End = array();
while ($row = mysqli_fetch_assoc($result_setlist)){
	$arrUserInfo[$row['res_seat']] = array($row["user_name"], $row["user_tel"], $row["res_spointname"], $row["resseq"]);

	if(array_key_exists($row['res_spointname'], $arrDB_Start)){ //출발 정류장
		$arrDB_Start[$row['res_spointname']] += 1;
	}else{
		$arrDB_Start[$row['res_spointname']] = 1;
	}

	if(array_key_exists($row['res_epointname'], $arrDB_End)){ //도착 정류장
		$arrDB_End[$row['res_epointname']] += 1;
	}else{
		$arrDB_End[$row['res_epointname']] = 1;
	}
}

// echo '<script type="text/javascript">$j(document).ready(function(){';

// while ($row = mysqli_fetch_assoc($result_setlist)){
// 	echo '$j("#seat'.$row['res_seat'].'").removeClass("tab1");';

// 	echo '$j("#seat'.$row['res_seat'].'").attr("onclick", "fnBusModify('.$row['resseq'].')");';
// 	echo '$j("#seat'.$row['res_seat'].'").addClass("tab2");';
// 	echo '$j("#seat'.$row['res_seat'].' input").remove();';

// 	echo '$j("#seat'.$row['res_seat'].'").parent().append(" <span  '.$cssColor.'></span> <br><b>'.$row["user_name"].'</b> (<span><a href=tel:'.$row["user_tel"].' style=cursor:text;>'.$row["user_tel"].'</a>)<br>['.$row["res_spointname"].']</span>");';
// }

// echo '});</script>';

$arrBus_Start = array();
$arrBus_End = array();
$busDataJson = fnBusPoint2023("", "", $shopseq);

foreach($busDataJson as $key => $value){
	$key_data = explode("_", $key);

	if($bus_gubun == $key_data[0]){
		$arrBus_Start[$key_data[1]] = 0;	
	}
	
	if($bus_gubun == "SA" || $bus_gubun == "JO"){ //서울 출발
		if("Send" == $key_data[0]){
			$arrBus_End[$key_data[1]] = 0;
		}
	}else if($bus_gubun == "AM" || $bus_gubun == "PM"){ //서울 복귀
		if("Eend" == $key_data[0]){
			$arrBus_End[$key_data[1]] = 0;
		}
	}
}
//unset($arrBus_Start[1]);

?>
	<div style="padding-bottom:5px;"></div>

    <table class="et_vars exForm bd_tb" width="100%">
        <tbody>
			 <tr>
				<td style="vertical-align:top;">
					<table width="100%">
						<colgroup>
							<col style="width:21%;">
							<col style="width:12%;">
							<col style="width:auto;">
						</colgroup>
						<tr>
							<th style="padding:4px;text-align:center;" colspan="3">출발 정류장</th>
						</tr>
						<tr>
							<th style="padding:4px;text-align:center;">탑승 정류장</th>
							<th style="padding:4px;text-align:center;">시간</th>
							<th style="padding:4px;text-align:center;">탑승위치</th>
						</tr>
					<?
					foreach($arrBus_Start as $key=>$value) {
						if($arrDB_Start[$key] == null){
							continue;
						}

						$arrData = explode("|", $busDataJson[$bus_gubun."_".$key]);
						$mem = $arrDB_Start[$key];
					?>
						<tr>
							<td style="padding:4px;text-align:left;">&nbsp;<?=$key?>&nbsp;&nbsp;<b>(<?=$mem?> 명)</b></td>
							<td style="padding:4px;text-align:center;"><?=$arrData[0]?></td>
							<td style="padding:4px;text-align:left;">&nbsp;<?=$arrData[1]?></td>
						</tr>
					<?}?>
					</table>
					
					<table width="100%" style="margin-top:10px;">
						<colgroup>
							<col style="width:21%;">
							<col style="width:auto;">
						</colgroup>
						<tr>
							<th style="padding:4px;text-align:center;" colspan="2">하차 정류장</th>
						</tr>
						<?
						foreach($arrBus_End as $key=>$value) {
							if($arrDB_End[$key] == null){
								continue;
							}
							$mem = $arrDB_End[$key];
						?>
						<tr>
						<td style="padding:4px;text-align:left;">&nbsp;<?=$key?>&nbsp;&nbsp;<b>(<?=$mem?> 명)</b></td>
							<td><b></b></td>
						</tr>
						<?}?>
					</table>

				</td>
				
			</tr>
		</tbody>
	</table>

	<div style="padding-bottom:5px;"></div>

<?
$select_query = "SELECT seat FROM `AT_PROD_BUS_DAY` WHERE useYN = 'Y' AND bus_gubun = '$bus_gubun' AND bus_num = '$bus_num' AND bus_date = '$selDate'";
$result = mysqli_query($conn, $select_query);
$rowMain = mysqli_fetch_array($result);
?>
<form name="frmDayConfirm" id="frmDayConfirm" autocomplete="off">
    <div class="gg_first">셔틀버스 예약정보</div>

    <table class="et_vars exForm bd_tb" style="margin-bottom:5px;width:100%;">
		<colgroup>
			<col style="width:20%;">
			<col style="width:20%;">
			<col style="width:20%;">
			<col style="width:20%;">
			<col style="width:20%;">
		</colgroup>
		<tbody>
	<?
	for($i=0; $i<=10; $i++){
		$num1 = ($i * 4) + 1;
		$num2 = ($i * 4) + 2;
		$num3 = ($i * 4) + 3;
		$num4 = ($i * 4) + 4;
		$num5 = ($i * 4) + 5;

		$arrInfo = array();
		$arrClass = array();
		$arrClick = array();
		for ($x=0; $x < 5; $x++) { 
			$for_i = ($i * 4) + ($x + 1);

			$user_name = $arrUserInfo[$for_i][0]; //예약자명
			$user_tel = $arrUserInfo[$for_i][1]; //예약자 연락처
			$user_point = $arrUserInfo[$for_i][2]; //탑승 정류장
			$res_seq = $arrUserInfo[$for_i][3]; //예약번호

			$class = "tab1";
			$onclick = "";
			$user_text = "";
			if($res_seq != ""){
				$class = "tab2";
				$onclick = "onclick='fnBusModify($res_seq);'";
				$user_text = "<br><b>$user_name</b> <span>(<a href='tel:$user_tel' style='cursor:text;'>$user_tel</a>)<br>[$user_point]</span>";
			}
			
			$arrInfo[$for_i] = $user_text;
			$arrClass[$for_i] = $class;
			$arrClick[$for_i] = $onclick;

			if($x == 0) echo '<tr height="68">';
			if($rowMain["seat"] == 44 || ($i < 10 && $rowMain["seat"] == 45)){

				if($x == 2) echo '<td>&nbsp;</td>';

				if($x < 4){
		?>
				<td class="col-3" style="text-align:center;vertical-align:top;">
					<span id="seat<?=$for_i?>" class="<?=$arrClass[$for_i]?>" <?=$arrClick[$for_i]?>>
						<span style="width:45px;"><label><input type="checkbox"> <?=$for_i?></label></span>
					</span>
					<?=$arrInfo[$for_i]?>
				</td>
		<?
				}
			}else{
		?>
				<td class="col-3" style="text-align:center;vertical-align:top;">
					<span id="seat<?=$for_i?>" class="<?=$arrClass[$for_i]?>" <?=$arrClick[$for_i]?>>
						<span style="width:45px;"><label><?=$for_i?></label></span>
					</span>
					<?=$arrInfo[$for_i]?>
				</td>
		<?
			}
			if($x == 4) echo '</tr>';
		}
	}
	?>

		</tbody>
	</table>
</form>