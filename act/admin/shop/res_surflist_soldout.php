<?php
$chk = $_REQUEST["chk"];
if($chk != ""){
    include __DIR__.'/../../db.php';
    
    session_start();
}

$select_query = 'SELECT a.*, b.optname FROM `AT_PROD_OPT_SOLDOUT` as a INNER JOIN AT_PROD_OPT as b
					ON a.seq = b.seq
						AND a.optseq = b.optseq
					WHERE a.seq = '.$_SESSION['shopseq'].' ORDER BY a.soldout_date, a.optseq';
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);
?>

<div class="gg_first" style="padding-bottom:7px;">[<?=$_SESSION['shopname']?>] - 매진 처리 목록</div>

<table class="et_vars exForm bd_tb" style="margin-bottom:5px;width:100%;">
<colgroup>
	<col style="width:100px;">
	<col style="width:*;">
	<col style="width:*;">
	<col style="width:*;">
	<col style="width:*;">
</colgroup>
<tbody>
	<?
	if($count == 0){
		echo '<tr><td><div style="text-align:center;font-size:14px;padding:50px;">
					<b>매진 처리된 항목이 없습니다.</b>
				</div></td></tr>';
	}else{
	?>
	<tr>
		<th style="text-align:center;">날짜</th>
		<th style="text-align:center;">항목</th>
		<th style="text-align:center;">남</th>
		<th style="text-align:center;">여</th>
		<th style="text-align:center;"></th>
	</tr>

	<?
		while ($row = mysqli_fetch_assoc($result_setlist)){
			$optsexM = "";
			$optsexW = "";
			if($row["opt_sexM"] == "Y") $optsexM = "매진";
			if($row["opt_sexW"] == "Y") $optsexW = "매진";
	?>
	<tr>
		<td style="text-align:center;"><?=$row["soldout_date"]?></td>
		<td style="text-align:center;"><?=$row["optname"]?></td>
		<td style="text-align:center;"><?=$optsexM?></td>
		<td style="text-align:center;"><?=$optsexW?></td>
		<td style="text-align:center;"><input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:40px; height:23px;" value="삭제" onclick="fnSoldModify(<?=$row["soldoutseq"]?>);" /></td>
	</tr>

	<?
		}
	}
	?>
</tbody>
</table>

