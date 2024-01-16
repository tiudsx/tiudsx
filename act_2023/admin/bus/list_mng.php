<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$selDate = $_REQUEST["selDate"];
$shopseq = $_REQUEST["seq"];

$select_query_bus = "SELECT seq, bus_oper, bus_gubun, bus_num, res_confirm, COUNT(*) AS CntBus,
						(CASE WHEN bus_gubun = 'SA'
									THEN '10' + RIGHT(bus_num, 1)
								WHEN bus_gubun = 'JO'
									THEN '20' + RIGHT(bus_num, 1)
								WHEN bus_gubun = 'AM'
									THEN '30' + RIGHT(bus_num, 1)
								WHEN bus_gubun = 'PM'
									THEN '40' + RIGHT(bus_num, 1)
								ELSE bus_num END) AS orderby FROM `AT_RES_SUB` 
						WHERE code = 'bus'
							AND res_date = '$selDate' 
							AND res_confirm = 3 
							AND seq = $shopseq
						GROUP BY seq, bus_oper, bus_gubun, bus_num, res_confirm
						ORDER BY orderby ASC";
$result_bus = mysqli_query($conn, $select_query_bus);
$count = mysqli_num_rows($result_bus);

if($count == 0){
?>
	<div class="contentimg bd">
	<div class="gg_first">셔틀버스 예약정보</div>
	<table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:5px;width:100%;">
		<colgroup>
			<col width="*" />
		</colgroup>
		<tbody>
			<tr>
				<td colspan="5" style="text-align:center;height:50px;">
				<b>확정 예약된 목록이 없습니다.</b>
				</td>
			</tr>
		</tbody>
	</table>
</div>
	
<?
	return;
}

$arrBus = array();
$arrBus_Start = array(); //서울 출발
$arrBus_Return = array(); //서울 복귀
while ($rowSub = mysqli_fetch_assoc($result_bus)){
	if($rowSub['bus_oper'] == "start"){
		$arrBus_Start[$rowSub['bus_gubun'].$rowSub['bus_num']] = $rowSub['CntBus'];
	}else if($rowSub['bus_oper'] == "return"){
		$arrBus_Return[$rowSub['bus_gubun'].$rowSub['bus_num']] = $rowSub['CntBus'];
	}

	$arrBus[$rowSub['bus_gubun'].$rowSub['bus_num']][$rowSub['res_confirm']] = $rowSub['CntBus'];
}


$arrBus = fnBusUrl($shopseq);

$bus_type = $arrBus["type"]; //양양, 동해
?>

<form name="frmDaySearch" id="frmDaySearch" autocomplete="off">

<input type="hidden" id="selDate" name="selDate" value="<?=$selDate?>">
<input type="hidden" id="seq" name="seq" value="<?=$shopseq?>">
<input type="hidden" id="bus_gubun" name="bus_gubun" value="">
<input type="hidden" id="bus_num" name="bus_num" value="">

<div class="gg_first" style="margin-top:0px;">셔틀버스 예약정보</div>
<table class='et_vars exForm bd_tb' style="width:100%;">
	<colgroup>
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:auto;">
	</colgroup>
	<tr>
		<th rowspan="2"><?=$bus_type?>행</th>
		<th>서울 출발</th>
		<td>
			<?
			foreach($arrBus_Start as $key=>$value){
				$arrBusName = fnBusNum2023($key);
				$gubun = $arrBusName["gubun"];
				$num = $arrBusName["bus_num"];
			?>
				<input type="button" name="buspoint" class="bd_btn" busgubun="<?=$key?>" style="padding-top:4px;font-family: gulim,Tahoma,Arial,Sans-serif;" value="<?=$arrBusName["full"]?> [<?=$value?>명]" onclick="fnDayList('<?=$gubun?>', '<?=$num?>', this, 'bus');" />
			<?}?>
		</td>
	</tr>
	<tr>
		<th>서울 복귀</th>
		<td>
			<?
			foreach($arrBus_Return as $key=>$value){
				$arrBusName = fnBusNum2023($key);
				$gubun = $arrBusName["gubun"];
				$num = $arrBusName["bus_num"];
			?>
				<input type="button" name="buspoint" class="bd_btn" busgubun="<?=$key?>" style="padding-top:4px;font-family: gulim,Tahoma,Arial,Sans-serif;" value="<?=$arrBusName["full"]?> [<?=$value?>명]" onclick="fnDayList('<?=$gubun?>', '<?=$num?>', this, 'bus');" />
			<?}?>
		</td>
	</tr>
</table>
</form>

<div id="dayList">
	<div style="text-align:center;font-size:14px;padding:50px;">
		<b>버스종류를 선택하세요.</b>
	</div>
</div>