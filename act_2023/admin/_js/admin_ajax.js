$j(function () {
	
});

//예약 중복체크, 명단표 생성 로직
function fnChkRev(objList) {

	//노선, 이름, 연락처, 이용일(인원), 임시, 확정, 처리

	var prod_pkg = "";
	var tel = "";

	objList.forEach(function(el) {
        
        prod_pkg = $j.trim(el.prod_pkg)


        if(prod_pkg == "서울 사당 - 양양 (편도/ 토요일, 일요일)"){
            el.bustypetext = "양양행(사당선)";
			el.bustypevalue = 1;	//출발
			el.usedate = el.bus_date
        }else if(prod_pkg == "서울 종로 - 양양 (편도/토요일)"){
            el.bustypetext = "양양행(종로선)";
			el.bustypevalue = 1;	//출발
			el.usedate = el.bus_date
        }else if(prod_pkg == "[15시] 양양 - 서울  (편도)"){
            el.bustypetext = "[15시] 양양>서울";
			el.bustypevalue = 2;	//복귀
			el.usedate = el.bus_date
        }else if(prod_pkg == "[17시] 양양 - 서울 (편도/ 토요일, 일요일)"){
            el.bustypetext = "[17] 양양>서울";
			el.bustypevalue = 2;	//복귀
			el.usedate = el.bus_date
        }

		el.resbusseat2 = el.ea.replace("인원 x ", "").replace("인원수 x ", "");
        el.username = el.user_fullname.replace(/ /gi, "");

        tel = el.user_tel.replace("+82-", "").replace("82-", "");
        if(tel.substring(0, 1) != "0"){
            if(tel.length == 8){
                el.usertel = "010" + tel;
            }else{
                el.usertel = "0" + tel;
            }
        } 

    });
	

	$j.ajax({
		type: "POST",
		url: "/act_2023/admin/bus/list_ajax.php",
		data: {data:objList},
		dataType:"json",
		success: function (data) {
			
			console.log(data);
			fnMakeRevTable(data);

		}
	});

	
}

//채널별 예약목록 html 테이블생성
function fnMakeRevTable(revList) {
	
	var tbHtml = "";
	var i = 1;
	
	revList.forEach(function(el){
		tbHtml = "<tr>"
		tbHtml += " <td>" + i + "</td>"
		tbHtml += " <td>" + el.bustypetext + "</td>"
		tbHtml += " <td>" + el.username + "</td>"
		tbHtml += " <td>" + el.usertel + "</td>"
		tbHtml += " <td>" + el.usedate + " (" + el.resbusseat2 + "명)</td>"
		tbHtml += " <td></td>"
		tbHtml += " <td></td>"
		tbHtml += " <td></td>"
		tbHtml += "</tr>";
		$j("#tbCopyList").append(tbHtml);
		i++;
	});
	
	

}