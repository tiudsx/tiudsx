<form name="frmCancel" id="frmCancel" autocomplete="off">
<table class='et_vars exForm bd_tb' style="width:60%">
	<colgroup>
		<col style="width:100px;">
		<col style="width:auto;">
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
				<option value="서프존">서프존</option>
				<option value="브라보서프">브라보</option>
				<option value="솜씨당">솜씨당</option>
				<option value="모행">모행</option>
				<option value="안내공지">안내공지</option>
			</select>
		</td>
		<td style="text-align:center;"><input type="button" class="btnsurfdel" style="width:40px;" value="삭제" onclick="fnBusCancelDel(this);" ></td>
	</tr>
	<tr>
		<th class="col-02" style="text-align:center;" colspan="4">
			상단 타이틀
			
			<select id="notice_text"  name="notice_text" onchange="fnNoticeText(this);">
				<option value="일정변경">일정변경</option>
				<option value="운행취소">운행취소</option>
				<option value="직접작성">직접작성</option>
			</select> : 
			<input type="text" id="html_1" name="html_1" size="30" value="셔틀버스 운행 변경안내" class="itx">
		</th>
	</tr>
	<tr>
		<td class="col-02" style="text-align:center;" colspan="4">
			<textarea id="html_2" name="html_2" cols="70" rows="5">
7/2 양양>서울행(저녁) 일정으로 운행 예정이었던 서핑버스가 내부사정으로 운행 변경되었습니다.
변경된 차량 안내드리며, 예약건은 취소(전액 환불) 또는 좌석예약 링크 발송 예정이니 양해부탁드립니다.

 ▶ 운행가능 차량
  - 일 : 서울 > 양양행
  - 일 : 양양 > 서울행

 ▶ 운행취소 차량
  - 일 : 양양 > 서울행
  - 일 : 서울 > 양양행
  
  - 취소를 원하실 경우 상담톡으로 연락주시면 처리진행하겠습니다.
  - 이용에 불편드려 죄송합니다.</textarea>

<span style="display:none;">
<textarea id="hid_html1" name="hid_html1" cols="70" rows="5">
7/2 양양>서울행(저녁) 일정으로 운행 예정이었던 서핑버스가 내부사정으로 운행 변경되었습니다.
변경된 차량 안내드리며, 예약건은 취소(전액 환불) 또는 좌석예약 링크 발송 예정이니 양해부탁드립니다.

 ▶ 운행가능 차량
  - 일 : 서울 > 양양행
  - 일 : 양양 > 서울행

 ▶ 운행취소 차량
  - 일 : 양양 > 서울행
  - 일 : 서울 > 양양행
  
  - 취소를 원하실 경우 상담톡으로 연락주시면 처리진행하겠습니다.
  - 이용에 불편드려 죄송합니다.</textarea>
<textarea id="hid_html2" name="hid_html2" cols="70" rows="5">
이번주 운행예정이었던 셔틀버스는 최소인원 미달로 인해 운행 취소되어 안내드립니다.
예약건은 취소 및 전액환불 예정이니 양해부탁드립니다.
이용에 불편드려 죄송합니다.</textarea>
</span>
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

