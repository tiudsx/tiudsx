<?php 
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/logininfo.php';

?>

<link rel="stylesheet" type="text/css" href="/act/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/act/css/surfview.css">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_surf.css">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_common.css">

<script type="text/javascript" src="/act/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/act_2023/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/admin_sol.js?v=<?=time()?>"></script>

<div class="bd_tl" style="width:100%;">
	<h1 class="ngeb clear"><i class="bg_color"></i>솔게스트하우스 예약관리 </h1>
</div>

<script>
    var mobileuse = "";
</script>

<div class="container" id="contenttop" style="padding-top:35px;">

	<div id="containerTab">
		<ul class="tabs">
			<li class="active" rel="tab1">예약현황</li>
			<li rel="tab2">예약검색</li>
			<li id="click" onclick="fnSolRes()" style="background:#ff6666;color:white;">예약등록</li>
		</ul>
		
		<div class="tab_container">

<div id="res_modify" style="display:none;"> 
    <form name="frmModify" id="frmModify" autocomplete="off">
    <div class="gg_first" style="margin-top:0px;">예약정보 등록
	 <!-- (<span id="insdate"></span>) -->
	</div>
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
                        <option value='정태원'>정태원</option>
                        <option value='이승철'>이승철</option>
                        <option value='정태일'>정태일</option>
                    </select>
				</td>
                <th>예약자이름</th>
                <td><input type="text" id="user_name" name="user_name" size="15" value="" class="itx"></td>
                <th>연락처</th>
				<td>
					<input type="text" id="user_tel" name="user_tel" size="15" value="" class="itx">
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
						<option value='Y'>발송</option>				
						<option value='N'>미발송</option>
					</select> (등록일 경우만 발송)
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
							<col width="70" />
						</colgroup>
						<tbody>
							<tr>
                                <th colspan="5">숙박정보</th>
                                <th rowspan="2"><input type="button" class="btnsurfadd" style="width:40px;" value="추가" data-gubun="trstay"></th>
                            </tr>
                            <tr>
                                <th>
									숙소명
								</th>
                                <th>
									이용일
								</th>
                                <th>고객정보</th>
                                <th>성별</th>
								<th>
									바베큐
									<input type="button" class="btnsurfadd" style="width:60px; height:22px;" value="일괄적용" data-gubun="trbbq">
								</th>
							</tr>
							<tr id="trstay" style="display:none;">
								<td>
									<input type="hidden" id="stayseq" name="stayseq[]" >
									<input type="hidden" id="staytype" name="staytype[]" value="I">
									<input type="hidden" id="res_stayshop" name="res_stayshop[]" value="N">
									<input type="hidden" id="res_staysex" name="res_staysex[]" value="남">
									<input type="hidden" id="res_stayM" name="res_stayM[]" value="1"> 

									<label><input type="radio" name="res_stayshopChk" id="res_stayshopChk" value="N" onchange="fnSolStaySel(this);" checked>미신청</label> &nbsp;
									<label><input type="radio" name="res_stayshopChk" id="res_stayshopChk1" value="솔게스트하우스" onchange="fnSolStaySel(this);">솔게하</label>
								</td>
								<td style="line-height:2.3em">
									<input type="text" calid="res_staysdate" cal="sol_sdate" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7"> ~ 
									<input type="text" calid="res_stayedate" cal="sol_edate" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7">

									<input type="hidden" id="res_staysdate" name="res_staysdate[]" value="">
									<input type="hidden" id="res_stayedate" name="res_stayedate[]" value="">
									<input type="hidden" id="res_bbqdate" name="res_bbqdate[]" value="">
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
									<label><input type="radio" name="res_staysexChk" id="res_staysexChk" onchange="fnSolSexSel(this);" value="남" checked>남</label> &nbsp;
									<label><input type="radio" name="res_staysexChk" id="res_staysexChk1" onchange="fnSolSexSel(this);" value="여">여</label>
								</td>
								<td>
									<input type="text" calid="res_bbqdate" cal="date" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7">
									<input type="button" class="btnsurfdel" style="width:20px;height:20px;" value="X" onclick="fnSolDateDel(this);" >
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
                                <th rowspan="2"><input type="button" class="btnsurfadd" style="width:40px;" value="추가" data-gubun="trsurf" ></th>
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
										<option value='서프팩토리'>서프팩토리</option>
										<option value='서퍼랑'>서퍼랑</option>
										<option value='솔서프'>솔서프</option>
										<option value='라라서프'>라라서프</option>
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
            <tr style="display:none;">
                <th>요청사항</th>
                <td colspan="5"><textarea id="memo" name="memo" rows="5" style="width: 60%; resize:none;"></textarea></td>
			</tr>
			<tr>
                <th>직원메모</th>
                <td colspan="5"><textarea id="memo2" name="memo2" rows="5" style="width: 60%; resize:none;"></textarea></td>
			</tr>
            <tr>
				<td class="col-02" style="text-align:center;" colspan="5">
                    <input type="hidden" id="resparam" name="resparam" size="10" value="soladd" class="itx">
                    <input type="hidden" id="resseq" name="resseq" size="10" value="" class="itx">
					<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="등록" onclick="fnSolDataAdd('soladd');" id="SolAdd"/>
					<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;display:none;" value="수정" onclick="fnSolDataAdd('modify');" id="SolModify" />&nbsp;
					<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="닫기" onclick="fnSolInsert();fnSolpopupReset();" />
                </td>
				<td style="text-align:center;">
					<input type="button" class="btnsurfadd" style="width:120px; height:40px;display:none;" id="SolDel" value="삭제" onclick="fnSolDel();" />
				</td>
            </tr>
        </tbody>
    </table>
    </form>
