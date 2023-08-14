
<form name="frmResKakao" id="frmResKakao" autocomplete="off">
<table class='et_vars exForm bd_tb'>
	<colgroup>
		<col style="width:15%">
		<col style="width:auto;">
		<col style="width:14%">
		<col style="width:18%">
		<col style="width:20%">
		<col style="width:20%">
	</colgroup>
	<tr>
		<td colspan="6">
			알림톡 발송 번호
		</td>
	</tr>
	<tr>
		<th></th>
		<th>노선</th>
		<th>이름</th>
		<th>연락처</th>
		<th>이용일 (서울출발)</th>
		<th>이용일 (서울복귀)</th>
	</tr>
	<tr>
		<td>			
			<select id="busgubun" onchange="fnAdminBusGubun(this, 1);">
				<option value="1">1박 왕복</option>
				<option value="2">당일 왕복</option>
				<option value="3">편도</option>
			</select>
			<?if($user_id == "mohaeng"){?>
				<input type="hidden" id="reschannel" name="reschannel" value="31">
			<?}else{?>
			 &nbsp;
			<select id="reschannel">
				<option value="31">모행</option>
				<option value="23">금진 브라보</option>
				<option value="22" kakaoUrl="https://open.kakao.com/o/g15tGdBf">프립-힐링캠프</option>
				<option value="29" kakaoUrl="https://open.kakao.com/o/g15tGdBf">네이버-힐링캠프</option>
			</select>
			<?}?>
		</td>
		<td style="text-align:center;">
			<select id="resbus">
				<option value="DH">-- 동해 --</option>
			</select>
		</td>
		<td><input type="text" id="username" name="username" style="width:66px;" value="" class="itx2" maxlength="20" onkeyup="spacetrim(this);"></td>
		<td><input type="text" id="userphone" name="userphone" style="width:100px;" value="" class="itx2" maxlength="20" onkeyup="spacetrim(this);" oninput="this.value = this.value.replace(/[^0-9-]/g, '').replace(/(\..*)\./g, '$1');"></td>
		<td>
			<input type="text" id="resDate1" name="resDate1" cal="sdate" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
			<select id="resbusseat1" onchange="fnAdminBusGubun(this, 2);">
			<?for ($i=0; $i < 20; $i++) { 
				echo '<option value="'.$i.'">'.$i.'명</option>';
			}?>
			</select>
		</td>
		<td>
			<input type="text" id="resDate2" name="resDate2" cal="edate" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
			<select id="resbusseat2">
			<?for ($i=0; $i < 20; $i++) { 
				echo '<option value="'.$i.'">'.$i.'명</option>';
			}?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="6" style="text-align:center;">
			<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="알림톡 발송" onclick="fnAdminResKakao();" /> &nbsp;
			<input type="button" class="gg_btn res_btn_color2" style="width:120px; height:40px;" value="초기화" onclick="fnAdminReset('#frmResKakao');" />
		</td>
	</tr>
</table>
</form>

<div class="gg_first">알림톡 발송 정보 <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:40px; height:20px;" value="조회" onclick="fnSearchAdmin('bus_mohaeng/list_search_channel.php', '#mngKakaoSearch', 'N');" /></div>
<div id="mngKakaoSearch"></div>
