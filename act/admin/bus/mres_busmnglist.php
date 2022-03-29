<?php
include __DIR__.'/../../db.php';
include __DIR__.'/../../surf/surffunc.php';

$selDate = $_REQUEST["selDate"];
$busNum = $_REQUEST["busNum"];

$select_query_sub = "SELECT a.user_name, a.user_tel, a.etc, b.* FROM 
						`AT_RES_MAIN` as a INNER JOIN `AT_RES_SUB` as b 
							ON a.resnum = b.resnum 
                                AND b.code = 'bus'
						where b.res_date = '$selDate' AND b.res_busnum = '$busNum' AND b.res_confirm  = 3";
// echo $select_query_sub;
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

	echo '$j("#seat'.$row['res_seat'].'").attr("onclick", "fnModifyInfo(\'bus\','.$row['ressubseq'].', 2)");';
	echo '$j("#seat'.$row['res_seat'].'").addClass("tab2");';
	echo '$j("#seat'.$row['res_seat'].' input").remove();';

	echo '$j("#seat'.$row['res_seat'].'").parent().append(" <span  '.$cssColor.'></span> <br><b>'.$row["user_name"].'</b><br><span><a href=tel:'.$row["user_tel"].' style=cursor:text;>'.$row["user_tel"].'</a><br>['.$row["res_spointname"].']</span>");';
}

echo '});</script>';


$busGubun = substr($busNum, 0, 1);
$busNumber = substr($busNum, 1);

$arrSeatInfo1 = array();
$arrSeatInfo2 = array();

if($busGubun == "Y"){
	$busGubunName1 = $busNum;
	$busGubunName2 = $busGubun;
}else{
	$busGubunName1 = $busGubun;
	$busGubunName2 = $busNum;
}

//양양, 동해행 코드
if($busNum == "Y1" || $busNum == "Y3" || $busNum == "Y5"){
	$busType = "yy1";
	$busEType = "yy9";
	$busEndPoint = "S21";
}else if($busNum == "Y2" || $busNum == "Y4" || $busNum == "Y6"){
	$busType = "yy2";
	$busEType = "yy9";
	$busEndPoint = "S21";
}else if($busNum == "E1" || $busNum == "E2" || $busNum == "E3"){
	$busType = "ea1";
	$busEType = "ea9";
	$busEndPoint = "A21";
}

//양양, 동해 서울행 코드
if($busGubun == "S"){
	$busType = "yyS";
	$busEType = "yyE";
}else if($busGubun == "A"){
	$busType = "eaS";
	$busEType = "eaE";
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
	if(array_key_exists($row['ordernum'].'.'.$row['res_spoint'], $arrSeatInfo1)){
		$arrSeatInfo1[$row['ordernum'].'.'.$row['res_spoint']] += $row['Cnt'];
	}else{
		$arrSeatInfo1[$row['ordernum'].'.'.$row['res_spoint']] = $row['Cnt'];
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
	if(array_key_exists($row['ordernum'].'.'.$row['res_epoint'], $arrSeatInfo2)){
		$arrSeatInfo2[$row['ordernum'].'.'.$row['res_epoint']] += $row['Cnt'];
	}else{
		$arrSeatInfo2[$row['ordernum'].'.'.$row['res_epoint']] = $row['Cnt'];
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
							<col style="width:33%;">
							<col style="width:12%;">
							<col style="width:*;">
						</colgroup>
						<tr>
							<th style="padding:4px;text-align:center;" colspan="4">출발 정류장</th>
						</tr>
						<tr>
							<th style="padding:4px;text-align:center;">정류장</th>
							<th style="padding:4px;text-align:center;">시간</th>
							<th style="padding:4px;text-align:center;">탑승위치</th>
						</tr>
					<?
					foreach($arrSeatInfo1 as $key=>$value) {
						$arrData = explode(".",$key);
						$pointname = explode("|", fnBusPoint($arrData[1], $busNum));
					?>
						<tr>
							<td style="padding:4px;text-align:left;">&nbsp;<?=$key?>&nbsp;&nbsp;<b><br>(<?=$value?> 명)</b></td>
							<td style="padding:4px;text-align:center;"><?=$pointname[0]?></td>
							<td style="padding:4px;text-align:left;">&nbsp;<?=$pointname[1]?></td>
						</tr>
					<?}?>
					</table>
					
					<table width="100%" style="margin-top:10px;">
						<colgroup>
							<col style="width:35%;">
							<col style="width:*;">
						</colgroup>
						<tr>
							<th style="padding:4px;text-align:center;" colspan="2">도착 정류장</th>
						</tr>
						<?foreach($arrSeatInfo2 as $key=>$value) {
							$arrData = explode(".",$key);
							$pointname = explode("|", fnBusPoint($arrData[1], $busEndPoint));
						?>
						<tr>
						<td style="padding:4px;text-align:left;">&nbsp;<?=$key?> <b>(<?=$value?> 명)</b></td>
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
//echo $busGubun.'/'.$busNumber;
$select_query = "SELECT * FROM `AT_PROD_BUS` WHERE use_yn = 'Y' AND busgubun = '$busGubun' AND busnum = '$busNumber' AND busdate = '$selDate'";
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
			if($rowMain["busseat"] == 44){
	?>
			<tr height="68">
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num1?>" class="tab1"><span style="width:35px;"><label><?=$num1?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num2?>" class="tab1"><span style="width:35px;"><label><?=$num2?></label></span></span></td>
				<td class="col-3">&nbsp;</td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num3?>" class="tab1"><span style="width:35px;"><label><?=$num3?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num4?>" class="tab1"><span style="width:35px;"><label><?=$num4?></label></span></span></td>
			</tr>
	<?
			}else{
	?>
			<tr height="68">
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num1?>" class="tab1"><span style="width:35px;"><label><?=$num1?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num2?>" class="tab1"><span style="width:35px;"><label><?=$num2?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num3?>" class="tab1"><span style="width:35px;"><label><?=$num3?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num4?>" class="tab1"><span style="width:35px;"><label><?=$num4?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num5?>" class="tab1"><span style="width:35px;"><label><?=$num5?></label></span></span></td>
			</tr>
	<?
			}
		}else{
	?>
			<tr height="68">
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num1?>" class="tab1"><span style="width:35px;"><label><?=$num1?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num2?>" class="tab1"><span style="width:35px;"><label><?=$num2?></label></span></span></td>
				<td>&nbsp;</td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num3?>" class="tab1"><span style="width:35px;"><label><?=$num3?></label></span></span></td>
				<td class="col-3" style="text-align:center;vertical-align:top;"><span id="seat<?=$num4?>" class="tab1"><span style="width:35px;"><label><?=$num4?></label></span></span></td>
			</tr>
	<?
		}
	}
	?>

		</tbody>
	</table>
</form>