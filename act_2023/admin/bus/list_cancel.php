<form name="frmCancel" id="frmCancel" autocomplete="off">
<table class='et_vars exForm bd_tb' style="width:60%">
	<colgroup>
		<col style="width:100px;">
		<col style="width:*;">
		<col style="width:150px;">
		<col style="width:100px;">
	</colgroup>
	<tr>
		<th>이름</th>
		<th>연락처</th>
		<th>예약채널</th>
		<th><input type="button" style="width:40px;" value="추가" onclick="fnBusAdd('trbuscancel');"></th>
	</tr>
	<tr id="trbuscancel" style="display:none;">
		<td style="text-align:center;">
			<input type="text" name="user_name[]" size="10" value="" class="itx">
		</td>
		<td style="text-align:center;">
			<input type="text" name="user_tel[]" size="10" value="" class="itx">
		</td>
		<td style="text-align:center;">
			<select id="user_channel"  name="user_channel[]">
				<option value="프립">프립</option>
				<option value="액트립">액트립</option>
				<option value="클룩">클룩</option>
				<option value="네이버쇼핑">네이버쇼핑</option>
				<option value="네이버예약">네이버예약</option>
				<option value="마이리얼트립">마이리얼트립</option>
				<option value="서프존">서프존</option>
				<option value="브라보서프">브라보</option>
				<option value="엑스크루">엑스크루</option>
				<option value="솜씨당">솜씨당</option>
			</select>
		</td>
		<td style="text-align:center;"><input type="button" class="btnsurfdel" style="width:40px;" value="삭제" onclick="fnBusCancelDel(this);" ></td>
	</tr>
	<tr>
		<th class="col-02" style="text-align:center;" colspan="2">
			상단 안내
		</th>
		<th class="col-02" style="text-align:center;" colspan="2">
			안내내용
		</th>
	</tr>
	<tr>
		<td class="col-02" style="text-align:center;" colspan="2">
			<textarea id="html_1" name="html_1" cols="40" rows="7">
이번주에 운행 예정이었던 서핑버스가 최소인원 미달로 인해 일부 운행 취소되어 죄송한 말씀 드립니다.
취소된 차량 안내드리며, 예약건은 취소 및 전액 환불 예정이니 양해부탁드립니다.</textarea>
		</td>
		<td class="col-02" style="text-align:center;" colspan="2">
			<textarea id="html_2" name="html_2" cols="40" rows="7">
 ▶ 운행가능 차량
  - 27일 : 서울 > 양양행
  - 28일 : 양양 > 서울행

 ▶ 운행취소 차량
  - 27일 : 양양 > 서울행
  - 28일 : 서울 > 양양행
  - 29일 : 서울 <> 양양</textarea>
		</td>
	</tr>
	<tr>
		<td class="col-02" style="text-align:center;" colspan="4">
			<input type="hidden" id="resparam" name="resparam" size="10" value="busCancel" class="itx">
			<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="발송" onclick="fnBusCancel();" id="Add"/>
			<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="초기화" onclick="fnBusCancelReset();" />
		</td>
	</tr>
</table>
</form>  