<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$selDate = $_REQUEST["selDate"];
$busNum = $_REQUEST["busNum"];
?>

<?
$select_query_sub = "SELECT a.user_name, a.user_tel, a.etc, a.resseq, b.res_seat, b.res_spointname FROM 
						`AT_RES_MAIN` as a INNER JOIN `AT_RES_SUB` as b 
							ON a.resnum = b.resnum 
                                AND b.code = 'bus'
						where b.res_date = '$selDate' AND b.res_busnum = '$busNum' AND b.res_confirm  = 3";
$result_setlist = mysqli_query($conn, $select_query_sub);
$count_sub = mysqli_num_rows($result_setlist);

if($count_sub == 0){
	echo '<div style="text-align:center;font-size:14px;padding:50px;" id="initText2">
				<b>예약된 좌석이 없습니다.</b>
			</div>';
	return;
}

echo '<script type="text/javascript">$j(document).ready(function(){';

while ($row = mysqli_fetch_assoc($result_setlist)){
	echo '$j("#seat'.$row['res_seat'].'").removeClass("tab1");';

	echo '$j("#seat'.$row['res_seat'].'").attr("onclick", "fnBusModify('.$row['resseq'].')");';
	echo '$j("#seat'.$row['res_seat'].'").addClass("tab2");';
	echo '$j("#seat'.$row['res_seat'].' input").remove();';

	echo '$j("#seat'.$row['res_seat'].'").parent().append(" <span  '.$cssColor.'></span> <br><b>'.$row["user_name"].'</b> (<span><a href=tel:'.$row["user_tel"].' style=cursor:text;>'.$row["user_tel"].'</a>)<br>['.$row["res_spointname"].']</span>");';
}

echo '});</script>';


$busGubun = substr($busNum, 0, 1);
$busPoint = substr($busNum, 1, 2);
$busNumber = substr($busNum, 1, 3);

$arrSeatInfo1 = array();
$arrSeatInfo2 = array();

//양양, 동해행 코드 : YSa1, YJo1, SY21, SY51, AE21, AE51
if($busPoint == "Sa" || $busPoint == "Jo"){ //사당선, 종로선
	$busType = $busPoint;
	$busEType = $busGubun."end";
}else if($busPoint == "Y2" || $busPoint == "Y5"){ //서울행 양양
	$busType = "SY";
	$busEType = "Send";
}else if($busPoint == "E2" || $busPoint == "E5"){ //서울행 동해
	$busType = "AE";
	$busEType = "Send";
}

$select_query_sub3 = "SELECT COUNT(*) AS Cnt, b.res_spoint, c.ordernum FROM `AT_RES_SUB` as b INNER JOIN AT_CODE as c 
						ON b.code = 'bus' 
							AND b.res_spoint = c.codename
							AND c.code = '$busType'
						WHERE b.res_Date = '$selDate' AND b.res_bus = '$busNum' AND b.res_confirm = 3
						GROUP BY b.res_spoint ORDER BY c.ordernum";
//  echo $select_query_sub3;
$resultSite3 = mysqli_query($conn, $select_query_sub3);
$i = 0;
while ($row = mysqli_fetch_assoc($resultSite3)){
	if(array_key_exists($row['ordernum'].'_'.$row['res_spoint'], $arrSeatInfo1)){
		$arrSeatInfo1[$row['ordernum'].'_'.$row['res_spoint']] += $row['Cnt'];
	}else{
		$arrSeatInfo1[$row['ordernum'].'_'.$row['res_spoint']] = $row['Cnt'];
	}
	$i += $row['Cnt'];
}
$select_query_sub3 = "SELECT COUNT(*) AS Cnt, b.res_epoint, c.ordernum FROM `AT_RES_SUB` as b INNER JOIN AT_CODE as c 
						ON b.code = 'bus' 
							AND b.res_epoint = c.codename
							AND c.code = '$busEType'
						WHERE b.res_Date = '$selDate' AND b.res_bus = '$busNum' AND b.res_confirm = 3
						GROUP BY b.res_epoint ORDER BY c.ordernum";
