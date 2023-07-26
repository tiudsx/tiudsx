<table class='et_vars exForm bd_tb'>
    <colgroup>
		<col style="width:auto;">
	</colgroup>
	<tr>
		<td>
			<select id="selPack">
                <option value="">패키지 선택</option>
                <option value="I">인구</option>
                <option value="M">마린</option>
            </select>
            <input type="text" id="packDate" name="packDate" cal="date" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
            <input type="button" class="gg_btn res_btn_color2" style="width:40px; height:20px;" value="조회" onclick="fnSearchPack();"/>
		</td>
	</tr>
	<tr>
		<th>패키지인원</th>
	</tr>
    <tbody id="t_pack_list">

    </tbody>
</table>