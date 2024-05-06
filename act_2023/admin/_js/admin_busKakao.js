//타채널 알림톡 발송
function fnResKakaoAdmin(){
    if($j("#username").val() == ""){
        alert("이름을 입력하세요.");
        return;
    }
    
    if($j("#userphone").val() == ""){
        alert("연락처를 입력하세요.");
        return;
    }

    if(!confirm("알림톡 발송을 하시겠습니까?")){
        return;
    }

    var params = $j("#frmResKakao").serializeArray();
    $j.ajax({
        type: "POST",
        url: "/act_2023/admin/busKakao/list_save.php",
        data: params,
        success: function (data) {
            if(data == "err"){
                alert("오류가 발생하였습니다.");
            }else{
                $j("#userphone").val("");
                $j("#username").val("");
                
                if($j("#datareset").val() == "0"){

                }else if($j("#datareset").val() == "1"){
                    $j("#start_day").val("");
                    $j("#return_day").val("");
                    $j("#start_cnt").val(0);
                    $j("#return_cnt").val(0);
                }else{
                    $j("#start_cnt").val(0);
                    $j("#return_cnt").val(0);
                }

                fnSearchAdmin('busKakao/list_search_channel.php', '#mngKakaoSearch', 'N');
            }
        }
    });
}

//타채널 예약건 재발송
function fnBusChannelKakao(resnum){
    if(!confirm("재발송 하시겠습니까?")){
        return;
    }

    var params = "resparam=reskakaode2&resnum=" + resnum;
    $j.ajax({
        type: "POST",
        url: "/act_2023/admin/busKakao/list_save.php",
        data: params,
        success: function (data) {
            if(data == "err"){
                alert("오류가 발생하였습니다.");
            }else{
                fnSearchAdmin('busKakao/list_search_channel.php', '#mngKakaoSearch', 'N');
            }
        }
    });
}

//타채널 예약건 삭제
function fnBusChannelDel(seq){
    if(!confirm("삭제 하시겠습니까?")){
        return;
    }

    var params = "resparam=reskakaodel&codeseq=" + seq;
    $j.ajax({
        type: "POST",
        url: "/act_2023/admin/busKakao/list_save.php",
        data: params,
        success: function (data) {
            if(data == "err"){
                alert("오류가 발생하였습니다.");
            }else{
                fnSearchAdmin('busKakao/list_search_channel.php', '#mngKakaoSearch', 'N');
            }
        }
    });
}

//편도, 왕복 구분
function fnAdminBusGubun(obj, type){
    if(type == 1){
        $j("#start_day").val("");
        $j("#return_day").val("");
        $j("#start_cnt").val(0);
        $j("#return_cnt").val(0);
    }else{
        
    }
}

//======== 데이터 맵핑 =============
function fnseatcheck(obj, type){
    if($j("#busgubun").val() != 3){ //1박 왕복, 당일 왕복
        if(type == 1){
            $j("#return_cnt").val($j("#start_cnt").val());
        }else{
            //$j("#start_cnt").val($j("#return_cnt").val());
        }

    }
}

//노선 선택
function fnChannel(obj){
    $j("#resbus option").show();

    if(obj.value == "17" || obj.value == "20"){ //양양
        $j("#resbus option").eq(1).hide();
        $j("#resbus").val("YY");
    }else if(obj.value == "21" || obj.value == "22" || obj.value == "23"){ //동해
        $j("#resbus option").eq(0).hide();
        $j("#resbus").val("DH");
    }
}

//클룩, 프립 데이터 맵핑
function fnGetJson(obj){
    var vlu = $j("#reschannel").val();
    if($j("#html_1").val() == ""){
        alert("데이터 맵핑 자료를 넣으세요.");
        return;
    }

    if(!confirm("맵핑 후 알림톡 전송을 하시겠습니까?")){
        return;
    }

	if (vlu == "11" || vlu == "17" || vlu == "20" || vlu == "21" || vlu == "22"){
        fnMakeJsonFrip(); //프립
    }else if(vlu == "16"){
        fnMakeJsonKlook(); //클룩
	} else if (vlu == "7") {
		fnMakeJsonNaver(); //네이버쇼핑
	}
	
}