<form name="frmKakaoInfo" id="frmKakaoInfo" autocomplete="off">
<table class='et_vars exForm bd_tb' style="width:80%">
	<colgroup>
		<col style="width:100px;">
		<col style="width:80px;">
		<col style="width:auto;">
		<col style="width:100px;">
		<col style="width:80px;">
		<col style="width:auto;">
	</colgroup>
	<tr>
		<th>이용일</th>
		<td colspan="5">
			<input type="text" id="kakao_sDate" name="kakao_sDate" cal="sdate" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >&nbsp;~
			<input type="text" id="kakao_eDate" name="kakao_eDate" cal="edate" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
			<label><input type="checkbox" id="chkbusKakaoNumY1" name="chkbusNum_Kakao[]" value="테스트" style="vertical-align:-3px;" />테스트</label>
			<label><input type="checkbox" id="chkResInfo" name="chkResInfo" value="확정" style="vertical-align:-3px;" />확정안내</label>
		</td>
	</tr>
	<tr>
		<td colspan="6">
			양양행
		</td>
	</tr>
	<tr>
		<th rowspan="2"><label><input type="checkbox" checked="checked" value="7" style="vertical-align:-3px;" onclick="fnChkBusAll_Kakao(this, 'Y1')" />서울-양양행</label></th>
		<th>사당선</th>
		<td>
			<label><input type="checkbox" id="chkbusKakaoNumY1" name="chkbusNum_Kakao[]" checked="checked" value="YSa1" style="vertical-align:-3px;" />1호차</label>
			<label><input type="checkbox" id="chkbusKakaoNumY1" name="chkbusNum_Kakao[]" checked="checked" value="YSa2" style="vertical-align:-3px;" />2호차</label>
			<label><input type="checkbox" id="chkbusKakaoNumY1" name="chkbusNum_Kakao[]" checked="checked" value="YSa3" style="vertical-align:-3px;" />3호차</label>
		</td>
		<th rowspan="2"><label><input type="checkbox" checked="checked" value="7" style="vertical-align:-3px;" onclick="fnChkBusAll_Kakao(this, 'Y2')" />양양-서울행</label></th>
		<th>양양 오후</th>
		<td>
			<label><input type="checkbox" id="chkbusKakaoNumY2" name="chkbusNum_Kakao[]" checked="checked" value="SY21" style="vertical-align:-3px;" />1호차</label>
			<label><input type="checkbox" id="chkbusKakaoNumY2" name="chkbusNum_Kakao[]" checked="checked" value="SY22" style="vertical-align:-3px;" />2호차</label>
			<label><input type="checkbox" id="chkbusKakaoNumY2" name="chkbusNum_Kakao[]" checked="checked" value="SY23" style="vertical-align:-3px;" />3호차</label>
		</td>
	</tr>
	<tr>
		<th>종로선</th>
		<td>
			<label><input type="checkbox" id="chkbusKakaoNumY1" name="chkbusNum_Kakao[]" checked="checked" value="YJo1" style="vertical-align:-3px;" />1호차</label>
			<label><input type="checkbox" id="chkbusKakaoNumY1" name="chkbusNum_Kakao[]" checked="checked" value="YJo2" style="vertical-align:-3px;" />2호차</label>
			<label><input type="checkbox" id="chkbusKakaoNumY1" name="chkbusNum_Kakao[]" checked="checked" value="YJo3" style="vertical-align:-3px;" />3호차</label>
		</td>
		<th>양양 저녁</th>
		<td>
			<label><input type="checkbox" id="chkbusKakaoNumY2" name="chkbusNum_Kakao[]" checked="checked" value="SY51" style="vertical-align:-3px;" />1호차</label>
			<label><input type="checkbox" id="chkbusKakaoNumY2" name="chkbusNum_Kakao[]" checked="checked" value="SY52" style="vertical-align:-3px;" />2호차</label>
			<label><input type="checkbox" id="chkbusKakaoNumY2" name="chkbusNum_Kakao[]" checked="checked" value="SY53" style="vertical-align:-3px;" />3호차</label>
		</td>
	</tr>
</table>
<table class='et_vars exForm bd_tb' style="width:60%">
	<colgroup>
		<col style="width:50%;">
		<col style="width:50%;">
	</colgroup>
	<tr>
		<th class="col-02" style="text-align:center;">
			상단 안내
		</th>
		<th class="col-02" style="text-align:center;">
			안내내용
		</th>
	</tr>
	<tr>
		<td class="col-02" style="text-align:center;">
			<textarea id="kakao_1" name="kakao_1" cols="40" rows="7">
액트립 서핑버스는 양양/동해 두개의 도착지로 운행되고 있습니다.
서울 출발 노선(사당선)이 동일하여, 두대의 서핑버스가 같은 경로로 이동하는데요.
탑승시 꼭 확인 후 탑승 부탁드립니다.</textarea>
		</td>
		<td class="col-02" style="text-align:center;">
			<textarea id="kakao_2" name="kakao_2" cols="40" rows="7">
 ▶ 휴게소 정차
  - 서울-양양 : 홍천휴게소
  - 서울-동해 : 횡성휴게소
  (단, 교통상황에 따라 정차 휴게소는 변경될 수 있습니다.)</textarea>
		</td>
	</tr>
	<tr>
		<td class="col-02" style="text-align:center;">
			<textarea id="kakao_3" name="kakao_3" cols="40" rows="7">
  - 각 도착지로 운행중 휴게소에 정차하게 되는데요.
  - 상황에 따라 15~30분 정도 휴식하니 참고부탁드립니다.</textarea>
		</td>
	</tr>
	<tr>
		<td class="col-02" style="text-align:center;" colspan="4">
			<input type="hidden" id="resparam" name="resparam" size="10" value="busKakaoInfo" class="itx">
			<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="발송" onclick="fnKakaoInfo();" id="Add"/>
		</td>
	</tr>
</table>
</form>