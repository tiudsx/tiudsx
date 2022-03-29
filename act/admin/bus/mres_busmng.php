<?php
include __DIR__.'/../../db.php';
include __DIR__.'/../../surf/surffunc.php';

$selDate = $_REQUEST["selDate"];

$select_query_bus = "SELECT seq, shopname, res_busnum, res_confirm, COUNT(*) AS CntBus FROM `AT_RES_SUB` 
						WHERE code = 'bus'
							AND res_date = '$selDate' 
							AND res_confirm = 3 
						GROUP BY seq, shopname, LEFT(res_busnum, 1) DESC, 
							(CASE WHEN LEFT(res_busnum, 1) = 'Y'  OR LEFT(res_busnum, 1) = 'E' 
								THEN RIGHT(res_busnum, 1) 
								ELSE RIGHT(res_busnum, 2) END), res_confirm";
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
				<td style="text-align:center;height:50px;">
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
$arrBusY = array(); //양양행
$arrBusS = array(); //양양 서울행
$arrBusE = array(); //동해행
$arrBusA = array(); //동해 서울행
while ($rowSub = mysqli_fetch_assoc($result_bus)){
	$bus = substr($rowSub['res_busnum'], 0, 1);
	if($bus == "Y"){
		$arrBusY[$rowSub['res_busnum']] = $rowSub['CntBus'];
	}else if($bus == "S"){
		$arrBusS[$rowSub['res_busnum']] = $rowSub['CntBus'];
	}else if($bus == "E"){
		$arrBusE[$rowSub['res_busnum']] = $rowSub['CntBus'];
	}else if($bus == "A"){
		$arrBusA[$rowSub['res_busnum']] = $rowSub['CntBus'];
	}

	$arrBus[$rowSub['res_busnum']][$rowSub['res_confirm']] = $rowSub['CntBus'];
}
?>

<form name="frmDaySearch" id="frmDaySearch" autocomplete="off">

<input type="hidden" id="selDate" name="selDate" value="<?=$selDate?>">
<input type="hidden" id="busNum" name="busNum" value="">

<div class="gg_first" style="margin-top:0px;">셔틀버스 예약정보</div>
<table class='et_vars exForm bd_tb' style="width:100%;display:;">
	<colgroup>
		<col style="width:10%;">
		<col style="width:10%;">
		<col style="width:*;">
	</colgroup>
	<tr>
		<th>양양행</th>
	</tr>
	<tr>
		<td>
			<?foreach($arrBusY as $key=>$value){?>
				<input type="button" name="buspoint" class="bd_btn" busgubun="<?=$key?>" style="padding-top:4px;font-family: gulim,Tahoma,Arial,Sans-serif;" value="<?=fnBusNum($key)?> [<?=$value?>명]" onclick="fnDayList('<?=$key?>', this);" />
			<?}?>
		</td>
	</tr>
	<tr>
		<td>
			<?foreach($arrBusS as $key=>$value){?>
				<input type="button" name="buspoint" class="bd_btn" busgubun="<?=$key?>" style="padding-top:4px;font-family: gulim,Tahoma,Arial,Sans-serif;" value="<?=fnBusNum($key)?> [<?=$value?>명]" onclick="fnDayList('<?=$key?>', this);" />
			<?}?>
		</td>
	</tr>
	<tr>
		<th>동해행</th>
	</tr>
	<tr>
		<td>
			<?foreach($arrBusE as $key=>$value){?>
				<input type="button" name="buspoint" class="bd_btn" busgubun="<?=$key?>" style="padding-top:4px;font-family: gulim,Tahoma,Arial,Sans-serif;" value="<?=fnBusNum($key)?> [<?=$value?>명]" onclick="fnDayList('<?=$key?>', this);" />
			<?}?>
		</td>
	</tr>
	<tr>
		<td>
			<?foreach($arrBusA as $key=>$value){?>
				<input type="button" name="buspoint" class="bd_btn" busgubun="<?=$key?>" style="padding-top:4px;font-family: gulim,Tahoma,Arial,Sans-serif;" value="<?=fnBusNum($key)?> [<?=$value?>명]" onclick="fnDayList('<?=$key?>', this);" />
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

<script>
function fnDayList(vlu, obj){
	$j("input[name=buspoint]").removeClass("buson");
	$j("input[name=buspoint]").css("background", "white");
	if(vlu == "ALL"){
		$j("#dayList").html('<div style="text-align:center;font-size:14px;padding:50px;"><b>버스종류를 선택하세요.</b></div>');
	}else{
		$j('#dayList').block({ message: "<br><h1>셔틀버스 좌석 조회 중...</h1><br><br>" }); 

		$j(obj).addClass("buson");
		$j(obj).css("background", "#2dc15e");
		$j("#busNum").val(vlu);

		var formData = $j("#frmDaySearch").serializeArray();

		$j.post("/act/admin/bus/mres_busmnglist.php", formData,
			function(data, textStatus, jqXHR){
			   $j("#dayList").html(data);
			   $j('#dayList').unblock();
			}).fail(function(jqXHR, textStatus, errorThrown){
		 
		});
	}
}
</script>