//프립 데이터 맵핑
function fnMakeJsonFrip() {
    //복사된 html을 가공 table[class='el-table__body']
    var strHtml = $j("#html_1").val().replace(/<!---->/gi,"");
    //$j("#divCopy").html($j("#divCopy").find("table[class='el-table__body']").html());

    //Json 인스턴스
    var objList = new Array();
    var objValue = new Object();

    var objTitle = "";          //상품명
    var objDate = "";           //일정
    var objbustypetext = ""     //버스상품명
    var objBustypevalue = ""    //버스상품타입 Y:양양행,S:동해행

    var strTitle = strHtml.substring(strHtml.indexOf('mx-4 text-sm') + 14, strHtml.lastIndexOf('row-padding'));
    $j("#divCopy").html(strTitle.substring(0, strTitle.lastIndexOf('</div>') + 6));
    
    //일정 상세정보 : 일정, 상품
    $j("#divCopy .row-padding").each(function(){
        if($j(this).find(".col-md-2").text() == "상품"){
            objTitle = $j(this).find(".col-md-2").next().text();
        }
        if($j(this).find(".col-md-2").text() == "일정"){
            objDate = $j(this).find(".col-md-2").next().find("span").find("span").eq(0).text().substring(0,10);
        }
    });
    
    $j("#divCopy").html(strHtml.substring(strHtml.indexOf('<table'), strHtml.lastIndexOf('</table>') + 8));

    //html 생성
    var objResbusseat2 = 1 //인원수
    $j("#divCopy .el-table__row").each(function(){

        objValue = new Object();

        objValue.title = objTitle;
        if(objTitle.indexOf("서프팩토리") > 0){ // 서프팩토리
            objbustypetext = "동해행(서팩)"
            objBustypevalue = "D"
        }else if(objTitle.indexOf("마린서프") > 0){ // 마린서프
            objbustypetext = "양양행(마린)"
            objBustypevalue = "Y"

        }else if(objTitle.indexOf("인구서프") > 0){ // 인구서프
            objbustypetext = "양양행(인구)"
            objBustypevalue = "Y"

        }else if(objTitle.indexOf("1박2일") > 0){ // 힐링서프
            objbustypetext = "동해행(힐링서프)"
            objBustypevalue = "D"
        }else if(objTitle.indexOf("서울→양양") > 0){ // 서울>양양
            objbustypetext = "서울>양양"
            objBustypevalue = "Y"
            objBustypevalue = "D"
        }else if(objTitle.indexOf("양양→서울") > 0){ // 양양>서울
            objbustypetext = "양양>서울"
            objBustypevalue = "S"
            objBustypevalue = "D"
        }else if(objTitle.indexOf("서울→동해(금진,대진)") > 0){ // 서울>동해
            objbustypetext = "서울>동해"
            objBustypevalue = "D"
        }else if(objTitle.indexOf("동해(금진,대진)→서울") > 0){ // 동해>서울
            objbustypetext = "동해>서울행"
            objBustypevalue = "S"
        }

        objValue.name = $j(this).find("td").eq(1).find(".cell").text(); //이름
        objValue.genser = $j(this).find("td").eq(2).find(".cell").text(); //성별
        objValue.usertel = $j(this).find("td").eq(3).find(".cell").text(); //연락처
        objValue.item = $j(this).find("td").eq(4).find(".cell").text(); //아이템명

        var objinfo = $j(this).find("td").eq(5).find(".cell span"); //추가정보    
        var addinfo = "";
        for (i = 0; i < objinfo.length; i++) {
            addinfo += objinfo[i].innerText + "|";
            
        }
        objValue.addinfo = addinfo;

        objValue.state = $j(this).find("td").eq(6).find(".cell").text(); //예약상태
        
        objValue["bus_date"] = objDate;             //이용일
        objValue["username"] = objValue.name;       //고객명
        objValue["usertel"] = objValue.usertel;     //고객 연락처
        objValue["usedate"] = objDate;              //사용일
        objValue["bustypetext"] = objbustypetext;   //버스상품명
        objValue["bustypevalue"] = objBustypevalue; //버스상품타입 Y:양양행,S:서울행,D:동해행
        objValue["etc1"] = "";                      //임시데이터 유무
        objValue["etc2"] = "";                      //확정데이터 유무
        objValue["etc3"] = "";                      //처리

        if ($j(this).find("button").length > 0) {
            objValue.btn = $j(this).find("button").text(); //액션

            if (objList.length <= 0) {
                objValue["resbusseat2"] = 1;                //인원수
                objList.push(objValue);
            }
            else{
                //alert(objList[objList.length-1].usertel);

                //인원수 병합
                if (objList[objList.length-1].usertel == objValue.usertel) {
                    objList[objList.length-1].resbusseat2 = Number(objList[objList.length-1].resbusseat2) + 1;
                }
                else{
                    objValue["resbusseat2"] = 1;                //인원수
                    objList.push(objValue);
                }

            }
        }
        else{
            objValue.btn = 'none'; //액션
        }
        
    });

    

    $j("#html_2").val(JSON.stringify(objList));

    /*
    //당일치기
    //[{"title":"[동해] 에메랄드빛 바다에서 당일치기 서핑해요! #서프팩토리","name":"망두1004","genser":" - ","tel":"01033657826","item":"[얼리버드] (여) 강습(보드+슈트) + 왕복셔틀","addinfo":"셔틀버스 노선 : 사당선 (셔틀버스 추가 배차될 경우 종로선 운행합니다)|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[동해] 에메랄드빛 바다에서 당일치기 서핑해요! #서프팩토리","name":"Nietzsche","genser":" 여성 ","tel":"01042077240","item":"[얼리버드] (여) 강습(보드+슈트) + 왕복셔틀","addinfo":"셔틀버스 노선 : 사당선 (셔틀버스 추가 배차될 경우 종로선 운행합니다)|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[동해] 에메랄드빛 바다에서 당일치기 서핑해요! #서프팩토리","name":"박선영","genser":" 여성 ","tel":"01033749239","item":"[얼리버드] (여) 강습(보드+슈트) + 왕복셔틀","addinfo":"셔틀버스 노선 : 사당선 (셔틀버스 추가 배차될 경우 종로선 운행합니다)|","state":"예약 대기","btn":" 예약건 취소 처리 "}]


    //힐링 캠프
    //[{"title":"[동해] 힐링 서핑캠프 #동거동락 #서핑트립 #1박2일 #MT","name":"욤희","genser":" - ","tel":"01037479816","item":"[얼리버드] 남/여|서핑강습(1회)+숙박+바베큐+왕복교통","addinfo":"이름을 입력해주세요 . : 한나영|성별이 어떻게 되시나요? : 여성|일요일 서핑강습 여부 : 미참여 (자유일정)|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[동해] 힐링 서핑캠프 #동거동락 #서핑트립 #1박2일 #MT","name":"영숙22","genser":" 여성 ","tel":"01027768162","item":"[얼리버드] 남/여|서핑강습(1회)+숙박+바베큐+왕복교통","addinfo":"이름을 입력해주세요 . : 허아현|성별이 어떻게 되시나요? : 여성|일요일 서핑강습 여부 : 미참여 (자유일정)|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[동해] 힐링 서핑캠프 #동거동락 #서핑트립 #1박2일 #MT","name":"권가은","genser":" 여성 ","tel":"01090281451","item":"[얼리버드] 남/여|서핑강습(1회)+숙박+바베큐+왕복교통","addinfo":"성별이 어떻게 되시나요? : 여성|일요일 서핑강습 여부 : 미참여|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[동해] 힐링 서핑캠프 #동거동락 #서핑트립 #1박2일 #MT","name":"연이2","genser":" 여성 ","tel":"01075539495","item":"[얼리버드] 남/여|서핑강습(1회)+숙박+바베큐+왕복교통","addinfo":"성별이 어떻게 되시나요? : 여성|일요일 서핑강습 여부 : 미참여|","state":"예약 대기","btn":" 예약건 취소 처리 "}]

    //셔틀버스
    //[{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"프립대원","genser":" 남성 ","tel":"01099850475","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"프립대원","genser":" 남성 ","tel":"01099850475","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"별님❤","genser":" 여성 ","tel":"01092942064","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"별님❤","genser":" 여성 ","tel":"01092942064","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"예약 대기","btn":" 예약건 취소 처리 "},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"김윤희","genser":" 여성 ","tel":"01047495581","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"취소 완료","btn":"none"},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"김윤희","genser":" 여성 ","tel":"01047495581","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"취소 완료","btn":"none"},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"슈붕슈붕","genser":" 여성 ","tel":"01022481787","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"취소 완료","btn":"none"},{"title":"[프립셔틀ㅣ서울→양양] 프립셔틀 타고 양양 놀러갈사람?!","name":"슈붕슈붕","genser":" 여성 ","tel":"01022481787","item":"서울 > 양양","addinfo":"해당 상품은 버스만 제공되는 셔틀버스입니다. : 네! 확인했습니다.|좌석/정류장 안내 카카오톡은 이용일 3~4일 전에 카카오톡으로 발송됩니다. : 네! 확인했습니다.|","state":"취소 완료","btn":"none"}]
    */

    console.log(objList);

    //예약 중복체크, 명단표 생성 로직


    if ($j("#tbCopyList tr").length == 0) {
        $j("#tbCopyList").html($j("#tbCopyList2").html());  //맵핑 표 초기화
    }
    
    fnChkRev("Frip",objList);

}

