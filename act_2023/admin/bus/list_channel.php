
<form name="frmResKakao" id="frmResKakao" autocomplete="off">
<table class='et_vars exForm bd_tb'>
	<colgroup>
		<col style="width:10%">
		<col style="width:*;">
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
		<th>노선</th>
		<th>채널</th>
		<th>이름</th>
		<th>연락처</th>
		<th>이용일 (서울출발)</th>
		<th>이용일 (서울복귀)</th>
	</tr>
	<tr>
		<td>
			<select id="reschannel" onchange="fnChannel(this);">
				<option value="11">프립</option>
				<option value="17" kakaoUrl="https://open.kakao.com/o/goYwKe5e">프립-마린</option>
				<option value="20" kakaoUrl="https://open.kakao.com/o/gf4LMe5e">프립-인구</option>
				<option value="21" kakaoUrl="https://open.kakao.com/o/g58J34ff">프립-서팩 동해</option>
				<option value="22" kakaoUrl="https://open.kakao.com/o/g4UVz4ff">프립-힐링캠프</option>
				<option value="16">클룩</option>
				<option value="7">네이버쇼핑</option>
				<option value="10">네이버예약</option>
				<option value="12">마이리얼트립</option>
				<option value="15">서프존</option>
				<option value="23">금진 브라보</option>
			</select>
		</td>
		<td style="text-align:center;">
			<select id="resbus">
				<option value="YY">-- 양양 --</option>
				<option value="DH">-- 동해 --</option>
			</select>
		</td>
		<td><input type="text" id="username" name="username" style="width:66px;" value="" class="itx2" maxlength="20" ></td>
		<td><input type="text" id="userphone" name="userphone" style="width:100px;" value="" class="itx2" maxlength="20"></td>
		<td>
			<input type="text" id="resDate1" name="resDate1" cal="date" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
			<select id="resbusseat1">
			<?for ($i=0; $i < 20; $i++) { 
				echo '<option value="'.$i.'">'.$i.'명</option>';
			}?>
			</select>
		</td>
		<td>
			<input type="text" id="resDate2" name="resDate2" cal="date" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
			<select id="resbusseat2">
			<?for ($i=0; $i < 20; $i++) { 
				echo '<option value="'.$i.'">'.$i.'명</option>';
			}?>
			</select>
		</td>
	</tr>
	<tr id="fripMapping" style="display:;">
		<td>데이터 맵핑</td>
		<td colspan="5">
			<textarea id="html_1" cols="40" rows="7"></textarea>
			<input type="button" class="gg_btn res_btn_color2" style="width:40px; height:20px;" value="맵핑" onclick="fnGetJson();" />

			<textarea id="html_2" cols="40" rows="7" style="display: ;"></textarea>
			<div id="divCopy" style="display: none;"></div>
		</td>
	</tr>
	<tr>
		<td colspan="6" style="text-align:center;"><input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="알림톡 발송" onclick="fnResKakaoAdmin();" /></td>
	</tr>
</table>
</form>

<div class="gg_first">알림톡 발송 정보 <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:40px; height:20px;" value="조회" onclick="fnSearchAdmin('bus/list_search_channel.php', '#mngKakaoSearch', 'N');" /></div>
<div id="mngKakaoSearch"> (https://alimtalk-api.bizmsg.kr/codeList.html)</div>