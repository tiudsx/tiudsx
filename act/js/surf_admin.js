//셀렉트 박스 상태 변경
function fnChangeModify(obj, confirmVlu){
	var trObj = $j(obj).parents('tr').prev();

	if(confirmVlu == $j(obj).val()){
		trObj.find("input[id=chkCancel]").prop("checked", false);
	}else{
		trObj.find("input[id=chkCancel]").prop("checked", true);
	}
}

//상태 변경처리 - 건당
function fnConfirmUpdate(num, obj){
	$j("#frmConfirmSel").html($j("#hidInitParam").html());

	var tbObj = $j(obj).parents('table');
	var chkObj = tbObj.find("input[id=chkCancel]");
	if(tbObj.find("input[id=chkCancel]:checked").length == 0){
		//alert("선택된 항목이 없습니다.");
		//return;
	}

	if(!confirm("저장 하시겠습니까?")){
		return;
	}

	var chkBox = '';
	for (var i = 0; i < chkObj.length; i++) {
		if(chkObj.eq(i).is(":checked")){
			chkBox += '<input type="checkbox" id="chkCancel" name="chkCancel[]" checked="checked" value="' + chkObj.eq(i).val() + '" />';
			chkBox += '<input type="text" id="selConfirm" name="selConfirm[]" value="' + tbObj.find("select[id=selConfirm]").eq(i).val() + '" />';
		}
	}
	chkBox += '<input type="text" id="MainNumber" name="MainNumber" value="' + tbObj.find("input[id=MainNumber]").val() + '" />';
	chkBox += '<textarea id="memo" name="memo">' + tbObj.find("textarea[id=memo]").val() + '</textarea>';

	$j("#frmConfirmSel").append(chkBox);

	var formData = $j("#frmConfirmSel").serializeArray();

	$j.post(folderBusRoot + "/Admin_SurfSave.php", formData,
		function(data, textStatus, jqXHR){
		   if(fnRtnText(data, 0)){
				location.reload();
		   }
		}).fail(function(jqXHR, textStatus, errorThrown){
	 
	});
}


//상태 변경처리 - 전체보기
function fnConfirmUpdateAll(num, obj){
	var calObj = $j("calBox[sel=yes]");

	$j("#frmConfirmSel").html($j("#hidInitParam").html());
	$j("#frmConfirmSel #userid").val(userid);

	var tbObj = $j(obj).parents('table');
	var chkObj = tbObj.find("input[id=chkCancel]");
	if(tbObj.find("input[id=chkCancel]:checked").length == 0){
		//alert("선택된 항목이 없습니다.");
		//return;
	}

	if(!confirm("저장 하시겠습니까?")){
		return;
	}

	var chkBox = '';
	for (var i = 0; i < chkObj.length; i++) {
		if(chkObj.eq(i).is(":checked")){
			chkBox += '<input type="checkbox" id="chkCancel" name="chkCancel[]" checked="checked" value="' + chkObj.eq(i).val() + '" />';
			chkBox += '<input type="text" id="selConfirm" name="selConfirm[]" value="' + tbObj.find("select[id=selConfirm]").eq(i).val() + '" />';
		}
	}
	chkBox += '<input type="text" id="MainNumber" name="MainNumber" value="' + tbObj.find("input[id=MainNumber]").val() + '" />';
	chkBox += '<textarea id="memo" name="memo">' + tbObj.find("textarea[id=memo]").val() + '</textarea>';

	$j("#frmConfirmSel").append(chkBox);

	var formData = $j("#frmConfirmSel").serializeArray();

	$j.post(folderBusRoot + "/Admin_SurfSave.php", formData,
		function(data, textStatus, jqXHR){
		   if(fnRtnText(data, 0)){
                fnSearchAdmin();

				if(calObj.attr("value") == null){
					fnCalMoveAdmin($j(".tour_calendar_month").text().replace('.', ''), 99);
				}else{
					fnCalMoveAdmin($j(".tour_calendar_month").text().replace('.', ''), calObj.attr("value").split('-')[2]);
				}
		   }
		}).fail(function(jqXHR, textStatus, errorThrown){
	 
	});
}

//달력 날짜 클릭
function fnPassengerAdmin(obj) {
	var selDate = obj.attributes.value.value;
    $j(".right_article3 calBox").css("background", "white");
    $j("calBox[sel=yes]").attr("sel", "no");
	$j(obj).css("background", "#efefef");
	$j(obj).attr("sel", "yes");

	$j("#sDate").val(selDate);
	$j("#eDate").val(selDate);

	$j("#schText").val('');

    $j("#divResList").load("/act/admin/Admin_SurfSearchList.php?selDate=" + selDate);

	$j("#initText2").css("display", "none");

	$j("input[id=chkResConfirm]").prop("checked", false);

	var arrGubun = $j(obj).attr("gubunchk").split(',');
	for (var i = 0; i < arrGubun.length; i++) {
		$j("input[id=chkResConfirm][value=" + arrGubun[i] + "]").prop('checked', true);
	}

	// fnSearchAdmin();
}


function fnCalMoveAdmin(selDate, day) {
	var nowDate = new Date();
	$j(".right_article3").load(folderBusRoot + "/Admin_SurfCalendar.php?selDate=" + selDate + "&selDay=" + day + "&t=" + nowDate.getTime());
	
	if(day == "0"){
		$j("#divResList").html("");
		$j("#initText2").css("display", "");
	}
}

function fnSearchAdmin(){
	var formData = $j("#frmSearch").serializeArray();

	$j.post(folderBusRoot + "/Admin_SurfSearch.php", formData,
		function(data, textStatus, jqXHR){
		   $j("#mngSearch").html(data);
		}).fail(function(jqXHR, textStatus, errorThrown){
	 
	});
}

// row 클릭
function fnListView(obj){
	if($j(obj).next().css("display") == "none"){
		$j("tr[name='btnTrList']").removeClass('selTr');
		$j(obj).addClass('selTr');

		$j("tr[name='btnTrList']").next().css("display", "none");
		$j(obj).next().css("display", "");
	}else{
		$j("tr[name='btnTrList']").removeClass('selTr');

		$j(obj).next().css("display", "none");
	}
}