//클룩 데이터 맵핑
function fnMakeJsonKlook() {
    $j("#divCopy").html($j("#html_1").val());
    $j("#divCopy").html($j("#divCopy").find(".booking-list-result").html());

    var $state = "";    //예약 상태영역
    var $info = "";     //예약 상세영역

    //Json 인스턴스
    var objList = new Array();
    var objValue = new Object();
    var colKey = "";
    var colValue = "";
    
    var colNameTitle = {
        "상품명":"prod_name",
        "패키지명":"prod_pkg",
        "단위":"ea",
        "이용시간":"bus_date",
        "전화번호":"user_tel",
        //"전화번호_2":"user_tel",
        "성":"user_name1",
        "이름":"user_name2",
        "성명":"user_fullname"
    }

    $j("#divCopy").find(".booking-item").each(function(){

        var objValue = new Object();

        //#region 예약 상태정보
        $state = $j(this).find(".boooking-general-info-operation");

        //예약확인ID
        objValue.res_id = $state.find(".info-item").eq(0).find("span").eq(1).text();
        //예약시간
        //objValue.res_time = $state.find(".info-item").eq(1).text().split(":")[1].trim().substring(0,10);
        //예약상태
        objValue.state = $state.find(".info-item").eq(2).find(".ant-tag").eq(0).text();

        //예약색상
        objValue.rgb = $state.find(".booking-color-tag").css("background-color");
        //#regionend

        /******************************************************************************/

        //#region 예약 상세정보
        $info = $j(this).find(".booking-info");

        $info.find("ul").each(function(){
            $j(this).find("li").each(function(){

                colKey = $j(this).find("p").eq(0).text().replace(":","").replace(/ /g, ''); //json Text
                colValue = $j(this).find("p").eq(1).text(); //json Value

                // if (objValue.user_tel_sub != undefined && colKey == "전화번호") {
                //     colKey = "전화번호_2";
                // }

                if (colNameTitle[colKey] != undefined) {
                    if(colNameTitle[colKey]  == "prod_name"){
                        if(colValue == "서울 - 양양 편도 or 왕복 서핑버스 (서피비치)"){
                            
                        }else if(colValue == "강원도 양양 셔틀버스 + 서핑 강습"){
                            
                        }else if(colValue == "양양 서핑 셔틀버스 + 강습"){
                            
                        }
                    }

                    if(objValue[colNameTitle[colKey]] == null){
                        objValue[colNameTitle[colKey]] = colValue;
                    }
                }
                
            });
        });

        objValue["username"] = "";      //고객명
        objValue["usertel"] = "";       //고객 연락처
        objValue["resbusseat2"] = "";   //인원수
        objValue["usedate"] = "";       //사용일
        objValue["bustypetext"] = "";   //버스상품명
        objValue["bustypevalue"] = "";  //버스상품타입 Y:양양행,S:동해행
        objValue["etc1"] = "";          //임시데이터 유무
        objValue["etc2"] = "";          //확정데이터 유무
        objValue["etc3"] = "";          //처리

        //#regionend

        objList.push(objValue);

    });

    $j("#html_2").val(JSON.stringify(objList));

    var i = 1;
	$j("#tbCopyList").html($j("#tbCopyList2").html());


    //예약 중복체크, 명단표 생성 로직
    fnChkRev("Klook",objList);
    return;

    objList.forEach(function(el) {
        var busGubun = "";
        var prod_pkg = $j.trim(el.prod_pkg)

        var resDate1 = "";
        var resDate2 = "";
        var resbusseat1 = "0";
		var resbusseat2 = "0";
		

        if(prod_pkg == "서울 사당 - 양양 (편도/ 토요일, 일요일)"){
            busGubun = "양양행(사당선)";
            resDate1 = el.bus_date;
            resbusseat1 = el.ea.replace("인원 x ", "").replace("인원수 x ", "");
        }else if(prod_pkg == "서울 종로 - 양양 (편도/토요일)"){
            busGubun = "양양행(종로선)";
            resDate1 = el.bus_date;
            resbusseat1 = el.ea.replace("인원 x ", "").replace("인원수 x ", "");
        }else if(prod_pkg == "[15시] 양양 - 서울  (편도)"){
            busGubun = "[15시] 양양>서울";
            resDate2 = el.bus_date;
            resbusseat2 = el.ea.replace("인원 x ", "").replace("인원수 x ", "");
        }else if(prod_pkg == "[17시] 양양 - 서울 (편도/ 토요일, 일요일)"){
            busGubun = "[17] 양양>서울";
            resDate2 = el.bus_date;
            resbusseat2 = el.ea.replace("인원 x ", "").replace("인원수 x ", "");
        }

        var user_name = el.user_fullname.replace(/ /gi, "");

        var tel = el.user_tel.replace("+82-", "").replace("82-", "");
        if(tel.substring(0, 1) != "0"){
            if(tel.length == 8){
                tel = "010" + tel;
            }else{
                tel = "0" + tel;
            }
        } 
        //tel = "01044370009";

        if(el.rgb == "rgb(255, 255, 255)"){
            var tbHtml = "<tr>"
                        + " <td>" + i + "</td>"
                        + " <td>" + busGubun + "</td>"
                        + " <td>" + user_name + "</td>"
                        + " <td>" + tel + "</td>"
                        + " <td>" + el.bus_date + " (" + el.ea.replace("인원 x ", "").replace("인원수 x ", "") + "명)</td>"
                        + " <td></td>"
						+ " <td></td>"
						+ " <td></td>"
                        + "</tr>";
            $j("#tbCopyList").append(tbHtml);
    
            i++;

            var resbus = "YY";
            var reschannel = "16";
			var params = "resparam=reskakao&username=" + user_name + "&resbus=" + resbus + "&userphone=" + tel + "&reschannel=" + reschannel + "&resDate1=" + resDate1 + "&resDate2=" + resDate2 + "&resbusseat1=" + resbusseat1 + "&resbusseat2=" + resbusseat2;

			/*알림톡 발송*/

            // $j.ajax({
            //     type: "POST",
            //     url: "/act_2023/admin/busKakao/list_save.php",
            //     data: params,
            //     success: function (data) {
            //         if(data == "err"){
            //             alert("오류가 발생하였습니다.");
            //         }
            //     }
            // });
        }
    });

    alert("처리 완료");

    fnSearchAdmin('busKakao/list_search_channel.php', '#mngKakaoSearch', 'N');

    // const result = objList.reduce((acc, v) => {
    //     return acc.includes(v) ? acc : [...acc, v];
    //   }, []);

    // console.log(result);

    // [{"res_id":"HCA813310","state":"확정됨","prod_name":"서울 - 양양 편도 or 왕복 서핑버스 (서피비치)","prod_pkg":"서울 사당 - 양양 (편도/ 토요일, 일요일) ","ea":"인원 x 1","bus_date":"2023-06-10","user_fullname":"한별 이","user_tel_sub":"82-01089199342","user_name1":"이","user_name2":"한별","user_tel":"+82-01089199342"}
    //,{"res_id":"ZMF207384","state":"취소됨","prod_name":"서울 - 양양 편도 or 왕복 서핑버스 (서피비치)","prod_pkg":"서울 사당 - 양양 (편도/ 토요일, 일요일) ","ea":"인원 x 2","bus_date":"2023-05-05","user_fullname":"박 세린","user_tel_sub":"***","user_name1":"박","user_name2":"세린","user_tel":"+82-01033836382"}
    //,{"res_id":"MAW511856","state":"취소됨","prod_name":"서울 - 양양 편도 or 왕복 서핑버스 (서피비치)","prod_pkg":"[17시] 양양 - 서울 (편도/ 토요일, 일요일)","ea":"인원수 x 2","bus_date":"2023-05-07","user_fullname":"박 세린","user_tel_sub":"***","user_name1":"박","user_name2":"세린","user_tel":"+82-01033836382"}
    //,{"res_id":"PRN058505","state":"확정됨","prod_name":"서울 - 양양 편도 or 왕복 서핑버스 (서피비치)","prod_pkg":"서울 사당 - 양양 (편도/ 토요일, 일요일) ","ea":"인원 x 2","bus_date":"2023-05-27","user_fullname":"-","user_tel_sub":"82-01049310092","user_tel":"+82-01049310092"}]
    console.log(objList);
}

