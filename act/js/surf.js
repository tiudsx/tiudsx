$j(document).ready(function() {
    //지역 카테고리 - 토글
    $j(".btnArea").toggle(function() {
        // $j(".listArea").css("display", 'block');
        $j(".listArea").slideDown("100");
    },
    function() {
        // $j(".listArea").css("display", "none");
        $j(".listArea").slideUp("100");
    });
    
});

function fnMapView(pointname){
    if (btnheight == "") btnheight = $j(".con_footer").height();
    if (type == "down") {
        $j(".con_footer").css("height", btnheight + "px");
        $j("#slide1").prop("src", "https://surfenjoy.cdn3.cafe24.com/button/btnMap.png");
        $j(".con_footer").css("background-color", "");
        $j(".resbottom").css("background-color", "");

        type = "";
    } else {
        $j(".con_footer").css("height", "100%");
        $j("#slide1").prop("src", "https://surfenjoy.cdn3.cafe24.com/button/btnMapx.png");
        $j(".resbottom").css("height", "100%");
        $j(".con_footer").css("background-color", "white");
        $j(".resbottom").css("background-color", "white");
        if(pointname != ""){
            var obj = $j("#ifrmMap").get(0);
            var objDoc = obj.contentWindow || obj.contentDocument;
            objDoc.mapMove(pointname);
        }
        
        type = "down";
    }
}

function maxLengthCheck(object) {
	if (object.value.length > object.maxLength) {
		object.value = object.value.slice(0, object.maxLength);
	}
}

function fnSaveErr(ojb){
	$j(divConfirm).unblock();
}