</div>
			<!-- #tab1 -->
			<div id="tab1" class="tab_content">
				<section>
					<aside id="right_article3" class="left_article5">
						<?include '_calendar.php'?>
					</aside>
					<article class="right_article5">
						<div class="gg_first">객실 예약현황 (<span id="roomdate"></span>)</div>
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
									<th colspan="4">301호(6명)</th>
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
											<!-- <td><?=$i + 6?>번(<?=(($i % 2) == 1) ? "1" : "2"?>층)</td> -->
											<td></td>
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

<div id="res_modify_2" style="display:none;padding:5px;height: 500px;overflow-y: scroll;"> 
    <form id="frmModify_2" autocomplete="off">
    <div class="gg_first" style="margin-top:0px;">솔게스트하우스 예약내역</div>
    <table class="et_vars exForm bd_tb" style="width:100%;display:;" id="infomodify">
        <colgroup>
            <col width="8%" />
            <col width="12%" />
            <col width="8%" />
            <col width="12%" />
            <col width="8%" />
            <col width="12%" />
            <col width="8%" />
            <col width="12%" />
            <col width="8%" />
            <col width="12%" />
        </colgroup>
        <tbody>
			<tr>
				<th>등록관리자</th>
				<td><input type="text" id="res_adminname_2" size="10" class="itx" readonly></td>
                <th>예약자이름</th>
                <td><input type="text" id="user_name_2" size="15" class="itx" readonly></td>
                <th>연락처</th>
				<td><input type="text" id="user_tel_2" size="15"  class="itx" readonly></td>
				<th>예약처</th>
				<td><input type="text" id="res_company_2" size="10" class="itx" readonly></td>
				<th>예약상태</th>
				<td><input type="text" id="res_confirm_2" size="10" class="itx" readonly></td>
			</tr>
			<tr>
                <th>숙박</th>
                <td colspan="9">
					<table class="et_vars exForm bd_tb tbcenter" style="width:100%">
						<colgroup>
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
						</colgroup>
						<tbody>
							<tr>
                                <th colspan="5">숙박정보</th>
                            </tr>
                            <tr>
                                <th>숙소명</th>
                                <th>이용일</th>
                                <th>고객정보</th>
                                <th>성별</th>
                                <th>바베큐</th>
							</tr>
							<tr id="trstay_2" style="display:none;">
								<td>
									<input type="text" id="res_stayshop_2" size="12" class="itx" readonly>
								</td>
								<td style="line-height:2.3em">
									<input type="text" calid="res_staysdate_2" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" disabled> ~ 
									<input type="text" calid="res_stayedate_2" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" disabled>
								</td>
								<td>
									<input type="text" id="res_stayroom_2" size="3" class="itx" readonly>
									<input type="text" id="res_staynum_2" size="7" class="itx" readonly>
								</td>
								<td>
									<input type="text" id="res_staysex_2" size="3" class="itx" readonly>
								</td>
								<td>
									<input type="text" calid="res_bbqdate_2" readonly="readonly" style="width:66px;" class="itx2" maxlength="7" disabled>
								</td>
							</tr>
                        </tbody>
					</table>
                </td>
			</tr>
            <tr>
                <th>서핑강습</th>
                <td colspan="9">
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
						</colgroup>
                        <tbody>
                            <tr>
                                <th rowspan="2">서핑샵</th>
                                <th rowspan="2">이용일</th>
                                <th colspan="3">서핑강습</th>
                                <th colspan="3">장비렌탈</th>
                            </tr>
                            <tr>
                                <th>시간</th>
                                <th>남</th>
                                <th>여</th>
                                <th>종류</th>
                                <th>남</th>
                                <th>여</th>
							</tr>
							<tr id="trsurf_2" style="display:none;">
								<td><input type="text" id="res_surfshop_2" size="12" class="itx" readonly></td>
								<td>
									<input type="text" calid="res_surfdate_2" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
								</td>
								<td><input type="text" id="res_surftime_2" size="10" class="itx" readonly></td>
								<td><input type="text" id="res_surfM_2" size="2" class="itx" readonly>명</td>
								<td><input type="text" id="res_surfM_2" size="2" class="itx" readonly>명</td>
								<td><input type="text" id="res_rent_2" size="10" class="itx" readonly></td>
								<td><input type="text" id="res_rentM_2" size="2" class="itx" readonly>명</td>
								<td><input type="text" id="res_rentW_2" size="2" class="itx" readonly>명</td>
							</tr>
                        </tbody>
					</table>
                </td>
            </tr>
			<tr>
                <th>직원메모</th>
                <td colspan="9"><textarea id="memo2_2" rows="5" style="width: 60%; resize:none;" readonly></textarea></td>
			</tr>
            <tr>
				<td class="col-02" style="text-align:center;" colspan="10">
					<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="닫기" onclick="$j.unblockUI();" />
                </td>
            </tr>
        </tbody>
    </table>
    </form>
</div>

<script>
$j(document).ready(function(){
	fnSearchAdminListSol("");
});
</script>