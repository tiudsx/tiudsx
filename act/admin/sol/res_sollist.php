<?php 
include __DIR__.'/../../db.php';
include __DIR__.'/../../common/logininfo.php';
?>

<link rel="stylesheet" type="text/css" href="/act/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/act/css/surfview.css">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_surf.css">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_common.css">
<script type="text/javascript" src="/act/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/act/js/admin_surf.js"></script>
<script type="text/javascript" src="/act/js/admin_sol.js?v=5"></script>
<script type="text/javascript" src="/act/js/common.js"></script>

<div class="bd_tl" style="width:100%;">
	<h1 class="ngeb clear"><i class="bg_color"></i>솔게스트하우스 예약관리</h1>
</div>

<script>
    var mobileuse = "";
</script>

<div class="container" id="contenttop" style="padding-top:35px;">

	<div id="containerTab">
		<ul class="tabs">
			<li class="active" rel="tab1">예약현황</li>
			<li rel="tab2">예약검색</li>
		</ul>
		
		<div class="tab_container">
			<!-- #tab1 -->
			<div id="tab1" class="tab_content">
				<section>
					<aside id="right_article3" class="left_article5">
						<?include 'res_calendar.php'?>
					</aside>
					<article class="right_article5">
						<div class="gg_first">객실 예약현황 (<span id="roomdate"></span>) <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:80px; height:20px;" value="예약등록" onclick="fnSolInsert();" /></div>
						<table class="et_vars exForm bd_tb tbcenter tbsolstay" style="margin-bottom:5px;width:100%;">
							<colgroup>
								<col style="width:8%;">
								<col style="width:17%;">
								<col style="width:8%;">
								<col style="width:17%;">
								<col style="width:8%;">
								<col style="width:17%;">
								<col style="width:8%;">
								<col style="width:17%;">
							</colgroup>
							<tbody>
								<tr>
									<th colspan="2">201호(8명)</th>
									<th colspan="2">202호(10명)</th>
									<th colspan="2">203호(6명)</th>
									<th colspan="2">204호(8명)</th>
								</tr>
								<?for ($i=1; $i < 11; $i++) { 
									
								?>
								<tr>
									<?if($i > 8){ echo "<td></td><td></td>"; }else{?>
										<td><?=$i?>번(<?=(($i % 2) == 1) ? "1" : "2"?>층)</td>
										<td room="201" id="201<?=$i?>"></td>
									<?}?>
										<td><?=$i?>번(<?=(($i % 2) == 1) ? "1" : "2"?>층)</td>
										<td room="202" id="202<?=$i?>"></td>
									<?if($i > 6){ echo "<td></td><td></td>"; }else{?>
										<td><?=$i?>번(<?=(($i % 2) == 1) ? "1" : "2"?>층)</td>
										<td room="203" id="203<?=$i?>"></td>
									<?}?>
									<?if($i > 8){ echo "<td></td><td></td>"; }else{?>
										<td><?=$i?>번(<?=(($i % 2) == 1) ? "1" : "2"?>층)</td>
										<td room="204" id="204<?=$i?>"></td>
									<?}?>
								</tr>	
								<?}?>
								<tr>
									<th colspan="4">301호(12명)</th>
									<th colspan="2">302호(8명)</td>
									<th colspan="2">303호(10명)</td>
								</tr>
								<?for ($i=1; $i < 11; $i++) { 
									
									?>
									<tr>
										<?if($i > 6){ echo "<td></td><td></td>"; }else{?>
											<td><?=$i?>번(<?=(($i % 2) == 1) ? "1" : "2"?>층)</td>
											<td room="301" id="301<?=$i?>"></td>
										<?}?>
										<?if($i > 6){ echo "<td></td><td></td>"; }else{?>
											<td><?=$i + 6?>번(<?=(($i % 2) == 1) ? "1" : "2"?>층)</td>
											<td room="301" id="301<?=$i + 6?>"></td>
											<?}?>
										<?if($i > 8){ echo "<td></td><td></td>"; }else{?>
											<td><?=$i?>번(<?=(($i % 2) == 1) ? "1" : "2"?>층)</td>
											<td room="302" id="302<?=$i?>"></td>
										<?}?>
											<td><?=$i?>번(<?=(($i % 2) == 1) ? "1" : "2"?>층)</td>
											<td room="303" id="303<?=$i?>"></td>
									</tr>	
									<?}?>
							</tbody>
						</table>
					</article>
				</section>

				<div id="mnglist" style="display:inline-block;width:100%">
				</div>
				<div id="mnglistStay" style="display:inline-block;width:100%">
				</div>
				<div id="mnglistSurf" style="display:inline-block;width:100%">
				</div>
			</div>

			<div id="tab2" class="tab_content" style="display:none;">
				<form name="frmSearch" id="frmSearch" autocomplete="off">
				<div class="gg_first" style="margin-top:0px;">예약관리 검색</div>
				<table class='et_vars exForm bd_tb' style="width:100%">
					<colgroup>
						<col style="width:65px;">
						<col style="width:*;">
						<col style="width:100px;">
					</colgroup>
					<tr>
						<th>구분</th>
						<td>
							<label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="0" checked="checked" style="vertical-align:-3px;" />대기</label>
							<label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="1" checked="checked" style="vertical-align:-3px;" />확정</label>
							<label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="3" style="vertical-align:-3px;" />환불</label>
							<label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="4" style="vertical-align:-3px;" />취소</label>
						</td>
					</tr>
					<tr>
						<th>검색어</th>
						<td><input type="text" id="schText" name="schText" value="" class="itx2" style="width:140px;"></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;"><input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="검색" onclick="fnSearchAdminSol('sol/res_sollist_search.php', 'mngSearch');" /></td>
					</tr>
				</table>
				</form>
				<div id="mngSearch"></div>
			</div>
		</div>

	</div>