//네이버쇼핑 맵핑
function fnMakeJsonNaver() {

	$j("#divCopy").html($j("#html_1").val());
	$j("#divCopy").html($j("#divCopy").find(".tui-grid-lside-area").html());

    alert("test??");

}



//예약 중복체크, 명단표 생성 로직
function fnChkRev(type, objList) {

	switch (type) {
		case "Frip": fnChkRev_Frip(objList);break;
		case "Klook": fnChkRev_Klook(objList);break;
	}

}

function fnChkRev_Frip(objList) {	
	//fnMakeRevTable("Frip",objList);

	$j.ajax({
		type: "POST",
		url: "/act_2023/admin/busKakao/list_ajax.php",
		data: {data:objList},
		dataType:"json",
		success: function (data) {
			console.log(data);
			fnMakeRevTable("Frip",data);
		}
	});

}

function fnChkRev_Klook(objList){

	//번호, 노선, 이름, 연락처, 이용일(인원), 임시, 확정, 처리

	var prod_pkg = "";
	var tel = "";

	objList.forEach(function(el) {
        
        prod_pkg = $j.trim(el.prod_pkg);

        if(prod_pkg == "서울 사당 - 양양 (편도/ 토요일, 일요일)"){
            el.bustypetext = "양양행(사당선)";
			el.bustypevalue = "Y";	//출발
			el.usedate = el.bus_date
        }else if(prod_pkg == "서울 종로 - 양양 (편도/토요일)"){
            el.bustypetext = "양양행(종로선)";
			el.bustypevalue = "Y";	//출발
			el.usedate = el.bus_date
        }else if(prod_pkg == "[15시] 양양 - 서울  (편도)"){
            el.bustypetext = "[15시] 양양>서울";
			el.bustypevalue = "S";	//복귀
			el.usedate = el.bus_date
        }else if(prod_pkg == "[17시] 양양 - 서울 (편도/ 토요일, 일요일)"){
            el.bustypetext = "[17] 양양>서울";
			el.bustypevalue = "S";	//복귀
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
		else{
			el.usertel = tel;
		}

    });
	
	$j.ajax({
		type: "POST",
		url: "/act_2023/admin/busKakao/list_ajax.php",
		data: {data:objList},
		dataType:"json",
		success: function (data) {
			console.log(data);
			fnMakeRevTable("Klook",data);
		}
	});

}

//채널별 예약목록 html 테이블생성
function fnMakeRevTable(type,revList) {
	
	var tbHtml = "";
	var i = 1;

	if (type == "Frip") {
		revList.forEach(function(el){
			//프립 확정 인원
			tbHtml = "<tr>"
			tbHtml += " <td>" + i + "/" + el.bus_date.substring(5) + "</td>"
			tbHtml += " <td>" + el.bustypetext + "</td>"
			tbHtml += " <td>" + el.username + "</td>"
			tbHtml += " <td>" + el.usertel + "</td>"
			tbHtml += " <td>" + el.usedate + " (" + el.resbusseat2 + "명)</td>"
			tbHtml += " <td>" + el.etc1 + "</td>"
			tbHtml += " <td>" + el.etc2 + "</td>"
			tbHtml += " <td>" + el.etc3 + "</td>"
			tbHtml += "</tr>";
			$j("#tbCopyList").append(tbHtml);
			i++;
		});	
		
	}
	else if (type == "Klook") {
		revList.forEach(function(el){
			//클룩 확정 인원
			if(el.rgb == "rgb(255, 255, 255)"){
				tbHtml = "<tr>"
				tbHtml += " <td>" + i + "</td>"
				tbHtml += " <td>" + el.bustypetext + "</td>"
				tbHtml += " <td>" + el.username + "</td>"
				tbHtml += " <td>" + el.usertel + "</td>"
				tbHtml += " <td>" + el.usedate + " (" + el.resbusseat2 + "명)</td>"
				tbHtml += " <td>" + el.etc1 + "</td>"
				tbHtml += " <td>" + el.etc2 + "</td>"
				tbHtml += " <td>" + el.etc3 + "</td>"
				tbHtml += "</tr>";
				$j("#tbCopyList").append(tbHtml);
				i++;
			}
		});		
	}
}
//======== 데이터 맵핑 =============