// echo $select_query_sub3;
$resultSite3 = mysqli_query($conn, $select_query_sub3);
while ($row = mysqli_fetch_assoc($resultSite3)){
	if(array_key_exists($row['ordernum'].'_'.$row['res_epoint'], $arrSeatInfo2)){
		$arrSeatInfo2[$row['ordernum'].'_'.$row['res_epoint']] += $row['Cnt'];
	}else{
		$arrSeatInfo2[$row['ordernum'].'_'.$row['res_epoint']] = $row['Cnt'];
	}
}
?>
	<div style="padding-bottom:5px;"></div>

	<input type="button" class="bd_btn" style="padding-top:4px;font-family: gulim,Tahoma,Arial,Sans-serif;margin-bottom:2px;" value="<?=fnBusNum($busNum)?> [총 : <?=$i?>명]" />
    <table class="et_vars exForm bd_tb" width="100%">
        <tbody>
			 <tr>
				<td style="vertical-align:top;">
					<table width="100%">
						<colgroup>
							<col style="width:21%;">
							<col style="width:12%;">
							<col style="width:auto;">
							<col style="width:12%;">
						</colgroup>
						<tr>
							<th style="padding:4px;text-align:center;" colspan="4">출발 정류장</th>
						</tr>
						<tr>
							<th style="padding:4px;text-align:center;">정류장</th>
							<th style="padding:4px;text-align:center;">시간</th>
							<th style="padding:4px;text-align:center;">탑승위치</th>
							<th style="padding:4px;text-align:center;">인원</th>
						</tr>
					<?
					foreach($arrSeatInfo1 as $key=>$value) {
						$arrData = explode("_",$key);
						$pointname = explode("|", fnBusPoint($arrData[1], $busNum));
					?>
						<tr>
							<td style="padding:4px;text-align:left;">&nbsp;<?=$key?>&nbsp;&nbsp;<b>(<?=$value?> 명)</b></td>
							<td style="padding:4px;text-align:center;"><?=$pointname[0]?></td>
							<td style="padding:4px;text-align:left;">&nbsp;<?=$pointname[1]?></td>
							<td style="padding:4px;text-align:center;"><?=$value?> 명</td>
						</tr>
					<?}?>
					</table>
					
					<table width="100%" style="margin-top:10px;">
						<colgroup>
							<col style="width:21%;">
							<col style="width:auto;">
						</colgroup>
						<tr>
							<th style="padding:4px;text-align:center;" colspan="2">도착 정류장</th>
						</tr>
						<?foreach($arrSeatInfo2 as $key=>$value) {
							$arrData = explode("_",$key);
							$pointname = explode("|", fnBusPoint($arrData[1], $busEType));
						?>
						<tr>
						<td style="padding:4px;text-align:left;">&nbsp;<?=$key?>&nbsp;&nbsp;<b>(<?=$value?> 명)</b></td>
							<td><b><?=$pointname[1]?></b></td>
						</tr>
						<?}?>
					</table>

				</td>
				
			</tr>
		</tbody>
	</table>

	<div style="padding-bottom:5px;"></div>

<?
$select_query = "SELECT seat FROM `AT_PROD_BUS_DAY` WHERE useYN = 'Y' AND bus_gubun = '$busGubun' AND bus_num = '$busNumber' AND bus_date = '$selDate'";
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

		if($i == 10){
			if($rowMain["seat"] == 44){
	?>
			<tr height="68">
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num1?>" class="tab1"><span style="width:45px;"><label><?=$num1?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num2?>" class="tab1"><span style="width:45px;"><label><?=$num2?></label></span></span></td>
				<td class="col-3">&nbsp;</td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num3?>" class="tab1"><span style="width:45px;"><label><?=$num3?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num4?>" class="tab1"><span style="width:45px;"><label><?=$num4?></label></span></span></td>
			</tr>
	<?
			}else{
	?>
			<tr height="68">
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num1?>" class="tab1"><span style="width:45px;"><label><?=$num1?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num2?>" class="tab1"><span style="width:45px;"><label><?=$num2?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num3?>" class="tab1"><span style="width:45px;"><label><?=$num3?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num4?>" class="tab1"><span style="width:45px;"><label><?=$num4?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num5?>" class="tab1"><span style="width:45px;"><label><?=$num5?></label></span></span></td>
			</tr>
	<?
			}
		}else{
	?>
			<tr height="68">
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num1?>" class="tab1"><span style="width:45px;"><label><?=$num1?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num2?>" class="tab1"><span style="width:45px;"><label><?=$num2?></label></span></span></td>
				<td>&nbsp;</td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num3?>" class="tab1"><span style="width:45px;"><label><?=$num3?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num4?>" class="tab1"><span style="width:45px;"><label><?=$num4?></label></span></span></td>
			</tr>
	<?
		}
	}
	?>

		</tbody>
	</table>
</form>