</div> 

<div id="res_modify" style="display:none;padding:5px;height: 600px;overflow-y: auto;"> 
    <form name="frmModify" id="frmModify" autocomplete="off">
    <div class="gg_first" style="margin-top:0px;">솔게스트하우스 예약등록 (<?=date("Y-m-d A h:i:s")?>)</div>
    <table class="et_vars exForm bd_tb" style="width:100%;display:;" id="infomodify">
        <colgroup>
            <col width="10%" />
            <col width="23%" />
            <col width="10%" />
            <col width="23%" />
            <col width="10%" />
            <col width="24%" />
        </colgroup>
        <tbody>
			<tr>
				<th>등록관리자</th>
				<td>
					<select id="res_adminname" name="res_adminname" class="select">
                        <option value='이승철'>이승철</option>
                        <option value='정태원'>정태원</option>
                        <option value='정태일'>정태일</option>
                    </select>
				</td>
                <th>예약자이름</th>
                <td><input type="text" id="user_name" name="user_name" size="15" value="" class="itx"></td>
                <th>연락처</th>
				<td>
					<input type="text" id="user_tel" name="user_tel" size="15" value="" class="itx">
					<!-- <input type="text" id="user_tel1" name="user_tel1" size="4" maxlength="4" value="" class="itx"> -
					<input type="text" id="user_tel2" name="user_tel2" size="5" maxlength="4" value="" class="itx"> -
					<input type="text" id="user_tel3" name="user_tel3" size="5" maxlength="4" value="" class="itx"> -->
				</td>
			</tr>
			<tr>
				<th>예약처</th>
				<td>
					<select id="res_company" name="res_company" class="select">
						<option value='네이버예약'>네이버예약</option>
						<option value='네이버쇼핑'>네이버쇼핑</option>
						<option value='전화예약'>전화예약</option>
						<option value='현장예약'>현장예약</option>
						<option value='여기어때'>여기어때</option>
						<option value='액트립'>액트립</option>
						<option value='프립'>프립</option>
						<option value='클룩'>클룩</option>
						<option value='야놀자'>야놀자</option>
					</select>
				</td>
				<th>예약상태</th>
				<td>
					<select id="res_confirm" name="res_confirm" class="select">
                        <option value='확정'>확정</option>
                        <option value='대기'>대기</option>
						<option value='환불'>환불</option>
						<option value='취소'>취소</option>
                    </select>
				</td>
                <th>알림톡</th>
				<td>
					<select id="res_kakao" name="res_kakao" class="select">						
						<option value='N'>미발송</option>
						<option value='Y'>발송</option>
					</select> (확정일 경우만 발송)
				</td>
			</tr>
			<tr>
                <th>숙박</th>
                <td colspan="6">
					<table class="et_vars exForm bd_tb tbcenter" style="width:100%">
						<colgroup>
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="70" />
						</colgroup>
						<tbody>
							<tr>
                                <th colspan="4">숙박정보</th>
                                <th colspan="2">바베큐/펍 참여</th>
                                <th rowspan="2"><input type="button" class="btnsurfadd" style="width:40px;" value="추가" onclick="fnSolAdd(this, 'trstay');" ></th>
                            </tr>
                            <tr>
                                <th>숙소명</th>
                                <th>이용일</th>
                                <th>성별</th>
                                <th>고객정보</th>
								<th>참여여부</th>
                                <th>이용일</th>
							</tr>
							<tr id="trstay" style="display:none;">
								<td>
									<input type="hidden" id="stayseq" name="stayseq[]" >
									<input type="hidden" id="staytype" name="staytype[]" value="I">
									<select id="res_stayshop" name="res_stayshop[]" class="select" onchange="fnSolStaySel(this);">
										<option value='N'>숙박미신청</option>
										<option value='솔게스트하우스'>솔게스트하우스</option>
										<!-- <option value='모닝비치'>모닝비치</option>
										<option value='방파제민박'>방파제민박</option> -->
									</select>
								</td>
								<td style="line-height:2.3em">
									<input type="text" calid="res_staysdate" cal="sol_sdate" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" disabled> ~ 
									<input type="text" calid="res_stayedate" cal="sol_edate" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" disabled>

									<input type="hidden" id="res_staysdate" name="res_staysdate[]" value="">
									<input type="hidden" id="res_stayedate" name="res_stayedate[]" value="">
									<input type="hidden" id="res_bbqdate" name="res_bbqdate[]" value="">
								</td>
								<td>
									<select id="res_staysex" name="res_staysex[]" class="select">
										<option value="남">남</option>
										<option value="여">여</option>	
									</select>
									<select id="res_stayM" name="res_stayM[]" class="select">
									<?for($i=1;$i<=1;$i++){?>
										<option value="<?=$i?>"><?=$i?></option>
									<?}?>
									</select>명
								</td>
								<td>
									<select id="res_stayroom" name="res_stayroom[]" class="select" onchange="fnRoomNum(this);" sel="">
										<option value="201">201호</option>
										<option value="202">202호</option>
										<option value="203">203호</option>
										<option value="204">204호</option>
										<option value="" selected>-------</option>
										<option value="301">301호</option>
										<option value="302">302호</option>
										<option value="303">303호</option>
									</select>
									<select id="res_staynum" name="res_staynum[]" class="select" sel="">
										<option value="">-------</option>
									</select>
								</td>
								<td>
									<select id="res_bbq" name="res_bbq[]" class="select" onchange="fnSolBbqSel(this);">
										<option value="N">미참여</option>
										<option value="바베큐">바베큐</option>
										<!-- <option value="펍파티">펍파티</option>
										<option value="바베큐,펍파티">바베큐,펍파티</option> -->
									</select>
								</td>
								<td>
									<input type="text" calid="res_bbqdate" cal="date" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" disabled>
								</td>
								<td style="text-align:center;"><input type="button" class="btnsurfdel" style="width:40px;" value="삭제" onclick="fnSolDel(this);" ></td>
							</tr>
                        </tbody>
					</table>
                </td>
			</tr>
            <tr>
                <th>서핑강습</th>
                <td colspan="6">
					<table class="et_vars exForm bd_tb tbcenter" style="width:100%">
						<colgroup>
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="70" />
						</colgroup>
                        <tbody>
                            <tr>
                                <th rowspan="2">서핑샵</th>
                                <th rowspan="2">이용일</th>
                                <th colspan="3">서핑강습</th>
                                <th colspan="3">장비렌탈</th>
                                <th rowspan="2"><input type="button" class="btnsurfadd" style="width:40px;" value="추가" onclick="fnSolAdd(this, 'trsurf');" ></th>
                            </tr>
                            <tr>
                                <th>시간</th>
                                <th>남</th>
                                <th>여</th>
                                <th>종류</th>
                                <th>남</th>
                                <th>여</th>
							</tr>
							<tr id="trsurf" style="display:none;">
								<td>
									<input type="hidden" id="surfseq" name="surfseq[]" >
									<input type="hidden" id="surftype" name="surftype[]" value="I">
									<select id="res_surfshop" name="res_surfshop[]" class="select">
										<option value='서퍼랑'>서퍼랑</option>
										<option value='서프팩토리'>서프팩토리</option>
										<option value='솔게스트하우스'>솔게스트하우스</option>
										<!-- <option value='라라서프'>라라서프</option> -->
									</select>
								</td>
								<td>
									<input type="text" calid="res_surfdate" name="res_surfdate[]" cal="date" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
								</td>
								<td>
									<select id="res_surftime" name="res_surftime[]" class="select" onchange="fnSolSurfSel(this);">
										<option value=''>강습미신청</option>
										<option value='9시'>9시</option>
										<option value='11시'>11시</option>
										<option value='13시'>13시</option>
										<option value='15시'>15시</option>
									</select>
								</td>
								<td>
									<select id="res_surfM" name="res_surfM[]" class="select">
									<?for($i=0;$i<=20;$i++){?>
										<option value="<?=$i?>"><?=$i?></option>
									<?}?>
									</select>명
								</td>
								<td>
									<select id="res_surfW" name="res_surfW[]" class="select">
									<?for($i=0;$i<=20;$i++){?>
										<option value="<?=$i?>"><?=$i?></option>
									<?}?>
									</select>명
								</td>
								<td>
									<select id="res_rent" name="res_rent[]" class="select" onchange="fnSolSurfRentSel(this);">
										<option value='N'>렌탈미신청</option>
										<option value='보드,슈트'>보드,슈트</option>
										<option value='보드'>보드</option>
										<option value='슈트'>슈트</option>
									</select>
								</td>
								<td>
									<select id="res_rentM" name="res_rentM[]" class="select">
									<?for($i=0;$i<=20;$i++){?>
										<option value="<?=$i?>"><?=$i?></option>
									<?}?>
									</select>명
								</td>
								<td>
									<select id="res_rentW" name="res_rentW[]" class="select">
									<?for($i=0;$i<=20;$i++){?>
										<option value="<?=$i?>"><?=$i?></option>
									<?}?>
									</select>명
								</td>
								<td style="text-align:center;"><input type="button" class="btnsurfdel" style="width:40px;" value="삭제" onclick="fnSolDel(this);" ></td>
							</tr>
                        </tbody>
					</table>
                </td>
            </tr>
            <tr>
                <th>요청사항</th>
                <td colspan="5"><textarea id="memo" name="memo" rows="5" style="width: 60%; resize:none;"></textarea></td>
			</tr>
			<tr>
                <th>직원메모</th>
                <td colspan="5"><textarea id="memo2" name="memo2" rows="5" style="width: 60%; resize:none;"></textarea></td>
			</tr>
            <tr>
				<td class="col-02" style="text-align:center;" colspan="6">
                    <input type="hidden" id="resparam" name="resparam" size="10" value="soladd" class="itx">
                    <input type="hidden" id="resseq" name="resseq" size="10" value="" class="itx">
					<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="등록" onclick="fnSolDataAdd('soladd');" id="SolAdd"/>
					<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;display:none;" value="수정" onclick="fnSolDataAdd('modify');" id="SolModify" />&nbsp;
					<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="닫기" onclick="fnModifyClose();fnSolpopupReset();" />
                </td>
            </tr>
        </tbody>
    </table>
    </form>
</div>

<script>
$j(document).ready(function(){
	fnSearchAdminSolList("");
});
</script>